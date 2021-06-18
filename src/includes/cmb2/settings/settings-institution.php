<?php

add_action('cmb2_admin_init', 'iande_institution');

function iande_institution() {

    $args = [
        'id'           => 'iande_institution_options_page',
        'title'        => __('Instituições', 'iande'),
        'object_types' => ['options-page'],
        'capability'   => 'manage_iande_options',
        'option_key'   => 'iande_institution',
        'parent_slug'  => 'iande',
        'tab_group'    => 'iande_tabs',
        'tab_title'    => __('Instituições', 'iande'),
        'save_button'  => esc_html__('Salvar opções', 'iande')
    ];

    $iande_institutions_options = new_cmb2_box($args);

    // Perfil
    $iande_institutions_options->add_field([
        'name' => __('Perfil da instituição', 'iande'),
        'id'   => 'institution_profile_title',
        'type' => 'title',
        'desc' => __('Gerencie abaixo os perfis disponíveis para as instituições', 'iande')
    ]);
    $iande_institutions_options->add_field([
        'name'       => __('Perfil', 'iande'),
        'id'         => 'institution_profile',
        'type'       => 'text',
        'repeatable' => true,
        'text'       => [
            'add_row_text' => __('Adicionar nova opção', 'iande'),
        ]
    ]);

    // Escolaridade
    $iande_institutions_options->add_field([
        'name' => __('Escolaridade', 'iande'),
        'id'   => 'iande_scholarity_title',
        'type' => 'title',
        'desc' => __('Gerencie abaixo as escolaridades disponíveis para as instituições', 'iande')
    ]);
    $iande_institutions_options->add_field([
        'name'       => __('Escolaridade', 'iande'),
        'id'         => 'institution_scholarity',
        'type'       => 'text',
        'repeatable' => true,
        'text'       => [
            'add_row_text' => __('Adicionar nova opção', 'iande'),
        ]
    ]);

    // Relação do Responsável
    $iande_institutions_options->add_field([
        'name' => __('Relação do Responsável com a Instituição', 'iande'),
        'id'   => 'iande_responsible_role_title',
        'type' => 'title',
        'desc' => __('Gerencie abaixo as relações disponíveis para as instituições', 'iande')
    ]);
    $iande_institutions_options->add_field([
        'name'       => __('Relação do Responsável com a Instituição', 'iande'),
        'id'         => 'institution_responsible_role',
        'type'       => 'text',
        'repeatable' => true,
        'text'       => [
            'add_row_text' => __('Adicionar nova opção', 'iande'),
        ]
    ]);

    // Deficiências
    $iande_institutions_options->add_field([
        'name' => __('Vocabulário de Deficiências', 'iande'),
        'id'   => 'iande_deficiency_title',
        'type' => 'title',
        'desc' => __('Gerencie abaixo o vocabulário de deficiências atendidas', 'iande')
    ]);
    $iande_institutions_options->add_field([
        'name'       => __('Deficiências', 'iande'),
        'id'         => 'institution_deficiency',
        'type'       => 'text',
        'repeatable' => true,
        'text'       => [
            'add_row_text' => __('Adicionar nova opção', 'iande')
        ]
    ]);

    // Idiomas
    $iande_institutions_options->add_field([
        'name' => __('Vocabulário de Idiomas', 'iande'),
        'id'   => 'iande_language_title',
        'type' => 'title',
        'desc' => __('Gerencie abaixo o vocabulário de idiomas adicionais atendidos', 'iande')
    ]);
    $iande_institutions_options->add_field([
        'name'       => __('Idiomas adicionais', 'iande'),
        'id'         => 'institution_language',
        'type'       => 'text',
        'repeatable' => true,
        'text'       => [
            'add_row_text' => __('Adicionar nova opção', 'iande')
        ]
    ]);

    // Faixa Etária
    $iande_institutions_options->add_field([
        'name' => __('Vocabulário de Faixas Etárias', 'iande'),
        'id'   => 'iande_age_range_title',
        'type' => 'title',
        'desc' => __('Gerencie abaixo o vocabulário de faixas etárias atendidas', 'iande')
    ]);
    $iande_institutions_options->add_field([
        'name'       => __('Faixas Etárias', 'iande'),
        'id'         => 'institution_age_range',
        'type'       => 'text',
        'repeatable' => true,
        'text'       => [
            'add_row_text' => __('Adicionar nova opção', 'iande')
        ]
    ]);

}