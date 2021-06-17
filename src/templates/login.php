<?php $title = __('Login', 'iande'); ?>
<?php require 'parts/header.php'; ?>

<?php
    if (!empty($_GET['next'])) {
        $next = esc_attr($_GET['next']);
    }
?>
<iande-login-page next="<?php echo $next ?>" reset-password="<?php echo wp_lostpassword_url(get_site_url(null, '/iande/user/welcome')) ?>"></iande-login-page>

<?php require 'parts/footer.php'; ?>
