<?php

namespace IandePlugin;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


/**
 * Adiciona filtro para evitar que o usuário apague todo o vocabulário `institution_profile`
 */
add_filter('cmb2_override_institution_profile_meta_value', 'IandePlugin\\iande_cmb2_institution_profile_default', 10, 4);

function iande_cmb2_institution_profile_default($data, $object_id, $args, $cmb)
{

    if (empty(cmb2_options($args['id'])->get($args['field_id']))) {

        $data = [
            'Escola estadual',
            'Escola municipal',
            'Escola federal',
            'Escola privada',
            'Universidade pública',
            'Universidade/faculdade privada',
            'ONG',
            'Agência turismo',
            'Empresa',
            'Outros'
        ];
    }

    return $data;

}


/**
 * Adiciona filtro para evitar que o usuário apague todo o vocabulário `institution_scholarity`
 */
add_filter('cmb2_override_institution_scholarity_meta_value', 'IandePlugin\\iande_cmb2_institution_scholarity_default', 10, 4);

function iande_cmb2_institution_scholarity_default($data, $object_id, $args, $cmb)
{

    if (empty(cmb2_options($args['id'])->get($args['field_id']))) {

        $data = [
            'Educação infantil',
            'Ensino fundamental I',
            'Ensino fundamental II',
            'Ensino médio',
            'Ensino técnico',
            'EJA | MOVA',
            'Ensino superior',
            'Turma mista'
        ];
    }

    return $data;

}


/**
 * Adiciona filtro para evitar que o usuário apague todo o vocabulário `institution_responsible_role`
 */
add_filter('cmb2_override_institution_responsible_role_meta_value', 'IandePlugin\\iande_cmb2_institution_responsible_role_default', 10, 4);

function iande_cmb2_institution_responsible_role_default($data, $object_id, $args, $cmb)
{

    if (empty(cmb2_options($args['id'])->get($args['field_id']))) {

        $data = [
            'Professor',
            'Orientador',
            'Coordenador',
            'Diretor',
            'Guia de turismo',
            'Outros'
        ];
    }

    return $data;

}


/**
 * Adiciona filtro para evitar que o usuário apague todo o vocabulário `institution_deficiency`
 */
add_filter('cmb2_override_institution_deficiency_meta_value', 'IandePlugin\\iande_cmb2_institution_deficiency_default', 10, 4);

function iande_cmb2_institution_deficiency_default($data, $object_id, $args, $cmb)
{

    if (empty(cmb2_options($args['id'])->get($args['field_id']))) {

        $data = [
            'Pessoa com deficiência intelectual',
            'Pessoa com deficiência física',
            'Cego / Baixa Visão',
            'Surdo / Baixa Audição',
            'Pessoa com mobilidade reduzida',
            'Outros'
        ];
    }

    return $data;

}


/**
 * Adiciona filtro para evitar que o usuário apague todo o vocabulário `institution_language`
 */
add_filter('cmb2_override_institution_language_meta_value', 'IandePlugin\\iande_cmb2_institution_language_default', 10, 4);

function iande_cmb2_institution_language_default($data, $object_id, $args, $cmb)
{

    if (empty(cmb2_options($args['id'])->get($args['field_id']))) {

        $data = [
            'Inglês',
            'Espanhol',
            'Libras',
            'Outros'
        ];
    }

    return $data;

}


/**
 * Adiciona filtro para evitar que o usuário apague todo o vocabulário `institution_age_range`
 */
add_filter('cmb2_override_institution_age_range_meta_value', 'IandePlugin\\iande_cmb2_institution_age_range_default', 10, 4);

function iande_cmb2_institution_age_range_default($data, $object_id, $args, $cmb)
{

    if (empty(cmb2_options($args['id'])->get($args['field_id']))) {

        $data = [
            'Até 4 anos',
            'De 5 a 9 anos',
            'De 10 a 14 anos',
            'De 15 a 19 anos',
            'De 20 a 24 anos',
            'De 25 a 39 anos',
            'De 40 a 59 anos',
            'Acima 60 anos',
            'Grupo misto'
        ];
    }

    return $data;

}