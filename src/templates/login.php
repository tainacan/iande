<?php $title = __('Login', 'iande'); ?>
<?php require 'parts/header.php'; ?>

<?php
    if (!empty($_GET['next'])) {
        $next = filter_input(INPUT_GET, 'next', FILTER_SANITIZE_URL);
    }
?>
<iande-login-page next="<?php echo esc_url($next) ?>" reset-password="<?php echo wp_lostpassword_url(get_site_url(null, '/iande/user/welcome')) ?>"></iande-login-page>

<?php require 'parts/footer.php'; ?>
