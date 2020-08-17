<?php

/**
 * @package           Iande
 *
 * @wordpress-plugin
 * Plugin Name:       Iandé
 * Description:       Agendamento de visitas em museus
 * Version:           0.1
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       iande
 * Domain Path:       /languages
 */

namespace Iande;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 */
define('IANDE_VERSION', '1.0.0');

define('IANDE_BASEPATH', plugin_dir_path(__FILE__));
define('IANDE_BASEURL', plugins_url('', __FILE__) . '/');

define('IANDE_DISTURL', IANDE_BASEURL  . 'dist/');

require 'includes/cmb2/cmb2.php';
require 'includes/post-types.php';
require 'includes/meta-boxes/class-metabox.php';
require 'includes/roles.php';
require 'includes/assets.php';
require 'includes/templates.php';
require 'includes/routes.php';