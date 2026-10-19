<?php

/**
 * Plugin Name: Teamp WebP Converter
 * Plugin URI: https://github.com/teampat/teamp-webp-converter
 * Description: A WordPress plugin to convert images to WebP format with customizable options
 * Version: 1.0.2
 * Requires at least: 6.7
 * Requires PHP: 7.4
 * Tested up to: 7.1
 * Author: teampat
 * Author URI: https://github.com/teampat
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: teamp-webp-converter
 */

if (!defined('ABSPATH')) {
    exit;
}

define('TEAMP_WEBP_VERSION', '1.0.2');
define('TEAMP_WEBP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TEAMP_WEBP_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once TEAMP_WEBP_PLUGIN_DIR . 'includes/class-teamp-webp-converter.php';
require_once TEAMP_WEBP_PLUGIN_DIR . 'includes/class-teamp-webp-converter-settings.php';

function teamp_webp_converter_init()
{
    Teamp_WebP_Converter_Settings::migrate_legacy_options();

    load_plugin_textdomain(
        'teamp-webp-converter',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );

    $plugin = new Teamp_WebP_Converter();
    $plugin->init();
}
add_action('plugins_loaded', 'teamp_webp_converter_init');

register_activation_hook(__FILE__, 'teamp_webp_converter_activate');
function teamp_webp_converter_activate()
{
    Teamp_WebP_Converter_Settings::migrate_legacy_options();
    Teamp_WebP_Converter_Settings::add_default_options();
}

function teamp_webp_converter_plugin_action_links($links)
{
    $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=teamp-webp-converter')) . '">' . esc_html__('Settings', 'teamp-webp-converter') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'teamp_webp_converter_plugin_action_links');
