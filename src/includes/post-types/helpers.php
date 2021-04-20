<?php

namespace IandePlugin;

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