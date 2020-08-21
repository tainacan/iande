<?php

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 * @return mixed           Option value
 */
function iande_get_option($key = '', $default = false) {

    if (function_exists('cmb2_get_option')) {
        // Use cmb2_get_option as it passes through some key filters.
        return cmb2_get_option('iande', $key, $default);
    }

    // Fallback to get_option if CMB2 is not loaded yet.
    $opts = get_option('iande', $default);

    $val = $default;

    if ('all' == $key) {
        $val = $opts;
    } elseif (is_array($opts) && array_key_exists($key, $opts) && false !== $opts[$key]) {
        $val = $opts[$key];
    }

    return $val;
    
}

/**
 * Expõe as configurações/dados do plugin no frontend
 * 
 * @link https://developer.wordpress.org/reference/functions/wp_localize_script/
 * @return var IandeSettings
 */
add_action('wp_enqueue_scripts', 'iande_institution_settings');

function iande_institution_settings() {

    $site_name          = get_bloginfo('name');
    $site_url           = get_bloginfo('url');
    $iande_url          = get_site_url(null, '/iande');
    $profiles           = cmb2_get_option('iande_institution', 'institution_profile', []);
    $responsible_role   = cmb2_get_option('iande_institution', 'institution_responsible_role', []);
    $deficiency         = cmb2_get_option('iande_institution', 'institution_deficiency', []);
    $language           = cmb2_get_option('iande_institution', 'institution_language', []);
    $age_range          = cmb2_get_option('iande_institution', 'institution_age_range', []);
    $schedules          = get_option('iande_schedules', []);
    $schedules_settings = get_option('iande_schedules_settings', []);

    wp_localize_script(
        'iande',
        'IandeSettings',
        [
            'siteName'          => $site_name,
            'siteUrl'           => $site_url,
            'iandeUrl'          => $iande_url,
            'profiles'          => $profiles,
            'responsibleRoles'  => $responsible_role,
            'deficiencies'      => $deficiency,
            'languages'         => $language,
            'ageRanges'         => $age_range,
            'schedules'         => $schedules,
            'schedulesSettings' => $schedules_settings
        ]
    );
}

/**
 * Define os valores padrões quando o plugin é ativado
 * @see /includes/init.php
 */
function iande_cmb2_settings_init() {

    /**
     * Agendamentos -> Objetivos
     */

    $appointment_purpose_default = [
        '',
        'Ilustrar os conteúdos que estou trabalhando com esse grupo',
        'Complementar o processo educacional realizado pela instituição de origem do grupo',
        'Possibilitar ao grupo o acesso/conhecimento à exposições e museus',
        'Promover o aprendizado sobre os temas da exposição/museu',
        'Iniciar a exploração/descoberta de um novo tema',
        'Desenvolver a cultura geral do grupo',
        'Promover uma atividade de lazer'
    ];
    $appointment_purpose = cmb2_get_option('appointment_purpose');

    if (is_array($appointment_purpose)) {
        $merge = array_merge($appointment_purpose_default, $appointment_purpose);
        cmb2_update_option('iande_appointments_settings', 'appointment_purpose', array_unique($merge));
    } else {
        cmb2_update_option('iande_appointments_settings', 'appointment_purpose', $appointment_purpose_default);
    }
    
    /**
     * Perfil
     */
    $institution_profile_default = [
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
    $institution_profile = cmb2_get_option('institution_profile');

    if (is_array($institution_profile)) {
        $merge = array_merge($institution_profile_default, $institution_profile);
        cmb2_update_option('iande_institution', 'institution_profile', array_unique($merge));
    } else {
        cmb2_update_option('iande_institution', 'institution_profile', $institution_profile_default);
    }

    /**
     * Escolaridade
     */
    $institution_schooling_default = [
        'Educação infantil',
        'Ensino fundamental I',
        'Ensino fundamental II',
        'Ensino médio',
        'Ensino técnico',
        'EJA | MOVA',
        'Ensino superior',
        'Turma mista'
    ];
    $institution_schooling = cmb2_get_option('institution_schooling');

    if (is_array($institution_schooling)) {
        $merge = array_merge($institution_schooling_default, $institution_schooling);
        cmb2_update_option('iande_institution', 'institution_schooling', array_unique($merge));
    } else {
        cmb2_update_option('iande_institution', 'institution_schooling', $institution_schooling_default);
    }

    /**
     * Relação do Responsável
     */
    $institution_responsible_role_default = [
        'Professor',
        'Orientador',
        'Coordenador',
        'Diretor',
        'Guia de turismo',
        'Outros'
    ];
    $institution_responsible_role = cmb2_get_option('institution_responsible_role');

    if (is_array($institution_responsible_role)) {
        $merge = array_merge($institution_responsible_role_default, $institution_responsible_role);
        cmb2_update_option('iande_institution', 'institution_responsible_role', array_unique($merge));
    } else {
        cmb2_update_option('iande_institution', 'institution_responsible_role', $institution_responsible_role_default);
    }

    /**
     * Deficiências
     */
    $institution_deficiency_default = [
        'Pessoa com deficiência intelectual',
        'Pessoa com deficiência física',
        'Cego / Baixa Visão',
        'Surdo / Baixa Audição',
        'Pessoa com mobilidade reduzida',
        'Outros'
    ];
    $institution_deficiency = cmb2_get_option('institution_deficiency');

    if (is_array($institution_deficiency)) {
        $merge = array_merge($institution_deficiency_default, $institution_deficiency);
        cmb2_update_option('iande_institution', 'institution_deficiency', array_unique($merge));
    } else {
        cmb2_update_option('iande_institution', 'institution_deficiency', $institution_deficiency_default);
    }

    /**
     * Idiomas
     */
    $institution_language_default = [
        'Inglês',
        'Espanhol',
        'Libras',
        'Outros'
    ];
    $institution_language = cmb2_get_option('institution_language');

    if (is_array($institution_language)) {
        $merge = array_merge($institution_language_default, $institution_language);
        cmb2_update_option('iande_institution', 'institution_language', array_unique($merge));
    } else {
        cmb2_update_option('iande_institution', 'institution_language', $institution_language_default);
    }

    /**
     * Faixa Etária
     */
    $institution_age_range_default = [
        'Até 4 anos',
        'De 5 a 9 anos',
        'De 10 a 14 anos',
        'De 15 a 19 anos',
        'De 20 a 24 anos',
        'De 25 a 39 anos',
        'De 40 a 59 anos',
        'Acima 60 anos',
        'Grupo misto',
    ];
    $institution_age_range = cmb2_get_option('institution_age_range');

    if (is_array($institution_age_range)) {
        $merge = array_merge($institution_age_range_default, $institution_age_range);
        cmb2_update_option('iande_institution', 'institution_age_range', array_unique($merge));
    } else {
        cmb2_update_option('iande_institution', 'institution_age_range', $institution_age_range_default);
    }
}