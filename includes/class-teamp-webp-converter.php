<?php
if (!defined('ABSPATH')) {
    exit;
}

class Teamp_WebP_Converter
{
    public function init()
    {
        add_filter('wp_handle_upload', array($this, 'convert_to_webp'), 10, 2);

        $settings = new Teamp_WebP_Converter_Settings();
        add_action('admin_menu', array($settings, 'add_admin_menu'));
        add_action('admin_init', array($settings, 'register_settings'));
    }

    public static function supports_webp()
    {
        return wp_image_editor_supports(array('mime_type' => 'image/webp'));
    }

    public function convert_to_webp($upload, $context)
    {
        try {
            return $this->maybe_convert_to_webp($upload);
        } catch (Throwable $e) {
            return $upload;
        }
    }

    private function maybe_convert_to_webp($upload)
    {
        if (!is_array($upload) || empty($upload['file'])) {
            return $upload;
        }

        if (Teamp_WebP_Converter_Settings::get('enabled') !== '1') {
            return $upload;
        }

        if (!self::supports_webp()) {
            return $upload;
        }

        $file = $upload['file'];
        $file_type = wp_check_filetype($file);
        $extension = strtolower((string) $file_type['ext']);

        if ($extension === 'webp' || (isset($upload['type']) && $upload['type'] === 'image/webp')) {
            return $upload;
        }

        $allowed_types = $this->get_allowed_types();
        if (!in_array($extension, $allowed_types, true)) {
            return $upload;
        }

        wp_raise_memory_limit('image');

        $editor = wp_get_image_editor($file);
        if (is_wp_error($editor)) {
            return $upload;
        }

        if (Teamp_WebP_Converter_Settings::get('resize_enabled') === '1') {
            $max_width = max(1, intval(Teamp_WebP_Converter_Settings::get('max_width')));
            $max_height = max(1, intval(Teamp_WebP_Converter_Settings::get('max_height')));
            $editor->resize($max_width, $max_height, false);
        }

        $quality = min(100, max(0, intval(Teamp_WebP_Converter_Settings::get('quality'))));
        $editor->set_quality($quality);

        $webp_file = $this->generate_unique_webp_filename($file);
        $saved = $editor->save($webp_file, 'image/webp');
        if (is_wp_error($saved) || !file_exists($webp_file)) {
            return $upload;
        }

        $upload['file'] = $webp_file;
        $upload['type'] = 'image/webp';
        $upload['url'] = trailingslashit(dirname($upload['url'])) . basename($webp_file);

        if (
            Teamp_WebP_Converter_Settings::get('delete_original') === '1'
            && file_exists($file)
            && $file !== $webp_file
        ) {
            wp_delete_file($file);
        }

        return $upload;
    }

    private function get_allowed_types()
    {
        $allowed_types = Teamp_WebP_Converter_Settings::get('image_types');
        if (!is_array($allowed_types)) {
            $allowed_types = array();
        }

        $allowed_types = array_map('strtolower', $allowed_types);

        if (in_array('jpg', $allowed_types, true) && !in_array('jpeg', $allowed_types, true)) {
            $allowed_types[] = 'jpeg';
        }

        return $allowed_types;
    }

    /**
     * Generate a unique WebP filename by adding a number if the file already exists.
     *
     * @param string $original_file The original file path.
     * @return string The unique WebP file path.
     */
    private function generate_unique_webp_filename($original_file)
    {
        $path_parts = pathinfo($original_file);
        $base_name = strtolower($path_parts['filename']);
        $directory = $path_parts['dirname'];
        $webp_file = $directory . '/' . $base_name . '.webp';
        $counter = 1;

        while (file_exists($webp_file)) {
            $webp_file = $directory . '/' . $base_name . '-' . $counter . '.webp';
            $counter++;
        }

        return $webp_file;
    }
}
