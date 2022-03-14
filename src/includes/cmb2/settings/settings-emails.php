<?php

add_action('cmb2_admin_init', 'iande_settings_emails');

function iande_settings_emails() {

    /**
     * Configurações dos E-mails
     */
    $args = [
        'id'           => 'iande_emails_options_page',
        'object_types' => ['options-page'],
        'capability'   => 'manage_iande_options',
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
        'textarea_rows' => 15,
        'teeny'         => true,
        'dfw'           => false,
        'tinymce'       => true,
        'quicktags'     => true
    ];

    // E-mail do museu
    $iande_emails_options->add_field([
        'name'        => __('E-mail do museu', 'iande'),
        'description' => __('E-mail que será utilizado para contato do público com o museu.', 'iande'),
        'id'          => 'email_contact_heading',
        'type'        => 'title'
    ]);
    $iande_emails_options->add_field([
        'id'          => 'email_contact',
        'type'        => 'text_email',
        'show_names'  => false,
    ]);

    // Assinatura dos e-mails
    $iande_emails_options->add_field([
        'name'        => __('Assinatura dos e-mails', 'iande'),
        'description' => __('Configure abaixo a assinatura de e-mail que acompanhará todos os e-mails enviados durante o processo de agendamento.', 'iande'),
        'id'          => 'email_signature_heading',
        'type'        => 'title'
    ]);
    $iande_emails_options->add_field([
        'id'          => 'email_signature',
        'type'        => 'wysiwyg',
        'show_names'  => false,
        'options'     => $options
    ]);

    // E-mail 1.1 - Solicitação de isenção
    $iande_emails_options->add_field([
        'name'       => __('Solicitação de isenção', 'iande'),
        'id'         => 'email_exemption_heading',
        'type'       => 'title'
    ]);
    $iande_emails_options->add_field([
        'id'         => 'email_exemption_title',
        'show_names' => false,
        'type'       => 'text',
        'attributes' => [
            'placeholder' => __('Título da solicitação de isenção', 'iande')
        ]
    ]);
    $iande_emails_options->add_field([
        'id'          => 'email_exemption',
        'description' => __('<b>Tags especiais</b> quando usadas, serão substituídas automaticamente ao enviar o e-mail: <b>%nome%</b>, <b>%exposicao%</b>, <b>%grupos%</b>, <b>%email_museu%</b>.', 'iande'),
        'type'        => 'wysiwyg',
        'show_names'  => false,
        'options'     => $options
    ]);
    $iande_emails_options->add_field([
        'id'         => 'email_exemption_attachment',
        'type'       => 'file',
        'show_names' => false,
        'options' => [
            'url' => false,
        ],
        'text' => [
            'add_upload_file_text' => __('Adicionar anexo ao e-mail', 'iande'),
            'file_text'            => __('Anexo: ', 'iande'),
            'file_download_text'   => __('Substituir anexo', 'iande'),
            'remove_text'          => __('Remover anexo', 'iande')
        ],
        'query_args' => [
            'type' => [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.oasis.opendocument.text',
                'application/pdf',
            ],
        ]
    ]);

    // E-mail 1.2 - Lembrete
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
        'id'          => 'email_reminder',
        'description' => __('<b>Tags especiais</b> quando usadas, serão substituídas automaticamente ao enviar o e-mail: <b>%exposicao%</b>, <b>%grupos%</b>.', 'iande'),
        'type'        => 'wysiwyg',
        'show_names'  => false,
        'options'     => $options
    ]);

    // E-mail 1.3 - Agendamento confirmado
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
        'id'          => 'email_confirmed',
        'description' => __('<b>Tags especiais</b> quando usadas, serão substituídas automaticamente ao enviar o e-mail: <b>%nome%</b>, <b>%exposicao%</b>, <b>%grupos%</b>, <b>%link%</b>.', 'iande'),
        'type'        => 'wysiwyg',
        'show_names'  => false,
        'options'     => $options
    ]);

    // E-mail 1.4 - Agendamento cancelado
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
        'id'          => 'email_canceled',
        'description' => __('<b>Tags especiais</b> quando usadas, serão substituídas automaticamente ao enviar o e-mail: <b>%nome%</b>, <b>%exposicao%</b>, <b>%grupos%</b>, <b>%link%</b>, <b>%motivo%</b>.', 'iande'),
        'type'        => 'wysiwyg',
        'show_names'  => false,
        'options'     => $options
    ]);

    // E-mail 1.5 - Pós-visita
    $iande_emails_options->add_field([
        'name'       => __('Pós-visita', 'iande'),
        'id'         => 'email_after_visiting_heading',
        'type'       => 'title'
    ]);
    $iande_emails_options->add_field([
        'id'         => 'email_after_visiting_title',
        'show_names' => false,
        'type'       => 'text',
        'attributes' => [
            'placeholder' => __('Título da pós-visita', 'iande')
        ]
    ]);
    $iande_emails_options->add_field([
        'id'          => 'email_after_visiting',
        'description' => __('<b>Tags especiais</b> quando usadas, serão substituídas automaticamente ao enviar o e-mail: <b>%nome%</b>, <b>%data%</b>, <b>%exposicao%</b>, <b>%link%</b>.', 'iande'),
        'type'        => 'wysiwyg',
        'show_names'  => false,
        'options'     => $options
    ]);

}
