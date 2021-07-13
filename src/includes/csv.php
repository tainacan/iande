<?php

add_action('manage_posts_extra_tablenav', 'iande_export_button', 20, 1);
function iande_export_button($which) {
    global $typenow;

    $post_types = IandePlugin\iande_post_types();

    if (\in_array($typenow, $post_types) && $which === 'top' && \current_user_can('manage_iande_options')) {
        ?>
        <input type="submit" name="iande_export_csv" id="iande_export_csv" class="button button-primary" value="<?php echo __('Exportar CSV', 'iande') ?>" />
        <?php
	}

}

add_action('init', 'iande_export_csv');
function iande_export_csv() {

    if (!\is_admin() || !isset($_GET['iande_export_csv'])) {
        return;
    }

    if (!\current_user_can('manage_iande_options')) {
        return;
    }

    $post_type = \filter_input(INPUT_GET, 'post_type');
    $post_types = IandePlugin\iande_post_types();
    if (!\in_array($post_type, $post_types)) {
        return;
    }

    if ($post_type === 'group') {
        $metadata_definition = \call_user_func('IandePlugin\\get_all_group_metadata_definition');
    } else {
        $metadata_definition = \call_user_func('IandePlugin\\get_' . $post_type . '_metadata_definition');
    }

    $posts = IandePlugin\serialize_post_type($post_type, $metadata_definition);

    if ($posts) {
        ob_start();

        // Cria o nome do CSV
        $current_datetime = \sanitize_title(date('Ymd-His', current_time('timestamp', 0)));
        $blogname = \sanitize_title(get_bloginfo('name'));
        $filename = $blogname . '-export-' . \sanitize_title($post_type) . '-' . $current_datetime . '.csv';

        // Cabeçalhos do CSV
        $header_row = ['ID', 'post_title', 'post_type', 'post_status', 'post_author', 'post_date'];
        $header_row = \array_merge($header_row, \array_keys($metadata_definition));

        $data_rows = [];
        foreach ($posts as $post) {
            $data_row = [];

            foreach ($header_row as $key) {
                if (isset($post->$key)) {
                    $value = $post->$key;

                    if (\is_scalar($value)) {
                        $data_row[] = $value;
                    } else {
                        $data_row[] = \json_encode($value);
                    }
                } else {
                    $data_row[] = null;
                }
            }

            $data_rows[] = $data_row;
        }

        // Ordena posts por ID
        usort($data_rows, function ($a, $b) {
            return $a[0] > $b[0] ? 1 : -1;
        });

        $fh = @fopen('php://output', 'w');
        // Printa caracteres BOM
        fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));

        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename={$filename}");
        header('Expires: 0');
        header('Pragma: public');

        fputcsv($fh, $header_row);
        foreach ($data_rows as $data_row) {
            fputcsv($fh, $data_row);
        }
        fclose($fh);

        ob_end_flush();

        die();
    }
}
