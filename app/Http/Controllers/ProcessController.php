<?php

/**
 * Tranquil Hideaways
 *
 * PHP version 8.2
 *
 * @abstract  Controlador Principal de Formularios o peticiones POST
 *
 * @category  Production
 *
 * @author    Josué Gutiérrez <josuandroidg7@gmail.com>
 * @author    Mijail Cabrera <maccx668@gmail.com >
 * @copyright 2025
 */

namespace App\Http\Controllers;

use App\Interfaces\ParticipantServiceInteface;
use App\Models\Form;
use App\Models\MundialCoupon;
use App\Models\ParticipantCoupon;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

class ProcessController extends Controller
{
    protected $url;

    protected $paginate;

    protected $prev;

    public function __construct(
        UrlGenerator $url,
        private readonly ParticipantServiceInteface $participantService
    ) {
        $this->prev = $url->previous();
        $this->paginate = 60;
    }

    public function postNodeAction(Request $request)
    {
        $action = $request->action;
        $node = $request->node;
        if (! in_array($action, ['edit', 'create'])) {
            return redirect($this->prev);
        }
        $className = \Func::getModel($node);
        $rules = (new $className)::${'rules_'.$action};
        $validator = \Validator::make($request->all(), $rules);
        $redirect = '';
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            session()->flash('message_error', (__('table.empty_parameters') ?? trans('responses.error.default')));

            return redirect($this->prev)->with('message_error', __('table.empty_parameters'))->withErrors($validator->errors())->withInput();
        }
        $message = '';
        if ($action == 'create') {
            $item = new $className;
            $item->created_at = date('Y-m-d H:i:s');
            $item->save();
            $message = __('table.item_created');
            $redirect = url(sprintf('admin/node/%s/edit/%s', $node, $item->id));
        } elseif ($action == 'edit') {
            $item = $className::find($request->id);
            $message = __('table.item_edited');
            $redirect = url(sprintf('admin/node/%s/edit/%s', $node, $item->id));
        }
        $params = $request->all();
        unset($params['id'], $params['node'], $params['action'], $params['_token']);
        foreach ($params as $key => $value) {
            if (\Str::contains($key, 'image') && ! empty($request->hasFile($key)) && \Func::validateImageUrl($value, $node.'-'.$key, $value, $item->$key)) {
                $item->$key = \Asset::upload_image($request->file($key), $node.'-'.$key);
            } elseif (\Str::contains($key, 'file') && ! empty($request->hasFile($key)) && \Func::validateFileUrl($value, $node.'-'.$key, $value, $item->$key)) {
                $item->$key = \Asset::upload_file($request->file($key), $node.'-'.$key);
            } else {
                $item->$key = $value;
            }
        }
        $item->save();

