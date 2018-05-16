<?php
/*
 * Plugin Name: MS Accessibility Tools
 * Plugin URI: https://github.com/thiagolt90/ms-accessibility-tools
 * Description: Tools for Accessibility
 * Version: 1.0.0
 * Author: Thiago Teixeira
 * Text Domain: ms-accessibility-tools
 * License: GPLv2 or later
 */
 
/* Prevent direct file access */
if (! defined( 'ABSPATH' )) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}
/* \\ Prevent direct file access */

/* Folders */
define("__MSAT_SRC_FOLDER__", realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR);
define("__MSAT_SRC_CLASS_FOLDER__", __MSAT_SRC_FOLDER__ . "Class" . DIRECTORY_SEPARATOR);
define("__MSAT_SRC_MODULES_FOLDER__", __MSAT_SRC_FOLDER__ . "Modules" . DIRECTORY_SEPARATOR);
define("__MSAT_SRC_PARTIALS_FOLDER__", __MSAT_SRC_FOLDER__ . "Partials" . DIRECTORY_SEPARATOR);
/* \\ Folders */

/* Scripts And Styles */
function msat_plugin_scripts_and_styles()
{
    /* Styles */
    wp_enqueue_style( 'custom_wp_msat_admin_css', plugins_url( '/assets/admin/css/style.css', __FILE__ ) );
    wp_enqueue_style( 'custom_wp_msat_bootstrap_css', plugins_url( '/assets/admin/css/vendor/bootstrap.css', __FILE__ ) );
    /* \ Styles */
}
add_action( 'admin_enqueue_scripts', 'msat_plugin_scripts_and_styles' );
/* \ Scripts And Styles */

/* Autoload Classes */
spl_autoload_register( 'msat_class_autoloader' );
function msat_class_autoloader($class_name)
{
    if (false !== strpos( $class_name, 'MSAT' )) {
        $class_file = str_replace( '_', DIRECTORY_SEPARATOR, str_replace( 'MSAT', 'Class', $class_name ) ) . '.php';
        require_once __MSAT_SRC_FOLDER__ . $class_file;
    }
}
/* \\ Autoload Classes */

/* Start */
new MSAT_AccessibilityTools();
/* \\ Start */