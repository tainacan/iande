<?php

namespace IandePlugin;

use Controller;

class Itinerary extends Controller {
    /**
     * Renderiza a página de finalização de roteiro virtual
     *
     * @param array $params
     *
     * @return void
     */
    function view_confirm(array $params = []) {
        $this->require_authentication();
        $this->render_vue(__('Novo roteiro virtual', 'iande'), 'confirm-itinerary');
    }

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
     * @return void
     */
    function view_edit(array $params = []) {
        $this->require_authentication();
        $this->render('edit-itinerary');
    }

    /**
     * Renderiza a página de listagem de roteiros virtuais
     *
     * A listagem possui duas seções: uma listagem dos roteiros do usuário
     * e uma listagem de todos os roteiros públicos
     *
     * @param array $params
     *
     * @return void
     */
    function view_list(array $params = []) {
        $this->require_authentication();
        $this->render_vue(__('Roteiros virtuais', 'iande'), 'list-itineraries');
    }

    /**
     * Renderiza a visualização de roteiro
     *
     * Essa página é pública, isso é, acessível por usuários não logados
     *
     * @param array $params
     *
     * @return void
     */
    function view_view(array $params = []) {
        $title = __('Roteiro virtual', 'iande');
        if (!empty($params['ID'])) {
            $title = \get_the_title(filter_input(INPUT_GET, 'ID', FILTER_DEFAULT));
        }

        $this->render_vue($title, 'view-itinerary');
    }

