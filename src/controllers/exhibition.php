<?php

namespace IandePlugin;

use Controller;

class Exhibition extends Controller
{

    /**
     * Retorna a disponibilidade de grupos e pessoas em determinada data e horário.
     *
     * @param array $params
     * @return void
     */
    function endpoint_check_availability (array $params = []) {
        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        if (get_post_type($params['ID']) != 'exhibition') {
            $this->error(__('O ID informado não é uma exposição válida', 'iande'));
        }

        $exhibition = $this->get_parsed_exhibition($params['ID']);

        if (empty($exhibition)) {
            return; // 404
        }

        if (empty($params['date'])) {
            $this->error(__('A data é obrigatória', 'iande'));
        }


        if (empty($params['hour'])) {
            $this->error(__('A hora é obrigatória', 'iande'));
        }

        $args = [
            'post_type'   => 'group',
            'post_status' => ['publish', 'pending'],
            'meta_query'  => [
                [
                    'key'   => 'exhibition_id',
                    'value' => $params['ID'],
                ],
                [
                    'key'   => 'date',
                    'value' => $params['date'],
                ],
                [
                    'key'   => 'hour',
                    'value' => $params['hour'],
                ],
            ],
        ];

        $groups = get_posts($args);

        $size = get_post_meta($params['ID'], 'group_size', true);
        $size = empty($size) ? 100 : intval($size);

        $slot = get_post_meta($params['ID'], 'group_slot', true);
        $slot = intval($slot);

        $count = count($groups);

        $this->success([
            'groups' => $slot - $count,
            'visitors' => ($slot - $count) * $size,
        ]);
    }

    /**
     * Retorna uma exposição pelo ID
     *
     * @param array $params
     * @return void
     */
    function endpoint_get(array $params = [])
    {

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
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
        $meta_query = [];

        if (!empty($params['show_private']) && $params['show_private'] == '1') {
            $post_statuses[] = 'private';
        } else {
            $meta_query = [
                'relation' => 'OR',
                [
                    'key'     => 'date_to',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key'     => 'date_to',
                    'value'   => \date('Y-m-d'),
                    'compare' => '>=',
                    'type'    => 'DATE',
                ],
            ];
        }

        $args = array(
            'post_type'      => 'exhibition',
            'post_status'    => $post_statuses,
            'posts_per_page' => -1,
            'order'          => 'ASC',
            'orderby'        => 'ID',
            'meta_query'     => $meta_query,
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
                    $this->error(\sprintf(__('O campo [%s] é obrigatório', 'iande'), \esc_html($key)));
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
