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
    $purposes         = cmb2_get_option('iande_appointments_settings', 'appointment_purpose', []);
    $profiles         = cmb2_get_option('iande_institution', 'institution_profile', []);
    $responsible_role = cmb2_get_option('iande_institution', 'institution_responsible_role', []);
    $deficiency       = cmb2_get_option('iande_institution', 'institution_deficiency', []);
    $language         = cmb2_get_option('iande_institution', 'institution_language', []);
    $age_range        = cmb2_get_option('iande_institution', 'institution_age_range', []);
    $scholarity       = cmb2_get_option('iande_institution', 'institution_scholarity', []);

    wp_localize_script(
        'iande',
        'IandeSettings',
        [
            'siteName'          => $site_name,
            'siteUrl'           => $site_url,
            'iandeUrl'          => $iande_url,
            'profiles'          => $profiles,
            'purposes'          => $purposes,
            'responsibleRoles'  => $responsible_role,
            'deficiencies'      => $deficiency,
            'languages'         => $language,
            'ageRanges'         => $age_range,
            'scholarity'        => $scholarity,
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
     * E-mail 1.1 - Pré agendamento
     */
    if (empty($emails_settings['email_pre_scheduling_title'])) {
        $title = "Confirmação de reserva da visita";
        cmb2_update_option('iande_emails_settings', 'email_pre_scheduling_title', $title);
    }

    if (empty($emails_settings['email_pre_scheduling'])) {

        $message = "Olá %nome%,

Aqui estão os dados da sua reserva para visitar o Museu <b>" . get_bloginfo('name') . "</b>.

<b>Exposição:</b> %exposicao%
<b>Data da visita:</b> %data%
<b>Horário da visita:</b> %horario%
<b>Número de visitantes:</b> %visitantes%

Para finalizar seu agendamento e garantir a visita do seu grupo, preencha os demais dados do seu grupo <a href='%link%'>clicando aqui</a>.

Você tem alguma dúvida? Entre em contato!";

        cmb2_update_option('iande_emails_settings', 'email_pre_scheduling', $message);

    }

    /**
     * E-mail 1.2 - Pré agendamento + isenção
     */
    if (empty($emails_settings['email_pre_scheduling_exemption_title'])) {
        $title = "Confirmação de reserva da visita";
        cmb2_update_option('iande_emails_settings', 'email_pre_scheduling_exemption_title', $title);
    }

    if (empty($emails_settings['email_pre_scheduling_exemption'])) {

        $message = "Olá %nome%,

Aqui estão os dados da sua reserva para visitar o Museu <b>" . get_bloginfo('name') . "</b>.

<b>Exposição:</b> %exposicao%
<b>Data da visita:</b> %data%
<b>Horário da visita:</b> %horario%
<b>Número de visitantes:</b> %visitantes%

Para finalizar seu agendamento e garantir a visita do seu grupo, preencha os demais dados do seu grupo <a href='%link%'>clicando aqui</a>.

Você tem alguma dúvida? Entre em contato!

Você solicitou a isenção do pagamento dos ingressos na sua visita ao Museu <b>" . get_bloginfo('name') . "</b>.

Lembramos que a gratuidade se aplica aos seguintes casos:

<li>Idosos acima de 60 anos</li>
<li>Crianças até 6 anos</li>
<li>Deficientes</li>
<li>Alunos da rede pública</li>
<li>Acompanhantes de grupos com visitas agendadas</li>
<li>Membros do ICOM, mediante apresentação de comprovante</li>

Anexado a este e-mail você encontrará um modelo de pedido de isenção que deve ser preenchido e enviado para o e-mail %email_museu%, para que sua solicitação de isenção seja processada.
 
Caso você tenha dúvidas se está apto a solicitar a isenção do ingresso, entre em contato.";

        cmb2_update_option('iande_emails_settings', 'email_pre_scheduling_exemption', $message);

    }

    /**
     * E-mail 1.3 - Lembrete
     */
    if (empty($emails_settings['email_reminder_title'])) {
        $title = "Lembrete de visita";
        cmb2_update_option('iande_emails_settings', 'email_reminder_title', $title);
    }

    if (empty($emails_settings['email_reminder'])) {

        $message = "A data da visita do seu grupo ao Museu <b>" . get_bloginfo('name') . "</b> está próxima!
 
<b>Exposição:</b> %exposicao%
<b>Data da visita:</b> %data%
<b>Horário da visita:</b> %horario%
<b>Número de visitantes:</b> %visitantes%

Estamos aguardando sua visita.";
        
        cmb2_update_option('iande_emails_settings', 'email_reminder', $message);
    }

    /**
     * E-mail 1.4 - Agendamento confirmado
     */
    if (empty($emails_settings['email_confirmed_title'])) {
        $title = "Visita confirmada";
        cmb2_update_option('iande_emails_settings', 'email_confirmed_title', $title);
    }

    if (empty($emails_settings['email_confirmed'])) {

        $message = "Olá %nome%,

A sua visita está confirmada! Te esperamos no dia e horário a seguir:

<b>Exposição:</b> %exposicao%
<b>Data da visita:</b> %data%
<b>Horário da visita:</b> %horario%
<b>Número de visitantes:</b> %visitantes%
 
Veja as regras de visitação do Museu acesando <a href='%regras%'>esse link</a>. 

Gostaríamos de saber um pouco mais sobre você. Por favor, preencha uma breve pesquisa sobre sua relação com o Museu <b>" . get_bloginfo('name') . "</b> <a href='%regras%'>clicando aqui</a>.
 
Mudou de ideia? Não se esqueça de cancelar seu agendamento <a href='%cancelar%'>clicando aqui</a>.
Precisa modificar a seu agendamento? Cancele e agende novamente <a href='%agendar%'>clicando aqui</a>.

Nos vemos em breve!";

        cmb2_update_option('iande_emails_settings', 'email_confirmed', $message);

    }

    /**
     * E-mail 1.5 - Agendamento cancelado
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
<b>Data da visita:</b> %data%
<b>Horário da visita:</b> %horario%
<b>Número de visitantes:</b> %visitantes%";

        cmb2_update_option('iande_emails_settings', 'email_canceled', $message);

    }
    
}

/**
 * Adiciona post_status "canceled"
 * @see https://developer.wordpress.org/reference/functions/register_post_status/
 * @see https://wordpress.org/support/article/post-status/#custom-status
 */
add_action('init', 'iande_register_status');

function iande_register_status() {

    register_post_status('canceled', [
        'label'                     => _x('Cancelado', 'post'),
        'public'                    => true,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Cancelado <span class="count">(%s)</span>', 'Cancelado <span class="count">(%s)</span>'),
    ]);

}

/**
 * Adiciona CMB2 group_list
 * @see https://cmb2.io/docs/adding-your-own-field-types
 */

\add_action('cmb2_render_group_list', 'cmb2_render_callback_for_group_list', 10, 5);
function cmb2_render_callback_for_group_list($field, $escaped_value, $object_id, $object_type, $field_type_object) {

    $groups_json = json_decode($field->value, true);

    if (array_key_exists('groups', $groups_json)) {

        foreach($groups_json['groups'] as $group) {

            echo '<div class="cmb-row cmb-row-top">';

                echo '<div class="cmb-th">';
                    echo '<b>Grupo</b>';
                echo '</div>';

                echo '<div class="cmb-td">';
                    echo $group['id'];
                echo '</div>';

            echo '</div>';
            echo '<div class="cmb-row cmb-row-line"">';

                echo '<div class="cmb-th">';
                    echo '<b>Deficiências do Grupo</b>';
                echo '</div>';

                echo '<div class="cmb-td">';
                    foreach ($group['disabilities'] as $disability) {
                        if (!empty($disability['other'])) {
                            echo 'Outra / ' . $disability['other'] . ' (' . $disability['count'] . ')';
                        } else {
                            echo $disability['type'] . ' (' . $disability['count'] . ')';
                        }
                        echo ("<br>");
                    }
                echo '</div>';

            echo '</div>';
            echo '<div class="cmb-row cmb-row-line">';

                echo '<div class="cmb-th">';
                    echo '<b>Idiomas do Grupo</b>';
                echo '</div>';

                echo '<div class="cmb-td">';
                    foreach ($group['languages'] as $language) {
                        if (is_string($language)) {
                            echo $language;
                        } else if (!empty($language['other'])) {
                            echo 'Outros / ' . $language['other'];
                        } else {
                            echo $language['name'];
                        }
                        echo ("<br>");
                    }
                echo '</div>';

            echo '</div>';
            echo '<div class="cmb-row cmb-row-line">';

                echo '<div class="cmb-th">';
                    echo '<b>Responsável do grupo</b>';
                echo '</div>';

                echo '<div class="cmb-td">';
                    echo $group['name'];
                echo '</div>';

            echo '</div>';
            echo '<div class="cmb-row cmb-row-line">';

                echo '<div class="cmb-th">';
                    echo '<b>Quantidade de pessoas no grupo</b>';
                echo '</div>';

                echo '<div class="cmb-td">';
                    echo $group['num_people'];
                echo '</div>';

            echo '</div>';
            echo '<div class="cmb-row cmb-row-line">';

                echo '<div class="cmb-th">';
                    echo '<b>Quantidade de responsáveis pelo grupo</b>';
                echo '</div>';

                echo '<div class="cmb-td">';
                    echo $group['num_responsible'];
                echo '</div>';

            echo '</div>';
            echo '<div class="cmb-row cmb-row-line">';

                echo '<div class="cmb-th">';
                    echo '<b>Escolaridade do grupo</b>';
                echo '</div>';

                echo '<div class="cmb-td">';
                    echo $group['scholarity'];
                echo '</div>';

            echo '</div>';

        }

        echo '<hr>';

        echo '<details>';
            echo '<summary>' . __('JSON', 'iande') . '</summary>';
            echo $field_type_object->textarea(['type' => 'textarea-small']);
            //echo '<textarea disabled>' . json_encode($escaped_value) . '</textarea>';
        echo '</details>';

    } else {
        _e('Nenhum grupo cadastrado', 'iande');
    }

}

\add_filter('cmb2_sanitize_group_list', 'cmb2_sanitize_group_list_callback', 10, 2);
function cmb2_sanitize_group_list_callback($override_value, $value) {
    return $value;
}

/**
 * Adiciona CMB2 calendar_appointments
 * @see https://cmb2.io/docs/adding-your-own-field-types
 */

 \add_action('cmb2_render_calendar_appointments', 'cmb2_render_callback_for_calendar_appointments', 10, 5);
function cmb2_render_callback_for_calendar_appointments($field, $escaped_value, $object_id, $object_type, $field_type_object) {
    ?>
    <div class="iande-admin-app">
        <iande-appointments-agenda :exhibition-id="<?= $object_id ?>"></iande-appointments-agenda>
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
