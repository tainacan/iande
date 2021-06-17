<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title ?> - <?php bloginfo('name') ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div id="iande-app" class="iande">
        <iande-navbar></iande-navbar>
