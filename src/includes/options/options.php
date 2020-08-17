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
        [
            'iande_general' => [
                'tab'   => 'iande_general',
                'title' => __('Section Example', 'iande'),
                'fields' => [
                    [
                        'id'          => 'field1',
                        'label'       => __('Field 1', 'iande'),
                        'type'        => 'text',
                        'default'     => 'Hello world',
                        'description' => __('Descrition Example', 'iande')
                    ]
                ]
            ],
            'iande_appointments' => [
                'tab'   => 'iande_appointments',
                'title' => __('Disponibilidade dos Agendamentos', 'iande'),
                'fields' => [
                    [
                        'id'          => 'duration',
                        'label'       => __('Duração', 'iande'),
                        'type'        => 'input',
                        'default'     => '30',
                        'description' => __('Duração em minutos para as visitas', 'iande'),
                        'attributes'  => [
                            'type' => 'number'
                        ]
                    ],
                    [
                        'id'          => 'group_size',
                        'label'       => __('Pessoas por grupo', 'iande'),
                        'type'        => 'input',
                        'description' => __('Número máximo de pessoas por grupo', 'iande'),
                        'attributes'  => [
                            'type' => 'number'
                        ]
                    ],
                    [
                        'id'          => 'group_slot',
                        'label'       => __('Grupos por horário', 'iande'),
                        'type'        => 'input',
                        'description' => __('Número máximo de grupos para cada horário', 'iande'),
                        'attributes'  => [
                            'type' => 'number'
                        ]
                    ],
                    /* @todo field schedules */
                ]
            ],
        ]
    );

}


\add_action('init', 'iande_plugin_options', 1);