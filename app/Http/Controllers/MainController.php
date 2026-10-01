<?php

/**
 * Tranquil Hideaways
 *
 * PHP version 8.2
 *
 * @abstract  Controlador Principal de Vistas
 *
 * @category  Production
 *
 * @author    Josué Gutiérrez <josuandroidg7@gmail.com>
 * @author    Mijail Cabrera <maccx668@gmail.com >
 * @copyright 2025
 */

namespace App\Http\Controllers;

use App\Helpers\ExcelFunc;
use App\Models\Form;
use App\Models\ParticipantCoupon;
use App\Models\Product;
use App\Repositories\ParticipantService;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

class MainController extends Controller
{
    protected $url;

    protected $tags;

    protected $ourDestTags;

    public function __construct(
        UrlGenerator $url,
        protected ParticipantService $participantService
    ) {
        $this->prev = $url->previous();
    }

    public function changeLocale($locale)
    {
        \Session::put('locale', $locale);

        return redirect($this->prev);
    }

    public function showError404()
    {
        return view('content.error-404');
    }

    public function showError500()
    {
        return view('content.error-500');
    }

    public function findLogout()
    {
        \Auth::logout();

        return redirect('home');
    }

    public function findDashboard()
    {
        $lists = config('nodes.available_nodes');
        $arrayOptions = [];
        foreach ($lists as $node) {
            $arrayOptions[] = [
                'label' => __('diesel.list.'.$node),
                'url' => url('node-list/'.$node),
            ];
        }

        return redirect('admin/node-list/participant-coupon');
    }

    public function getNodeList($node, $paginate = true, $excel = false)
    {
        $request = request();
        $availableNodes = config('nodes.available_nodes');
        $noRestricts = ['xt_with_image', 'xt_ci_number'];
        if (! in_array($node, $availableNodes)) {
            return redirect($this->prev);
        }
        $lang = \App::getLocale();
        $className = \Func::getModel($node);
        $class = with(new $className);
        $table = $class->getTable();
        $titleNode = $class->getLang($lang);
        $fields = \Func::getColumnsWithTimestamps($table, $lang);
        $items = $className::query();
        $filters = $request->all();
        foreach ($filters as $key => $value) {
            $key = str_replace('_from', '', $key);
            if (! \Schema::hasColumn($table, trim(strtolower($key))) && ! in_array($key, $noRestricts)) {
                continue;
            }
            if (empty($value)) {
                continue;
            }
            $type = \Str::contains($key, 'xt_') ? 'extra' : getTypeField($table, $key);
            $items = $items->when(\Str::contains($key, 'xt_with_image'), function ($q) use ($value) {
                $boolImage = filter_var($value, FILTER_VALIDATE_BOOLEAN);

                return $boolImage ? $q->whereNotNull('image') : $q->whereNull('image');
            })->when(\Str::contains($key, 'xt_ci_number'), function ($q) use ($value) {
                return $q->whereHas('participant', function ($q) use ($value) {
                    $q->where('ci_number', $value);
                });
            })->when(in_array($type, ['int', 'bigint', 'tinyint', 'enum']), function ($q) use ($key, $value) {
                return $q->where($key, $value);
            })->when(in_array($type, ['text', 'string', 'varchar']), function ($q) use ($key, $value) {
                $isUuid = preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $value);
                if ($isUuid) {
                    return $q->where($key, $value);
                }
                $query = 'TRIM( REPLACE( REPLACE(  REPLACE( REPLACE( REPLACE( LOWER( `'.$key."`), 'á', 'a' ), 'e', 'e' ), 'i', 'i' ), 'ó', 'o' ), 'u', 'u' ) ) LIKE '%".clean4search($value)."%'";

                return $q->whereRaw($query, []);
            })->when(in_array($type, ['datetime', 'date', 'timestamp']) && isset($filters[$key.'_from']) && isset($filters[$key.'_to']), function ($q) use ($key, $filters) {
                return $q->whereDate($key, '>=', $filters[$key.'_from'].' 00:00:00')->whereDate($key, '<=', $filters[$key.'_to'].' 23:59:00');
            });
        }
        $excel = $request->excel ?? 0;
        $items = $paginate && ! $excel
            ? $items->orderBy('created_at', 'DESC')->paginate(config('nodes.paginate'))->appends($request->all())
            : $items->orderBy('created_at', 'DESC')->get();
        if ($excel) {
            $filename = sprintf('%s-%s.xlsx', $table, time());
            $finalData = $items;
            if ($node == 'participant-coupon') {
                $finalData = [];
                foreach ($items as $q) {
                    $finalData[] = [
                        'ID' => $q->id,
                        'Participante' => $q->participant->name ?? '',
                        'CI' => $q->participant->ci_number ?? '',
                        'Teléfono' => $q->participant->cellphone ?? '',
                        'Lugar de Canje' => $q->participant->city_agency ?? '',
                        'Email' => $q->participant->email ?? '',
                        'Código' => $q->code,
                        'Premio' => $q->coupon->price->name ?? $q->price->name ?? 'SIN PREMIO',
                        'Fecha de registro' => $q->created_at->format('Y-m-d H:i:s'),
                        'Unidad de negocio' => $q->business_unit,
                    ];
                }
            }
            if ($items->count() == 0) {
                return redirect($this->prev)->with('message_error', __('table.empty_rows'));
            }

            return ExcelFunc::export(collect($finalData), $filename);
        }
        $result = [
            'title' => __('table.list.'.$node),
            'filters' => \Func::getFilters($table, $lang),
            'fields' => $fields,
            'items' => $items,
            'node_name' => $node,
        ];

