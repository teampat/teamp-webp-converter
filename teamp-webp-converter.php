<?php

/**
 * Plugin Name: Teamp WebP Converter
 * Plugin URI: https://github.com/teampat/teamp-webp-converter
 * Description: A WordPress plugin to convert images to WebP format with customizable options
 * Version: 1.0.0
 * Author: teampat
 * Author URI: https://github.com/teampat
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: teamp-webp-converter
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SWC_VERSION', '1.0.0');
define('SWC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SWC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once SWC_PLUGIN_DIR . 'includes/class-teamp-webp-converter.php';
require_once SWC_PLUGIN_DIR . 'includes/class-teamp-webp-converter-settings.php';

// Initialize the plugin
function team_webp_converter_init()
{
    $plugin = new Teamp_WebP_Converter();
    $plugin->init();
}
add_action('plugins_loaded', 'team_webp_converter_init');

// Activation hook
register_activation_hook(__FILE__, 'team_webp_converter_activate');
function team_webp_converter_activate()
{
    // Check PHP version and WebP support
    if (!extension_loaded('imagick') && !extension_loaded('gd')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('This plugin requires PHP with Imagick or GD extension for WebP support.');
    }

    // Add default options
    add_option('swc_enabled', '0');
    add_option('swc_quality', '75');
    add_option('swc_delete_original', '1');
    add_option('swc_resize_enabled', '1');
    add_option('swc_max_width', '2048');
    add_option('swc_max_height', '2048');
    add_option('swc_image_types', array('jpg', 'jpeg', 'png'));
}

// Add settings link to plugins page
function team_webp_converter_plugin_action_links($links)
{
    $settings_link = '<a href="' . admin_url('options-general.php?page=teamp-webp-converter') . '">' . __('Settings', 'teamp-webp-converter') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'team_webp_converter_plugin_action_links');