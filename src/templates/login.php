<?php $title = __('Login', 'iande'); ?>
<?php require 'parts/header.php'; ?>

<?php
    if (!empty($_GET['next'])) {
        $next = $_GET['next'];
    }
?>
<iande-login-page next="<?= esc_attr($next) ?>" reset-password="<?= wp_lostpassword_url(get_site_url(null, '/iande/appointment/list')) ?>"></iande-login-page>

<?php require 'parts/footer.php'; ?>
