<?php

namespace IandePlugin;

use Controller;

class Itinerary extends Controller
{
    /**
     * Renderiza a página de criação de roteiro virtual
     *
     * @param array $params
     * @return void
     */
    function view_create(array $params = []) {
        $this->require_authentication();
        $this->render_vue(__('Novo roteiro virtual', 'iande'),'create-itinerary');
    }

    /**
     * Renderiza a página de edição de roteiro virtual
     *
     * @param array $params
     * @return void
     */
    function view_edit(array $params = [])
    {
        $this->require_authentication();
        $this->render('edit-itinerary');
    }
}
