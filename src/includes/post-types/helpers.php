<?php

namespace IandePlugin;

/**
 * Retorna uma array contendo todos os post-types reconhecidos
 * pelo Iandé
 *
 * @return array
 */
function iande_post_types () {
    return ['appointment', 'exception', 'exhibition', 'group', 'institution', 'itinerary'];
}

/**
 * Determina se um post existe numa array de posts
 *
 * @param int $post_id ID do post procurado
 * @param WP_Post[] $array Lista de posts
 * @return bool
 */
function array_post_exists ($post_id, array $array) {
    foreach ($array as $post) {
        if ($post->ID == $post_id) {
            return true;
        }
    }

    return false;
}

/**
 * Retorna as opções de municípios, de maneira compatível com o front-end
 *
 * @param string $state UF do estado, para filtragem
 * @return array
 */
function get_city_options ($state = '') {
    $json = \json_decode(\file_get_contents(IANDE_PLUGIN_BASEURL . 'assets/json/municipios.json'), true);
    $options = [];
    if (empty($state)) {
        $state = 'AC'; // First state, alphabetically
    }

    foreach ($json as $key => $value) {
        if (\strpos($key, $state)  === 0) {
            $options[$key] = $value;
        }
    }

    \asort($options, SORT_LOCALE_STRING);
    return $options;
}

/**
 * Retorna as opções de estados, de maneira compatível com o front-end
 *
 * @return array
 */
function get_state_options () {
    $json = \json_decode(\file_get_contents(IANDE_PLUGIN_BASEURL . 'assets/json/estados.json'), true);
    $options = [];

    foreach ($json as $key => $value) {
        $options[$key] = $key;
    }

    \ksort($options);
    return $options;
}

/**
 * Mapeia uma array de strings para opções CMB2
 *
 * @param array $array Lista de opções
 * @param boolean $empty_option Exibir opção em branco
 * @return array
 */
function map_array_to_options (array $array, bool $empty_option = true) {
    $options = $empty_option ? ['' => ''] : [];

    foreach ($array as $item) {
        $options[$item] = $item;
    }

    return $options;
}

/**
 * Mapeia uma lista de posts para opções CMB2
 *
 * @param WP_Post[] $args Lista de posts
 * @param boolean $empty_option Exibir opção em branco
 * @return array
 */
function map_posts_to_options (array $posts, bool $empty_option = true) {
    $options = $empty_option ? ['' => ''] : [];

    foreach ($posts as $post) {
        $options[$post->ID] = $post->post_title . ' #' . $post->ID;
    }

    return $options;
}

/**
 * Mapeia uma lista de usuários para opções CMB2
 *
 * @param WP_Users[] $args Lista de usuários
 * @param boolean $empty_option Exibir opção em branco
 * @return array
 */
function map_users_to_options (array $users, bool $empty_option = true) {
    $options = $empty_option ? [0 => '--'] : [];

    foreach ($users as $user) {
        $options[$user->ID] = $user->data->display_name ?? $user->data->user_nicename;
    }

    return \array_filter($options);
}

/**
 * Retorna os parametros dos campos para os metadados do post type `group`
 *
 * @param array $metadata_definition Definição dos metadados
 * @param object $metabox_definition Objeto \new_cmb2_box com a definição do metabox
 *
 * @filter iande.' . $metabox_definition->meta_box['id'] . '_metabox_fields
 *
 * @link https://cmb2.io/docs/field-parameters
 *
 * @return array
 */
function get_group_fields_parameters(array $metadata_definition, object $metabox_definition)
{

    $fields = [];

    foreach ($metadata_definition as $key => $definition) {

        if (isset($definition->metabox)) {

            $name              = '';
            $desc              = '';
            $default           = '';
            $type              = '';
            $options           = [];
            $save_field        = true;
            $attributes        = [];
            $repeatable        = false;
            $select_all_button = false;

            if (isset($definition->metabox->name))
                $name = $definition->metabox->name;

            if (isset($definition->metabox->desc))
                $desc = $definition->metabox->desc;

            if (isset($definition->metabox->default))
                $default = $definition->metabox->default;

            if (isset($definition->metabox->type))
                $type = $definition->metabox->type;

            if (isset($definition->metabox->options))
                $options = $definition->metabox->options;

            if (isset($definition->metabox->save_field))
                $save_field = $definition->metabox->save_field;

            if (isset($definition->metabox->attributes))
                $attributes = $definition->metabox->attributes;

            if (isset($definition->metabox->repeatable))
                $repeatable = $definition->metabox->repeatable;

            if (isset($definition->metabox->select_all_button))
                $select_all_button = $definition->metabox->select_all_button;

            $fields[] = [
                'name'              => $name,
                'desc'              => $desc,
                'default'           => $default,
                'id'                => $key,
                'type'              => $type,
                'options'           => $options,
                'save_field'        => $save_field,
                'attributes'        => $attributes,
                'repeatable'        => $repeatable,
                'select_all_button' => $select_all_button
            ];

        }

    }

    $fields = \apply_filters('iande.' . $metabox_definition->meta_box['id'] . '_metabox_fields', $fields);

    if (is_object($metabox_definition)) {
        foreach ($fields as $field) {
            $metabox_definition->add_field($field);
        }
    }

    return $metabox_definition;

}

/**
 * Retorna todas definições dos metadados po post type `group`
 *
 * @filter iande.group_all_metadata_definition
 *
 * @return array
 */
function get_all_group_metadata_definition()
{

    $group_metadata_definition    = get_group_metadata_definition();
    $checkin_metadata_definition  = get_group_checkin_metadata_definition();
    $feedback_metadata_definition = get_group_feedback_metadata_definition();
    $report_metadata_definition   = get_group_report_metadata_definition();

    $metadata_definition = array_merge($group_metadata_definition, $checkin_metadata_definition, $feedback_metadata_definition, $report_metadata_definition);

    $metadata_definition = \apply_filters('iande.group_all_metadata_definition', $metadata_definition);

    return $metadata_definition;

}

/**
 * Verifica se o usuário atual possui a capability `checkin`
 */
function is_iande_staff() {
    if (\current_user_can('checkin')) {
        return true;
    }

    return false;
}

/**
 * Adiciona post_status "canceled"
 * @see https://developer.wordpress.org/reference/functions/register_post_status/
 * @see https://wordpress.org/support/article/post-status/#custom-status
 */
function iande_register_status() {

    register_post_status('canceled', [
        'label'                     => _x('Cancelado', 'post', 'iande'),
        'public'                    => true,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Cancelado <span class="count">(%s)</span>', 'Cancelado <span class="count">(%s)</span>', 'iande'),
    ]);

}
add_action('init', 'IandePlugin\\iande_register_status');

/**
 * Adiciona `-- Cancelado` depois do nome do post se seu status for "canceled"
 */
function iande_display_post_states ($post_states, $post) {
    if ($post->post_status == 'canceled') {
        $post_states['canceled'] = _x('Cancelado', 'post', 'iande');
    }
    return $post_states;
}
add_filter('display_post_states', 'IandePlugin\\iande_display_post_states', 10, 2);
