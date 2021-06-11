<?php

namespace IandePlugin;

use Controller;

class Exhibition extends Controller
{

    /**
     * Retorna uma exposição pelo id
     *
     * @param array $params
     *
     * @return void
     */
    function endpoint_get(array $params = [])
    {

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro id é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro id deve ser um número inteiro', 'iande'));
        }

        if (get_post_type($params['ID']) != 'exhibition') {
            $this->error(__('O ID informado não é uma exposição válida', 'iande'));
        }

        $exhibition = $this->get_parsed_exhibition($params['ID']);

        if (empty($exhibition)) {
            return; // 404
        }

        $this->success($exhibition);

    }

    /**
     * Retorna a lista de exposições. Por padrão, exibe apenas exposições
     * públicas, mas pode retornar exposições privadas também.
     *
     * @param array $params
     * @return void
     */
    function endpoint_list(array $params = [])
    {
        $post_statuses = ['publish'];

        if (!empty($params['show_private']) && $params['show_private'] == '1') {
            $post_statuses[] = 'private';
        }

        $args = array(
            'post_type'      => 'exhibition',
            'post_status'    => $post_statuses,
            'posts_per_page' => -1,
            'order'          => 'ASC',
            'orderby'        => 'ID',
        );

        $exhibitions = get_posts($args);

        if (empty($exhibitions)) {
            return $this->success([]);
        }

        $parsed_exhibitions = [];

        foreach ($exhibitions as $key => $exhibition) {
            $parsed_exhibitions[] = $this->get_parsed_exhibition($exhibition->ID);
        }

        $parsed_exhibitions = array_filter($parsed_exhibitions);

        if (empty($parsed_exhibitions)) {
            return $this->success([]);
        }

        $this->success($parsed_exhibitions);

    }

    /**
     * Valida os metadados da exposição
     *
     * @param array $params Valores dos metadados
     * @param boolean $validate_missing_requirements Se deve validar a obrigatoriedade dos campos não passados no array $params
     * @return void
     */
    function validate(array $params = [], $validate_missing_requirements = false)
    {

        $metadata_definition = get_exhibition_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            // validação de campos obrigatórios
            if ($definition->required && empty($params[$key])) {
                if ($validate_missing_requirements) {
                    $this->error(__('O campo [' . $key . '] é obrigatório'));
                } else if (isset($params[$key])) {
                    $this->error($definition->required);
                }
            }

            if (!empty($params[$key])) {
                $validation_fn = $definition->validation;
                $validation = $validation_fn($params[$key], $params);
                $valid = $validation === true;
                if (!$valid) {
                    $this->error($validation);
                }
            }
        }

    }

    /**
     * Parsea a exposição para retorno na API
     *
     * @param \WP_Post $exhibition
     * @param array $metadata
     *
     * @filter iande.exhibition
     *
     * @return object
     */
    function parse_exhibition(\WP_Post $exhibition, array $metadata = [])
    {

        $parsed_exhibition = (object) [
            'ID'      => $exhibition->ID,
            'title'   => $exhibition->post_title,
        ];

        $metadata_definition = get_exhibition_metadata_definition();

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($metadata_definition as $key => $definition) {

            if ($key == 'exception' && isset($metadata[$key][0])) {

                foreach (unserialize($metadata[$key][0]) as $post_id) {

                    $post      = get_post($post_id);
                    $schedules = get_post_meta($post->ID, 'exception', true);
                    $date_from = get_post_meta($post->ID, 'date_from', true);
                    $date_to   = get_post_meta($post->ID, 'date_to', true);

                    $exceptions[] = (object) [
                        'ID'         => $post->ID,
                        'title'      => $post->post_title,
                        'date_from'  => $date_from,
                        'date_to'    => $date_to,
                        'exceptions' => (object) $schedules
                    ];

                }
                $parsed_exhibition->$key = $exceptions;

            } elseif ( in_array($key, $days) && isset($metadata[$key][0])) {

                $day                    = unserialize($metadata[$key][0]);
                $parsed_exhibition->$key = (object) $day;

            } else {
                $parsed_exhibition->$key = isset($metadata[$key][0]) ? $metadata[$key][0] : null;
            }

        }

        $parsed_exhibition = \apply_filters('iande.parse_exhibition', $parsed_exhibition, $exhibition, $metadata);

        return $parsed_exhibition;

    }

    /**
     * Retorna uma exposição parseada
     *
     * @param integer $exhibition_id
     * @return object
     */
    function get_parsed_exhibition(int $exhibition_id)
    {

        $exhibition = get_post($exhibition_id);

        if (is_null($exhibition)) {
            return null;
        }

        $meta = get_post_meta($exhibition_id);

        return $this->parse_exhibition($exhibition, $meta);

    }

}
