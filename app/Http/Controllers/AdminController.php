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

use Illuminate\Routing\UrlGenerator;

class AdminController extends Controller
{
    protected $url;

    protected $tags;

    protected $prev;

    public function __construct(
        UrlGenerator $url
    ) {
        $this->prev = $url->previous();
    }

    public function listContact()
    {
        return view('admin.list-contact');
    }

    public function listAbout()
    {
        return view('admin.list-about');
    }

    public function syncProperties()
    {
        app(ProcessController::class)->sychronizeProperties();

        return redirect($this->prev)->with('message_success', 'Sincronización finalizada con éxito');
    }
}