        return redirect($redirect)->with('message_success', __('table.action_successfully'));
    }

    public function postValidateCoupon(Request $request)
    {
        $rules = ['ci_number' => 'required', 'control_code' => 'required'];
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('message_error', 'Hubo unos errores al validar la información enviada.')
                ->withErrors($validator->errors())
                ->withInput();
        }

        // Consultamos modelo Form filtrando por ci_number y asegurando relación estricta "sale"
        // donde valide también el control_code enviado
        $forms = Form::where('ci_number', $request->ci_number)
            ->has('sale') // Condición estricta que tenga un sale asociado
            ->whereHas('sale', function ($q) use ($request) {
                $q->where('control_code', $request->control_code);
            })
            ->where('business_unit', 'ECO')
            ->get();

        // Validamos existencia de resultados
        if ($forms->count() == 0) {
            return redirect()->back()
                ->with('message_error', 'No existen registros con los datos ingresados.')
                ->withInput();
        }

        // Retornamos view directo con resultados
        return view('content.consultar-raspadita-results', compact('forms'));
    }

    public function postUpdateCoupon(Request $request)
    {
        ParticipantCoupon::where('code', $request->coupon)->limit(1)->update(['status' => 'exchanged']);

        return ['status' => true, 'message' => 'Cupón actualizado con éxito.'];
    }

    public function postScratchForm(Request $request)
    {
        $id = $request->id;
        $form = Form::with('prize')->find($id);

        if ($form) {
            $form->is_scratched = true;
            $form->save();

            return response()->json([
                'success' => true,
                'prize_name' => $form->prize ? $form->prize->name : 'Premio',
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function postRegistraTusCompras(Request $request)
    {
        // Honeypot checker para bots
        if (! empty($request->website)) {
            return redirect()->back()->with('message_error', 'Se ha bloqueado la solicitud por motivos de seguridad.');
        }

        // Validación de datos
        $rules = [
            'name' => 'required|string|max:255',
            'ci_number' => 'required|string|max:50',
            'cellphone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'invoice_image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240', // Hasta 10MB
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('message_error', 'Hubo errores al procesar el formulario. Verifique sus datos y que la imagen sea válida.')
                ->withErrors($validator->errors())
                ->withInput();
        }

        // Guardar modelo Form
        $form = new Form;
        $form->name = $request->name;
        $form->ci_number = $request->ci_number;
        $form->cellphone = $request->cellphone;
        $form->city = $request->city;
        $form->status = 'registered';
        $form->business_unit = 'WEB';
        // Almacenar ruta de archivo públicamente
        if ($request->hasFile('invoice_image')) {
            $path = $request->file('invoice_image')->store('uploads/invoices', 'public');
            $form->invoice_image = $path;
        }

        $form->save();

        // Éxito
        return redirect()->back()->with('message_success', 'Tus datos fueron procesados. Nos comunicaremos contigo en un plazo menor a 24 horas para indicarte si los datos de tu formulario fueron validados con éxito.');
    }

    public function uploadKernevForm(Request $request)
    {
        $rules = ['name' => 'required', 'ci_number' => 'required', 'cellphone' => 'required', 'file' => 'required', 'product' => 'required', 'city' => 'required', 'type' => 'required'];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            session()->flash('message_error', 'Debe completar todos los campos para poder enviar el formulario.');

            return redirect($this->prev)->with('message_error', 'Debe completar todos los campos para poder enviar el formulario.')->withErrors($validator->errors())->withInput();
        }
        $this->participantService->createCouponsKernev($request);

        return redirect('landing-kernig')->with('message_success', 'Formulario registrado con éxito.');
    }

    public function checkCoupons(Request $request)
    {
        $ciNumber = $request->ci_number;
        $type = $request->type;
        if (! $ciNumber) {
            return ['status' => false, 'message' => 'No se encontraron cupones con los datos enviados.'];
        }
        $kernevFormCoupons = ParticipantCoupon::has('participant')->with('participant')->when($type == 'ci_number', function ($q) use ($ciNumber) {
            return $q->whereHas('participant', function ($q) use ($ciNumber) {
                $q->where('ci_number', $ciNumber);
            });
        })->when($type == 'code', function ($q) use ($ciNumber) {
            return $q->where('code', $ciNumber);
        })->get();
        if ($kernevFormCoupons->count() == 0) {
            return ['status' => false, 'message' => 'No se encontraron cupones con los datos enviados.'];
        }
        $kernevFormCoupons = $kernevFormCoupons->map(function ($coupon) {
            $coupon->date_formatted = $coupon->created_at->format('Y-m-d H:i:s');

            return $coupon;
        });

        return ['status' => true, 'message' => 'Cupones obtenidos exitósamente.', 'coupons' => $kernevFormCoupons];
    }

    public function checkCouponsMundial(Request $request)
    {
        $ciNumber = $request->ci_number;
        $type = $request->type;
        if (! $ciNumber) {
            return ['status' => false, 'message' => 'No se encontraron cupones con los datos enviados.'];
        }
        $kernevFormCoupons = MundialCoupon::has('participant')->with('participant')->when($type == 'ci_number', function ($q) use ($ciNumber) {
            return $q->whereHas('participant', function ($q) use ($ciNumber) {
                $q->where('ci_number', $ciNumber);
            });
        })->when($type == 'code', function ($q) use ($ciNumber) {
            return $q->where('code', $ciNumber);
        })->get();
        if ($kernevFormCoupons->count() == 0) {
            return ['status' => false, 'message' => 'No se encontraron cupones con los datos enviados.'];
        }
        $kernevFormCoupons = $kernevFormCoupons->map(function ($coupon) {
            $coupon->date_formatted = $coupon->created_at->format('Y-m-d H:i:s');

            return $coupon;
        });

        return ['status' => true, 'message' => 'Cupones obtenidos exitósamente.', 'coupons' => $kernevFormCoupons];
    }
}
