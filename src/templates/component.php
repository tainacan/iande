<?php require 'parts/header.php'; ?>

<?php
    $tag = "<$component";
    foreach ($props as $key => $value) {
        $tag .= " :$key='" . esc_attr(json_encode($value)) . "'";
    }
    $tag .= "></$component>";
?>

<?= $tag ?>

<?php require 'parts/footer.php'; ?>
