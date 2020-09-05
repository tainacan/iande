<?php

add_action('cmb2_admin_init', 'iande_settings_emails');

function iande_settings_emails() {

    /**
     * Configurações dos E-mails
     */
    $args = [
        'id'           => 'iande_emails_options_page',
        'object_types' => ['options-page'],
        'parent_slug'  => 'iande',
        'tab_group'    => 'iande_tabs',
        'option_key'   => 'iande_emails_settings',
        'title'        => __('E-mails', 'iande'),
        'tab_title'    => __('E-mails', 'iande'),
        'save_button'  => __('Salvar opções', 'iande')
    ];

    $iande_emails_options = new_cmb2_box($args);

    $options = [
        'wpautop'       => true,
        'media_buttons' => false,
        'textarea_rows' => 10,
        'teeny'         => true,
        'dfw'           => false,
        'tinymce'       => false,
        'quicktags'     => true
    ];

    // E-mail 1.1 - Pré agendamento
    $iande_emails_options->add_field([
        'name'       => __('Pré agendamento', 'iande'),
        'id'         => 'email_pre_scheduling_heading',
        'type'       => 'title'
    ]);
    $iande_emails_options->add_field([
        'id'         => 'email_pre_scheduling_title',
        'show_names' => false,
        'type'       => 'text',
        'attributes' => [
            'placeholder' => __('Título do pré agendamento', 'iande')
        ]
    ]);
    $iande_emails_options->add_field([
        'id'         => 'email_pre_scheduling',
        'type'       => 'wysiwyg',
        'show_names' => false,
        'options'    => $options
    ]);

    // E-mail 1.2 - Pré agendamento + isenção
    $iande_emails_options->add_field([
        'name'       => __('Pré agendamento + isenção', 'iande'),
        'id'         => 'email_pre_scheduling_exemption_heading',
        'type'       => 'title'
    ]);
    $iande_emails_options->add_field([
        'id'         => 'email_pre_scheduling_exemption_title',
        'show_names' => false,
        'type'       => 'text',
        'attributes' => [
            'placeholder' => __('Título do pré agendamento + isenção', 'iande')
        ]
    ]);
    $iande_emails_options->add_field([
        'id'         => 'email_pre_scheduling_exemption',
        'type'       => 'wysiwyg',
        'show_names' => false,
        'options'    => $options
    ]);

    // E-mail 1.3 - Lembrete
    $iande_emails_options->add_field([
        'name'       => __('Lembrete', 'iande'),
        'id'         => 'email_reminder_heading',
        'type'       => 'title'
    ]);
    $iande_emails_options->add_field([
        'id'         => 'email_reminder_title',
        'show_names' => false,
        'type'       => 'text',
        'attributes' => [
            'placeholder' => __('Título do lembrete', 'iande')
        ]
    ]);
    $iande_emails_options->add_field([
        'id'         => 'email_reminder',
        'type'       => 'wysiwyg',
        'show_names' => false,
        'options'    => $options
    ]);

    // E-mail 1.4 - Agendamento confirmado
    $iande_emails_options->add_field([
        'name'       => __('Agendamento confirmado', 'iande'),
        'id'         => 'email_confirmed_heading',
        'type'       => 'title'
    ]);
    $iande_emails_options->add_field([
        'id'         => 'email_confirmed_title',
        'show_names' => false,
        'type'       => 'text',
        'attributes' => [
            'placeholder' => __('Título do agendamento confirmado', 'iande')
        ]
    ]);
    $iande_emails_options->add_field([
        'id'         => 'email_confirmed',
        'type'       => 'wysiwyg',
        'show_names' => false,
        'options'    => $options
    ]);

    // E-mail 1.5 - Agendamento cancelado
    $iande_emails_options->add_field([
        'name'       => __('Agendamento cancelado', 'iande'),
        'id'         => 'email_canceled_heading',
        'type'       => 'title'
    ]);
    $iande_emails_options->add_field([
        'id'         => 'email_canceled_title',
        'show_names' => false,
        'type'       => 'text',
        'attributes' => [
            'placeholder' => __('Título do agendamento cancelado', 'iande')
        ]
    ]);
    $iande_emails_options->add_field([
        'id'         => 'email_canceled',
        'type'       => 'wysiwyg',
        'show_names' => false,
        'options'    => $options
    ]);

}
