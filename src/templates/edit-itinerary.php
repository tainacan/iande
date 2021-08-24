<?php $title = __('Edição de roteiro virtual', 'iande'); ?>
<?php require 'parts/header.php'; ?>

<?php
    $search = [
        'default_view_mode' => 'iande-masonry',
        'enabled_view_modes' => ['iande-masonry', 'iande-list'],
        'hide_exposers_button' => true,
        'is_forced_view_mode' => true,
        'show_filters_button_inside_search_control' => true,
        'show_inline_view_mode_options' => true,
    ];

    $itineraryId = (int) filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);
    if ($itineraryId) {
        $source = get_post_meta($itineraryId, 'source', true);

        if ($source === 'collection') {
            $collectionId = get_post_meta($itineraryId, 'collection', true);
            $search['collection_id'] = $collectionId;
        }

        if ($source === 'exhibition') {
            $exhibitionId = get_post_meta($itineraryId, 'exhibition', true);
            $exhibition = get_post_meta($exhibitionId, 'tainacan_meta', true);

            try {
                $exhibition = json_decode($exhibition, true);
            } catch (Exception $err) {
                $exhibition = [];
            }

            if (!empty($exhibition['metakey']) && !empty($exhibition['metavalue'])) {
                $search['term_id'] = $exhibition['metavalue'];
            } else if (!empty($exhibition['collection'])) {
                $search['collection_id'] = $exhibition['collection'];
            }
        }
    }
?>

<iande-edit-itinerary-page></iande-edit-itinerary-page>

<div class="iande-container">
    <?php tainacan_the_faceted_search($search); ?>
</div>

<?php require 'parts/footer.php'; ?>
