<?php $title = 'Login'; ?>
<?php require 'parts/header.php'; ?>

<?php
    if (!empty($_GET['next'])) {
        $next = $_GET['next'];
    }
?>
<iande-login-page next="<?= $next ?>" reset-password="<?= wp_lostpassword_url(get_site_url(null, '/iande/user/welcome')) ?>"></iande-login-page>

<?php require 'parts/footer.php'; ?>
