<?php

namespace Iande;

/**
 * Determina se um post existe numa array de posts
 *
 * @param int $post_id ID do post procurado
 * @param WP_Post[] $array Lista de posts
 * @return bool
 */
function array_post_exists ($post_id, $array) {
    foreach ($array as $post) {
        if ($post->ID == $post_id) {
            return true;
        }
    }

    return false;
}

/**
 * Mapeia uma array de strings para opções CMB2
 *
 * @param array $array Lista de opções
 * @return array
 */
function map_array_to_options ($array) {
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
function map_posts_to_options ($posts) {
    $options = [];

    foreach ($posts as $post) {
        $options[$post->ID] = $post->post_title;
    }

    return \array_filter($options);
}
