<?php

namespace IandePlugin;

use Controller;

class Itinerary extends Controller
{
    /**
     * Renderiza a página de criação de roteiro virtual
     *
     * @param array $params
     * @return void
     */
    function view_create(array $params = []) {
        $this->require_authentication();
        $this->render_vue(__('Novo roteiro virtual', 'iande'),'create-itinerary');
    }

    /**
     * Renderiza a página de edição de roteiro virtual
     *
     * @param array $params
     *
     * @action iande.before_create_itinerary
     * @action iande.after_create_itinerary
     *
     * @return void
     */
    function view_edit(array $params = []) {
        $this->require_authentication();
        $this->render('edit-itinerary');
    }

    /**
     * Cria um roteiro novo
     *
     * @param array $params
     * @return $array O roteiro criado
     */
    function endpoint_create(array $params = []) {
        $this->require_authentication();
        $this->validate($params, true);

        \do_action('iande.before_create_itinerary', $params);

        $args = [
            'post_type'   => 'itinerary',
            'post_author' => \get_current_user_id(),
            'post_title'  => '',
            'post_status' => 'draft'
        ];

        $itinerary_id = \wp_insert_post($args);

        $this->set_itinerary_metadata($itinerary_id, $params);
        $this->set_itinerary_title($itinerary_id);

        $itinerary = $this->get_parsed_itinerary($itinerary_id);

        \do_action('iande.after_create_itinerary', $itinerary_id, $itinerary);

        $this->success($itinerary);
    }

    /**
     * Retorna um roteiro parseado
     *
     * @param integer $itinerary_id
     * @return object
     */
    function get_parsed_itinerary(int $itinerary_id) {
        $itinerary = \get_post($itinerary_id);

        if (is_null($itinerary)) {
            return null;
        }

        $meta = \get_post_meta($itinerary_id);

        return $this->parse_itinerary($itinerary, $meta);
    }

    /**
     * Parseia o roteiro para retorno na API
     *
     * @param \WP_Post $itinerary
     * @param array $metadata
     *
     * @filter iande.parse_itinerary
     *
     * @return object
     */
    function parse_itinerary(\WP_Post $itinerary, array $metadata = [])
    {
        $parsed_itinerary = (object) [
            'ID'          => $itinerary->ID,
            'user_id'     => $itinerary->post_author,
            'title'       => $itinerary->post_title,
            'post_status' => $itinerary->post_status
        ];

        $metadata_definition = get_itinerary_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            $parsed_itinerary->$key = isset($metadata[$key][0]) ? $metadata[$key][0] : null;
        }

        $parsed_itinerary = \apply_filters('iande.parse_itinerary', $parsed_itinerary, $itinerary, $metadata);

        return $parsed_itinerary;
    }

    /**
     * Insere ou atualiza os metadados do roteiro
     *
     * @param integer $post_id
     * @param array $params
     * @return void
     */
    function set_itinerary_metadata(int $post_id, array $params = []) {
        $metadata_definition = get_itinerary_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            if (isset($params[$key])) {

                \update_post_meta($post_id, $key, $params[$key]);

            }
        }
    }

    /**
     * Define/atualiza o título do roteiro
     *
     * @param integer $itinerary
     * @return void
     */
    function set_itinerary_title(int $itinerary_id) {
        $name = \get_post_meta($itinerary_id, 'name', true);

        if ($name) {
            $title = $name;
        } else {
            $title = 'Roteiro ' . $itinerary_id;
        }

        $slug  = \sanitize_title($title);
        $slug  = \wp_unique_post_slug($slug, $itinerary_id, get_post_status($itinerary_id), 'itinerary', 0);

        if ($title && $slug) {
            $post = [
                'ID'         => $itinerary_id,
                'post_title' => \apply_filters('title', $title),
                'post_name'  => $slug
            ];
            \wp_update_post($post);
        }
    }

    /**
     * Valida os metadados do roteiro
     *
     * @param array $params Valores dos metadados
     * @param boolean $validate_missing_requirements Se deve validar a obrigatoriedade dos campos não passados no array $params
     * @return void
     */
    function validate(array $params = [], $validate_missing_requirements = false) {
        $metadata_definition = get_itinerary_metadata_definition();

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
}
