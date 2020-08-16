<?php
require 'class-options.php';

function iande_plugin_options() {

    $iande_plugin_options = new Iande_Plugin_Options(
        'iande',
        __('Iandé', 'iande'),
        'manage_options'
    );
    $iande_plugin_options->set_tabs(
        array(
            array(
                'id' => 'iande_general',
                'title' => __('Configurações Gerais', 'iande'),
            ),
            array(
                'id' => 'iande_appointments',
                'title' => __('Agendamentos', 'iande')
            )
        )
    );
    $iande_plugin_options->set_fields(
        array(
            'iande_general' => array(
                'tab'   => 'iande_general',
                'title' => __('Section Example', 'iande'),
                'fields' => array(
                    array(
                        'id' => 'field1',
                        'label' => __('Field 1', 'iande'),
                        'type' => 'text',
                        'default' => 'Hello world',
                        'description' => __('Descrition Example', 'iande')
                    ),
                    array(
                        'id' => 'field2',
                        'label' => __('Field 2', 'iande'),
                        'type' => 'text'
                    )
                )
            ),
            'iande_appointments' => array(
                'tab'   => 'iande_appointments',
                'title' => __('Section Example', 'iande'),
                'fields' => array(
                    array(
                        'id' => 'field1',
                        'label' => __('Field 1', 'iande'),
                        'type' => 'text',
                        'default' => 'Hello world',
                        'description' => __('Descrition Example', 'iande')
                    ),
                    array(
                        'id' => 'field2',
                        'label' => __('Field 2', 'iande'),
                        'type' => 'text'
                    )
                )
            ),
        )
    );

}


\add_action('init', 'iande_plugin_options', 1);