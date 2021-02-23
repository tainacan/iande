<?php $title = 'Demo Tainacan'; ?>
<?php require 'parts/header.php'; ?>

<iande-tainacan></iande-tainacan>

<div class="iande-container">
    <?php tainacan_the_faceted_search(['default_view_mode' => 'demo-iande']); ?>
</div>

<?php require 'parts/footer.php'; ?>
