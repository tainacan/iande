<?php

namespace IandePlugin;

/**
 * Rederiza o template
 *
 * @param string $template
 * @param array $params
 * @return void
 */
function template_render(string $template, array $params = [])
{
    $template_filename = IANDE_PLUGIN_BASEPATH . "templates/{$template}.php";

    render($template_filename, $params);
}

/**
 * Renderiza o templata part
 *
 * @param string $template_part
 * @param array $params
 * @return void
 */
function template_part(string $template_part, array $params = [])
{
    $template_filename = IANDE_PLUGIN_BASEPATH . "templates/parts/{$template_part}.php";

    render($template_filename, $params);
}

/**
 * Renderiza o arquivo
 *
 * @param string $filename
 * @param array $params
 * @return void
 */
function render(string $filename, array $params = [])
{
    if (!file_exists($filename)) {
        throw new \Exception("Template {$filename} not found.");
    }

    global $wp_query;

    extract($params);

    require $filename;
}