    /**
     * Cria um roteiro novo
     *
     * @param array $params
     *
     * @action iande.before_create_itinerary
     * @action iande.after_create_itinerary
     *
     * @return $array O roteiro criado
     */
    function endpoint_create(array $params = []) {
        $this->require_authentication();
        $this->validate($params);

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
     * Retorna um roteiro pelo ID
     *
     * @param array $params
     *
     * @return void
     */
    function endpoint_get(array $params = []) {

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        $itinerary = $this->get_parsed_itinerary($params['ID']);

        if (empty($params['public']) && $itinerary->post_status !== 'publish') {
            $this->require_authentication();
            $this->check_user_permission($itinerary);
        }

        if (empty($itinerary)) {
            return; // 404
        }

        $this->success($itinerary);
    }

    /**
     * Retorna todos os roteiros do usuário
     *
     * @return void
     */
    function endpoint_list(array $params = []) {
        $this->require_authentication();

        $user_id = \get_current_user_id();

        $args = [
            'author'         => $user_id,
            'post_type'      => 'itinerary',
            'post_status'    => ['draft', 'pending', 'publish'],
            'posts_per_page' => -1,
        ];

        $itineraries = \get_posts($args);

        if (empty($itineraries)) {
            return $this->success([]);
        }

        $parsed_itineraries = [];

        foreach ($itineraries as $key => $itinerary) {
            $parsed_itineraries[] = $this->get_parsed_itinerary($itinerary->ID);
        }

        $parsed_itineraries = array_filter($parsed_itineraries);

        if (empty($parsed_itineraries)) {
            return $this->success([]);
        }

        $this->success($parsed_itineraries);
    }

    /**
     * Retorna todos os roteiros públicos
     *
     * Esse endpoint é paginado
     *
     * @return void
     */
    function endpoint_list_published(array $params = []) {
        $this->require_authentication();

        $page = $params['page'] ?? 1;

        $args = [
            'post_type'      => 'itinerary',
            'post_status'    => ['publish'],
            'paged'          => $page,
            'posts_per_page' => 12,
            'meta_query'     => [
                [
                    'key'   => 'publicly_findable',
                    'value' => 'yes',
                ],
            ],
        ];

        $query = new \WP_Query($args);
        $itineraries = $query->posts;

        if (empty($itineraries)) {
            return $this->success([
                'num_pages' => $query->max_num_pages,
                'page'      => $page,
                'items'     => [],
            ]);
        }

        $parsed_itineraries = [];

        foreach ($itineraries as $key => $itinerary) {
            $parsed_itineraries[] = $this->get_parsed_itinerary($itinerary->ID);
        }

        $parsed_itineraries = array_filter($parsed_itineraries);

        if (empty($parsed_itineraries)) {
            return $this->success([
                'num_pages' => $query->max_num_pages,
                'page'      => $page,
                'items'     => [],
            ]);
        }

        $this->success([
            'num_pages' => $query->max_num_pages,
            'page'      => $page,
            'items'     => $parsed_itineraries,
        ]);
    }

    /**
     * Altera o status do roteiro
     *
     * @param array $params
     *
     * @return void
     */
    function endpoint_set_status(array $params = []) {
        $this->require_authentication();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        if (empty($params['post_status'])) {
            $this->error(__('O parâmetro status é obrigatório', 'iande'));
        }

        $default_post_status = ['publish', 'future', 'draft', 'pending', 'private', 'trash', 'auto-Draft', 'inherit', 'canceled'];
        if (!in_array(\sanitize_title($params['post_status']), $default_post_status)) {
            $this->error(__('O parâmetro status informado não é permitido', 'iande'));
        }

        $itinerary = \get_post($params['ID']);

        if (empty($itinerary)) {
            return; // 404
        }

        $this->check_user_permission($itinerary);

        $itinerary_update = \wp_update_post([
            'ID'          => $params['ID'],
            'post_status' => $params['post_status'],
        ]);

        if (!\is_wp_error($itinerary_update) || !\is_null($itinerary_update)) {
            $parsed_itinerary = $this->get_parsed_itinerary($params['ID']);
            $this->success($parsed_itinerary);
        }
    }

    /**
     * Atualiza o roteiro
     *
     * @param array $params
     *
     * @action iande.before_update_itinerary
     * @action iande.after_update_itinerary
     *
     * @return void
     */
    function endpoint_update(array $params = []) {
        $this->require_authentication();

        if (empty($params['ID'])) {
            $this->error(__('O parâmetro ID é obrigatório', 'iande'));
        }

        if (!is_numeric($params['ID']) || intval($params['ID']) != $params['ID']) {
            $this->error(__('O parâmetro ID deve ser um número inteiro', 'iande'));
        }

        $this->validate($params);

        $itinerary = \get_post($params['ID']);

        $this->check_user_permission($itinerary);

        \do_action('iande.before_update_itinerary', $params);

        $this->set_itinerary_metadata($params['ID'], $params);
        $this->set_itinerary_title($params['ID']);

        \do_action('iande.after_update_itinerary', $params);

        $parsed_itinerary = $this->get_parsed_itinerary($params['ID']);

        $this->success($parsed_itinerary);
    }

    /**
     * Verifica se o usuário tem permissão para ver o roteiro
     * Se não tiver permissão retorna o erro na API
     *
     * @param WP_Post|object $itinerary
     *
     * @return void
     */
    function check_user_permission ($itinerary){

        $user_id = $itinerary instanceof \WP_Post ? $itinerary->post_author : $itinerary->user_id;

        if ($user_id != get_current_user_id()) {
            $this->error(__('Você não tem permissão para ver este roteiro', 'iande'), 403);
        }
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
        $user_name = \get_user_by('ID', $itinerary->post_author)->display_name;

        $parsed_itinerary = (object) [
            'ID'          => $itinerary->ID,
            'user_id'     => $itinerary->post_author,
            'user_name'   => $user_name,
            'title'       => $itinerary->post_title,
            'post_status' => $itinerary->post_status
        ];

        $metadata_definition = get_itinerary_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            $parsed_itinerary->$key = isset($metadata[$key][0]) ? maybe_unserialize($metadata[$key][0]) : null;
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
            $title = \sprintf(__('Roteiro %s', 'iande'), $itinerary_id);
        }

        $slug  = \sanitize_title($title);
        $slug  = \wp_unique_post_slug($slug, $itinerary_id, \get_post_status($itinerary_id), 'itinerary', 0);

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
}
