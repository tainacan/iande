<?php $title = 'Demo Tainacan'; ?>
<?php require 'parts/header.php'; ?>

<iande-tainacan></iande-tainacan>

<div class="iande-container">
    <?php tainacan_the_faceted_search([
        // 'collection_id' => 10,
        'default_view_mode' => 'demo-iande',
        'enabled_view_modes' => ['demo-iande'],
        'is_forced_view_mode' => true,
    ]); ?>
</div>

<?php require 'parts/footer.php'; ?>
