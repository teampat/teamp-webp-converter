<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$options = array(
    'swc_enabled',
    'swc_quality',
    'swc_delete_original',
    'swc_resize_enabled',
    'swc_max_width',
    'swc_max_height',
    'swc_image_types',
);

foreach ($options as $option) {
    delete_option($option);
}
