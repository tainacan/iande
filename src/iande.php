<?php

/**
 * @package           Iande
 *
 * @wordpress-plugin
 * Plugin Name:       Iandé
 * Plugin URI:        https://iandecultura.com.br/
 * Description:       Agendamento de visitas de grupos para instituições que recebem públicos presencial ou digitalmente.
 * Version:           0.15.5
 * Author:            Percebe
 * Author URI:        https://percebeeduca.com.br/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       iande
 * Domain Path:       /languages
 */

namespace IandePlugin;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 */
define('IANDE_PLUGIN_VERSION', '0.15.5');

define('IANDE_PLUGIN_BASEPATH', plugin_dir_path(__FILE__));
define('IANDE_PLUGIN_BASEURL', plugins_url('', __FILE__) . '/');

define('IANDE_PLUGIN_DISTURL', IANDE_PLUGIN_BASEURL  . 'dist/');

require 'includes/init.php';
require 'includes/cmb2.php';
require 'includes/post-types.php';
require 'includes/roles.php';
require 'includes/assets.php';
require 'includes/captcha.php';
require 'includes/templates.php';
require 'includes/routes.php';
require 'includes/reports.php';
require 'includes/csv.php';
require 'includes/cron.php';
require 'includes/email-reminder.php';