        return view('livewire.pages.admin.table-crud', $result);
    }

    public function getNodeAction($node, $action, $id = null)
    {
        $request = request();
        $availableNodes = config('nodes.available_nodes');
        if (! in_array($node, $availableNodes)) {
            return redirect($this->prev);
        }
        if (! in_array($action, ['create', 'edit', 'read', 'delete'])) {
            return redirect($this->prev);
        }
        $lang = \App::getLocale();
        $className = \Func::getModel($node);
        $class = with(new $className);
        $table = $class->getTable();
        $titleNode = $class->getLang($lang);
        $fields = \Func::getColumns($table, $lang);
        $function = sprintf('%sItem', $action);
        /* Mandamos la acción */
        $evalItem = \Func::$function($class, $id);
        if (! $evalItem['process']) {
            return redirect($this->prev)->with('message_error', $evalItem['message']);
        }
        /* Contiene toda la INFO para poder dibujar un formulario y poblarlo en caso de editar o ver y eliminar */
        $result = [
            'title' => $titleNode,
            'fields' => $fields,
            'item' => $evalItem['item'],
            'message' => $evalItem['message'],
            'id' => $id,
            'action' => $action,
            'node' => $node,
        ];
        $redirect = $evalItem['redirect'] ?? false;
        if ($redirect) {
            return redirect('admin/node-list/'.$node)->with('message_success', $evalItem['message']);
        }

        return view('livewire.pages.admin.form-crud', $result);
    }

    public function findParticipate($ciNumber)
    {
        $participantCoupon = ParticipantCoupon::whereHas('participant', function ($q) use ($ciNumber) {
            $q->where('ci_number', $ciNumber);
        })
            ->where('status', 'created')
            ->first();
        echo 'Participante validado con éxito, vista en construcción!!';

        return;

        return view('content.home', compact('participantCoupon'));
    }

    public function showLanding()
    {
        return view('content.landing');
    }

    public function showLandingTicketDorado()
    {
        return view('content.landing-ticket-dorado');
    }

    public function showConsultarRaspadita(Request $request)
    {
        return view('content.consultar-raspadita');
    }

    public function showRegistraTusCompras(Request $request)
    {
        return view('content.registra-tus-compras');
    }

    public function showForm()
    {
        return view('content.form');
    }

    public function showLandingKernev()
    {
        return view('content.landing-kernev');
    }

    public function showElMundialSeViveEnCasa()
    {
        return view('content.el-mundial-se-vive-en-casa');
    }

    public function showFormKernev()
    {
        $products = Product::orderBy('order', 'asc')->get();

        return view('content.form-kernev', compact('products'));
    }

    public function showFormKernevTickets()
    {
        return view('content.form-kernev-tickets');
    }

    public function showFormKernevTicketsMundial()
    {
        return view('content.coupons-mundial');
    }

    public function findTestQueries()
    {
        // ASIGNACIÓN MANUAL DE PREMIO POR CASOS ESPECIALES
        // f566ebf8-423f-11f1-aa6b-0278441b5a53
        $idForm = 'f566ebf8-423f-11f1-aa6b-0278441b5a53'; // Colocar el ID del formulario aquí
        if ($idForm) {
            $form = Form::find($idForm);
            if ($form) {
                $prizeAllocation = $this->participantService->assignRandomEcommercePrize($form->id);
                $prizeId = $prizeAllocation['prize_id'] ?? null;
                if ($prizeId) {
                    $form->update([
                        'branch_id' => $prizeAllocation['branch_id'],
                        'prize_id' => $prizeId,
                    ]);
                    dd('Premio asignado con éxito: '.$prizeId);
                }
                dd('No hay premios disponibles para asignar.');
            }
            dd('Formulario no encontrado.');
        }
        dd('Finalizado');
    }
}
