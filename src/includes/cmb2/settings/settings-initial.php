<?php
add_action('cmb2_admin_init', 'iande_settings');

function iande_settings()
{

    $args = [
        'id'           => 'iande_options_page',
        'title'        => __('Configurações', 'iande'),
        'object_types' => ['options-page'],
        'capability'   => 'manage_iande_options',
        'option_key'   => 'iande',
        'parent_slug'  => 'iande-main-menu',
        'tab_group'    => 'iande_tabs',
        'tab_title'    => __('Configurações', 'iande'),
        'save_button'  => esc_html__('Salvar opções', 'iande'),
    ];

    $iande_initial_options = new_cmb2_box($args);

    // Usar Isenção
    $iande_initial_options->add_field([
        'name' => __('Permitir solicitação de isenção?', 'iande'),
        'id'   => 'use_exemption_heading',
        'type' => 'title'
    ]);
    $link_email = \admin_url('admin.php?page=iande_emails_settings#email-exemption-heading');
    $iande_initial_options->add_field([
        'id'          => 'use_exemption',
        'description' => sprintf(__('Marcando essa opção, o museu aceitará solicitações para isenção dos ingressos nos agendamentos. Lembre-se de adicionar o anexo com o modelo de solicitação que será enviado por e-mail para o responsável pelo agendamento que solicitou isenção, <a href="%s">clicando aqui</a>.', 'iande'), esc_url($link_email)),
        'type'        => 'radio_inline',
        'default'     => 'no',
        'options'     => [
            'yes' => __('Sim', 'iande'),
            'no'  => __('Não', 'iande')
        ]
    ]);
}