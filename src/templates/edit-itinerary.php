<?php $title = __('Edição de roteiro virtual', 'iande'); ?>
<?php require 'parts/header.php'; ?>

<iande-edit-itinerary-page></iande-edit-itinerary-page>

<div class="iande-container">
    <?php tainacan_the_faceted_search([
        // 'collection_id' => 10,
        'default_view_mode' => 'iande-masonry',
        'enabled_view_modes' => ['iande-masonry', 'iande-list'],
        'is_forced_view_mode' => true,
    ]); ?>
</div>

<?php require 'parts/footer.php'; ?>
