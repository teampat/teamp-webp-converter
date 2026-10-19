<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$options = array(
    'teamp_webp_enabled',
    'teamp_webp_quality',
    'teamp_webp_delete_original',
    'teamp_webp_resize_enabled',
    'teamp_webp_max_width',
    'teamp_webp_max_height',
    'teamp_webp_image_types',
);

foreach ($options as $option) {
    delete_option($option);
}
