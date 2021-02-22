<?php

namespace IandePlugin;

use Controller;

class Itinerary extends Controller
{
    /**
     * Demo
     */
    function view_demo(array $params = [])
    {
        $this->require_authentication();
        $this->render('tainacan');
    }
}
