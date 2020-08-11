<?php

namespace Iande;

use Controller;

class Appointment extends Controller
{
    /**
     * Renderiza a página de criação de reserva
     *
     * @param array $params
     * @return void
     */
    function view_create(array $params = [])
    {
        $this->render('create-appointment');
    }
}