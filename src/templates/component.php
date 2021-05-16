<?php require 'parts/header.php'; ?>

<iande-page page="<?= esc_attr($component) ?>" :props="<?= esc_attr(json_encode((object)$props)) ?>"></iande-page>

<?php require 'parts/footer.php'; ?>
