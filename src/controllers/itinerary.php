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
        $this->render_component(__('Novo roteiro virtual', 'iande'),'iande-create-itinerary-page');
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
