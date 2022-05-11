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

    $site_name        = get_bloginfo('name');
    $site_url         = get_bloginfo('url');
    $iande_url        = get_site_url(null, '/iande');
    $admin_url        = get_admin_url(null, '/edit.php?post_type=exhibition');
    $tainacan_active  = IandePlugin\is_plugin_active('tainacan/tainacan.php');
    $tainacan_url     = get_site_url(null, '/wp-json/tainacan/v2');
    $purposes         = cmb2_get_option('iande_appointments_settings', 'appointment_purpose', []);
    $profiles         = cmb2_get_option('iande_institution', 'institution_profile', []);
    $responsible_role = cmb2_get_option('iande_institution', 'institution_responsible_role', []);
    $deficiency       = cmb2_get_option('iande_institution', 'institution_deficiency', []);
    $language         = cmb2_get_option('iande_institution', 'institution_language', []);
    $age_range        = cmb2_get_option('iande_institution', 'institution_age_range', []);
    $scholarity       = cmb2_get_option('iande_institution', 'institution_scholarity', []);
    $use_exemption    = cmb2_get_option('iande', 'use_exemption', 'no');
    $privacy_policy   = get_privacy_policy_url();
    $recaptcha_keys   = IandePlugin\compute_recaptcha_keys();

    if (!empty($recaptcha_keys)) {
        $recaptcha_public_key = $recaptcha_keys->public;
    } else {
        $recaptcha_public_key = null;
    }

    wp_localize_script(
        'iande',
        'IandeSettings',
        [
            'siteName'         => $site_name,
            'siteUrl'          => $site_url,
            'iandePath'        => IANDE_PLUGIN_BASEURL,
            'iandeUrl'         => $iande_url,
            'adminUrl'         => $admin_url,
            'tainacanActive'   => $tainacan_active,
            'tainacanUrl'      => $tainacan_url,
            'profiles'         => $profiles,
            'purposes'         => $purposes,
            'responsibleRoles' => $responsible_role,
            'disabilities'     => $deficiency,
            'languages'        => $language,
            'ageRanges'        => $age_range,
            'scholarity'       => $scholarity,
            'useExemption'     => $use_exemption,
            'privacyPolicy'    => $privacy_policy,
            'nonce'            => \wp_create_nonce('wp_rest'),
            'recaptchaKey'     => $recaptcha_public_key,
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
    $institution_scholarity_default = [
        'Educação infantil',
        'Ensino fundamental I',
        'Ensino fundamental II',
        'Ensino médio',
        'Ensino técnico',
        'EJA | MOVA',
        'Ensino superior',
        'Turma mista'
    ];
    $institution_scholarity = cmb2_get_option('institution_scholarity');

    if (is_array($institution_scholarity)) {
        $merge = array_merge($institution_scholarity_default, $institution_scholarity);
        cmb2_update_option('iande_institution', 'institution_scholarity', array_unique($merge));
    } else {
        cmb2_update_option('iande_institution', 'institution_scholarity', $institution_scholarity_default);
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


    /**
     * Define os e-mails padrões ao ativar o plugin
     */
    $emails_settings = \get_option('iande_emails_settings', '');

    /**
     * E-mail do museu
     */
    if (empty($emails_settings['email_contact'])) {

        cmb2_update_option('iande_emails_settings', 'email_contact', get_option('admin_email'));

    }

    /**
     * Assinatura dos e-mails
     */
    if (empty($emails_settings['email_signature'])) {

        $message = "\n\r
--
<b><a href=". \home_url('/') .">" . \get_bloginfo('name') . "</b>
<b>Telefone:</b> +55 (00) 0000 0000
<b>E-mail:</b> " . \get_option('admin_email');

        cmb2_update_option('iande_emails_settings', 'email_signature', $message);

    }

    /**
     * E-mail 1.1 - Solicitação de isenção
     */
    if (empty($emails_settings['email_exemption_title'])) {
        $title = "Formulário de isenção";
        cmb2_update_option('iande_emails_settings', 'email_exemption_title', $title);
    }

    if (empty($emails_settings['email_exemption'])) {

        $message = "Olá %nome%,

Você solicitou a isenção do pagamento dos ingressos na sua visita ao Museu <b>" . get_bloginfo('name') . "</b>.

<b>Exposição:</b> %exposicao%
<b>Grupo(s): </b> %grupos%

Lembramos que a gratuidade se aplica aos seguintes casos:

<li>Idosos acima de 60 anos</li>
<li>Crianças até 6 anos</li>
<li>Pessoas com deficiência</li>
<li>Estudantes da rede pública</li>
<li>Acompanhantes de grupos com visitas agendadas</li>
<li>Membros do ICOM, mediante apresentação de comprovante</li>

Anexado a este e-mail você encontrará um modelo de pedido de isenção que deve ser preenchido e enviado para o e-mail do Museu <b>" . get_bloginfo('name') . "</b>, para que sua solicitação de isenção seja processada.

Caso você tenha dúvidas se está apto a solicitar a isenção do ingresso, entre em contato.";

        cmb2_update_option('iande_emails_settings', 'email_exemption', $message);

    }

    /**
     * E-mail 1.2 - Lembrete
     */
    if (empty($emails_settings['email_reminder_title'])) {
        $title = "Lembrete de visita";
        cmb2_update_option('iande_emails_settings', 'email_reminder_title', $title);
    }

    if (empty($emails_settings['email_reminder'])) {

        $message = "A data da visita do seu grupo ao Museu <b>" . get_bloginfo('name') . "</b> está próxima!

<b>Exposição:</b> %exposicao%
<b>Grupo(s):</b> %grupos%

Estamos aguardando sua visita.";

        cmb2_update_option('iande_emails_settings', 'email_reminder', $message);
    }

    /**
     * E-mail 1.3 - Agendamento confirmado
     */
    if (empty($emails_settings['email_confirmed_title'])) {
        $title = "Visita confirmada";
        cmb2_update_option('iande_emails_settings', 'email_confirmed_title', $title);
    }

    if (empty($emails_settings['email_confirmed'])) {

        $message = "Olá %nome%,

A sua visita está confirmada! Te esperamos no dia e horário a seguir:

<b>Exposição:</b> %exposicao%
<b>Grupo(s): </b> %grupos%

Para ver todos os detalhes do seu agendamento, <a href='%link%'>clicando aqui</a>.

Nos vemos em breve!";

        cmb2_update_option('iande_emails_settings', 'email_confirmed', $message);

    }

    /**
     * E-mail 1.4 - Agendamento cancelado
     */
    if (empty($emails_settings['email_canceled_title'])) {
        $title = "Visita cancelada";
        cmb2_update_option('iande_emails_settings', 'email_canceled_title', $title);
    }

    if (empty($emails_settings['email_canceled'])) {

        $message = "Olá %nome%,

Sua visita foi cancelada.

Dados da visita cancelada:

<b>Exposição:</b> %exposicao%
<b>Grupo(s):</b> %grupos%
<b>Motivo do cancelament:</b> %motivo%

Caso queira fazer um novo agendamento, <a href='%link%'>clique aqui</a>.";

        cmb2_update_option('iande_emails_settings', 'email_canceled', $message);

    }

    /**
     * E-mail 1.5 - Pós-visita
     */
    if (empty($emails_settings['email_after_visiting_title'])) {
        $title = "Agradecemos sua visita";
        cmb2_update_option('iande_emails_settings', 'email_after_visiting_title', $title);
    }

    if (empty($emails_settings['email_after_visiting'])) {

        $message = "Olá %nome%,

Agradecemos por sua visita à exposição %exposicao%, no dia %data%.

Por favor, acesse <a href='%link%'>esse link</a> e faça uma avaliação de sua visitação. Ela é muito importante para que possamos diagnosticar e aplicar melhorias em nossos procedimentos.

Nos vemos em sua próxima visita!";

        cmb2_update_option('iande_emails_settings', 'email_after_visiting', $message);

    }

}

/**
 * Adiciona CMB2 calendar_appointments
 * @see https://cmb2.io/docs/adding-your-own-field-types
 */

 \add_action('cmb2_render_calendar_appointments', 'cmb2_render_callback_for_calendar_appointments', 10, 5);
function cmb2_render_callback_for_calendar_appointments($field, $escaped_value, $object_id, $object_type, $field_type_object) {
    ?>
    <div class="iande-admin-app">
        <iande-exhibition-agenda :exhibition-id="<?php echo $object_id ?>"></iande-exhibition-agenda>
    </div>
    <?php
}

/**
 * Adiciona CMB2 iande_date
 */

\add_filter('cmb2_render_iande_date', 'cmb2_render_iande_date_callback', 10, 5);
function cmb2_render_iande_date_callback($field, $escaped_value, $object_id, $object_type, $field_type_object) {
    $value = $field->value;
    if (!empty($value) && is_string($value)) {
        if (strpos($value, 'AM')) {
            $value = substr($value, 0, 5);
        } else if (strpos($value, 'PM')) {
            $value = (intval(substr($value, 0, 2)) + 12) . substr($value, 2, 3);
        }
    }
	echo $field_type_object->input(['type' => 'date', 'value' => $field->value]);
}

\add_filter('cmb2_sanitize_iande_date', 'cmb2_sanitize_iande_date_callback', 10, 2);
function cmb2_sanitize_iande_date_callback($override_value, $value) {
    $d = \DateTime::createFromFormat('Y-m-d', $value);
    if ($d && $d->format('Y-m-d') === $value) {
        return $value;
    } else {
        return substr($value, 0, 5) || '';
    }
}

/**
 * Adiciona CMB2 iande_time
 */

\add_filter('cmb2_render_iande_time', 'cmb2_render_iande_time_callback', 10, 5);
function cmb2_render_iande_time_callback($field, $escaped_value, $object_id, $object_type, $field_type_object) {
	echo $field_type_object->input(['type' => 'time', 'value' => $field->value]);
}

\add_filter('cmb2_sanitize_iande_time', 'cmb2_sanitize_iande_time_callback', 10, 2);
function cmb2_sanitize_iande_time_callback($override_value, $value) {
    $d = \DateTime::createFromFormat('H:i', $value);
    if ($d && $d->format('H:i') === $value) {
        return $value;
    } else {
        return substr($value, 0, 5) || '';
    }
}

/**
 * Verifica se o museu permitiu a solicitação de isenções
 *
 * @return boolean
 */
function use_exemption() {

    $use_exemption = cmb2_get_option('iande', 'use_exemption', 'no');

    if ($use_exemption == 'yes') {
        return true;
    } else {
        return false;
    }

}

/**
 * Verifica se existem horários especiais cadastrados
 *
 * @return boolean
 */
function has_exception() {

    $args = [
        'post_type' => 'exception',
        'fields'    => 'ids'
    ];

    $exceptions = get_posts($args);

    if (count($exceptions) >= 1) {
        return true;
    } else {
        return false;
    }

}