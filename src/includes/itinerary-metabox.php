<?php

namespace IandePlugin;

/**
 * Adiciona metabox de roteiros para exposições
 *
 * @param post $post O objeto do post
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
 */
function itinerary_metabox($post)
{
    if (is_plugin_active('tainacan/tainacan.php')) {
        \add_meta_box('itinerary_metabox', __('Roteiros', 'iande'), 'IandePlugin\\build_itinerary_metabox', 'exhibition', 'side', 'low');
    }
}
\add_action('add_meta_boxes_exhibition', 'IandePlugin\\itinerary_metabox');

/**
 * Renderiza metabox de roteiros
 *
 * @param post $post The post object
 */
function build_itinerary_metabox($post) {
?>
    <div class="iande-admin-app">
        <iande-itinerary-metabox :previous="<?php echo \esc_attr(\get_post_meta($post->ID, 'tainacan_meta', true)) ?>"></iande-itinerary-metabox>
    </div>
<?php
}

/**
 * Salvar dados da metabox de roteiro
 *
 * @param int $post_id O ID do post
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/save_post
 */
function save_itinerary_metabox($post_id) {
    $tainacan_meta = \filter_var($_POST['tainacan-meta'] ?: '');
    if (!empty($tainacan_meta)) {
        \update_post_meta($post_id, 'tainacan_meta', $tainacan_meta);
    }
}
\add_action('save_post_exhibition', 'IandePlugin\\save_itinerary_metabox', 10, 1);
