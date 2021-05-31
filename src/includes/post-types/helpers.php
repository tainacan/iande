<?php

namespace IandePlugin;

/**
 * Retorna todos um post de um post_type, devidamente serializado
 * @param string $post_type O 'post_type' escolhido
 * @param array $definitions As definições dos campos do post_type
 * @return array A array de posts
 */
function parse_report_data ($post_type, $definitions) {
    global $wpdb;

    $posts_results = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_type, post_status, post_author, post_date FROM $wpdb->posts WHERE post_type = %s", $post_type));
    $meta_results = $wpdb->get_results($wpdb->prepare("SELECT pm.post_id, pm.meta_key, pm.meta_value FROM $wpdb->postmeta pm INNER JOIN $wpdb->posts p ON p.ID = pm.post_id WHERE p.post_type = %s", $post_type));

    $posts = [];
    foreach ($posts_results as $post) {
        $posts[$post->ID] = $post;
    }

    foreach ($meta_results as $meta) {
        $postId = $meta->post_id;
        $metaKey = $meta->meta_key;
        if (\key_exists($postId, $posts) && \key_exists($metaKey, $definitions)) {
            $posts[$postId]->$metaKey = \maybe_unserialize($meta->meta_value);
        }
    }

    return \array_values($posts);
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
 * @return array
 */
function map_array_to_options (array $array) {
    $options = [];

    foreach ($array as $item) {
        $options[$item] = $item;
    }

    return \array_filter($options);
}

/**
 * Mapeia uma lista de posts para opções CMB2
 *
 * @param WP_Post[] $args Lista de posts
 * @return array
 */
function map_posts_to_options (array $posts) {
    $options = [];

    foreach ($posts as $post) {
        $options[$post->ID] = $post->post_title . ' #' . $post->ID;
    }

    return \array_filter($options);
}

/**
 * Mapeia uma lista de usuários para opções CMB2
 *
 * @param WP_Users[] $args Lista de usuários
 * @param boolean $empty_option Exibir opção em branco
 * @return array
 */
function map_users_to_options (array $users, $empty_option = false) {
    $options = [];

    if ($empty_option) {
        $options[0] = '--';
    }

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
 * Verifica se o usuário é de determinada `role`
 *
 * @param string $role A role para verificar com o usuário atual
 * @return bool
 */
function current_user_is( string $role )
{
    $user = wp_get_current_user();
    return in_array($role, (array) $user->roles);
}