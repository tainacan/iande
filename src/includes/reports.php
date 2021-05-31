<?php

namespace IandePlugin;

$appointments = parse_report_data('appointment', get_appointment_metadata_definition());
?>
<div class="wrap">
    <h1><?= __('Relatórios', 'iande') ?></h1>
    <pre>
    <?php var_dump($appointments) ?>
    </pre>
    <div id="vue-app">
    </div>
</div>
