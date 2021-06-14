<?php

namespace IandePlugin;

use ReCaptcha;

/**
 * Return reCAPTCHA v3 keys, if available, or false
 * @return object|false The keys dictionary
 */
function compute_recaptcha_keys () {

    if (is_plugin_active('google-captcha/google-captcha.php')) {
        $config = \get_option('gglcptch_options');

        if (!empty($config) && $config['recaptcha_version'] == 'v3') {
            return (object) [
                'public'  => $config['public_key'],
                'private' => $config['private_key'],
            ];
        }

    }

    return false;
}

/**
 * Verify if reCAPTCHA token is valid
 * @return boolean
 */
function verify_recaptcha ($token) {
    require_once 'recaptchalib.php';

    $recaptcha_keys = compute_recaptcha_keys();
    if (empty($recaptcha_keys)) {
        return true;
    }

    $remote_ip = $_SERVER['REMOTE_ADDR'];

    $recaptcha = new ReCaptcha($recaptcha_keys->private);
    $recaptcha_response = $recaptcha->verifyResponse($remote_ip, $token);
    return $recaptcha_response->success;
}
