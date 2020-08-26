<?php $title = 'Login'; ?>
<?php require 'parts/header.php'; ?>

<iande-login-page reset-password="<?= wp_lostpassword_url(get_site_url(null, '/iande/appointment/list')) ?>"></iande-login-page>

<?php require 'parts/footer.php'; ?>
