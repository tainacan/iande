<?php

namespace IandePlugin;

use Controller;

class Group extends Controller
{


    /**
     * Cria um agendamento novo
     *
     * @param array $params
     *
     * @action iande.before_create_appointment
     * @action iande.after_create_appointment
     *
     * @return void
     */
    function endpoint_create(array $params = [])
    {

        $this->validate($params, true);

        $args = [
            'post_type'   => 'group',
            'post_author' => get_current_user_id(),
            'post_title'  => '',
            'post_status' => 'pending'
        ];

        $group_id = wp_insert_post($args);

        $this->set_group_metadata($group_id, $params);

        $group = $this->get_parsed_group($group_id);

        $this->success($group);

    }

    /**
     * Insere ou atualiza os metadados do grupo
     *
     * @param integer $post_id
     * @param array $params
     * @return void
     */
    function set_group_metadata(int $post_id, array $params = [])
    {
        $metadata_definition = get_group_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            if (isset($params[$key])) {
                \update_post_meta($post_id, $key, $params[$key]);
            }
        }
    }

    /**
     * Retorna um agendamento parseado
     *
     * @param integer $group_id
     * @return object
     */
    function get_parsed_group(int $group_id)
    {
        $group = \get_post($group_id);

        if (is_null($group)) {
            return null;
        }

        $meta = \get_post_meta($group_id);

        return $this->parse_group($group, $meta);
    }

    /**
     * Parsea o grupo para retorno na API
     *
     * @param \WP_Post $group
     * @param array $metadata
     *
     * @filter iande.parse_group
     *
     * @return object
     */
    function parse_group(\WP_Post $group, array $metadata = [])
    {
        $pased_group = (object) [
            'ID'          => $group->ID,
            'user_id'     => $group->post_author,
            'title'       => $group->post_title,
            'post_status' => $group->post_status
        ];

        $metadata_definition = get_group_metadata_definition();

        foreach ($metadata_definition as $key => $definition) {
            $pased_group->$key = isset($metadata[$key][0]) ? $metadata[$key][0] : null;
        }

        return $pased_group;
    }

    /**
     * Valida os metadados do grupo
     *
     * @param array     $params Valores dos metadados
     * @param boolean   $validate_missing_requirements Se deve validar a obrigatoriedade dos campos não passados no array $params
     * @param boolean   $force Defina como true para conseguir validar campos não obrigatórios - exemplo de uso, endpoint_update para atualizar um campo que não é obrigatório "group_list"
     * @return void
     */
    function validate(array $params = [], $validate_missing_requirements = false, $force = false)
    {

        $metadata_definition = get_group_metadata_definition($params);

        foreach ($metadata_definition as $key => $definition) {

            // validação de campos obrigatórios
            if ($definition->required && empty($params[$key]) && !$force) {
                if ($validate_missing_requirements) {
                    $this->error($definition->required);
                } else if (isset($params[$key]) || empty($params[$key])) {
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