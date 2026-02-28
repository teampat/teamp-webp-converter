<?php
if (!defined('ABSPATH')) {
    exit;
}

class Teamp_WebP_Converter
{
    private $settings;

    public function init()
    {
        $this->settings = new Teamp_WebP_Converter_Settings();

        // Add hooks for image upload
        add_filter('wp_handle_upload', array($this, 'convert_to_webp'), 10, 2);

        // Add admin menu
        add_action('admin_menu', array($this->settings, 'add_admin_menu'));

        // Register settings
        add_action('admin_init', array($this->settings, 'register_settings'));
    }

    public function convert_to_webp($upload, $context)
    {
        // Check if conversion is enabled
        if (get_option('swc_enabled') !== '1') {
            return $upload;
        }

        $file = $upload['file'];
        $file_type = wp_check_filetype($file);
        $allowed_types = get_option('swc_image_types', array('jpg', 'png'));
        // Add 'jpeg' if 'jpg' is selected
        if (in_array('jpg', $allowed_types) && !in_array('jpeg', $allowed_types)) {
            $allowed_types[] = 'jpeg';
        }

        // Check if file type is allowed for conversion
        if (!in_array(strtolower($file_type['ext']), $allowed_types)) {
            return $upload;
        }

        // Get image quality
        $quality = get_option('swc_quality', 75);
        $delete_original = get_option('swc_delete_original') === '1';
        $resize_enabled = get_option('swc_resize_enabled') === '1';
        $original_file = $file;

        // Check if resize is enabled
        if ($resize_enabled) {
            $max_width = get_option('swc_max_width', 2048);
            $max_height = get_option('swc_max_height', 2048);

            // Resize image if needed
            $image = wp_get_image_editor($file);
            if (!is_wp_error($image)) {
                $image->resize($max_width, $max_height, false);
                $image->save($file);
            }
        }

        // Generate WebP filename with number if file exists
        $webp_file = $this->generate_unique_webp_filename($file);

        if (extension_loaded('imagick')) {
            $imagick = new Imagick($file);
            $imagick->setImageFormat('webp');
            $imagick->setImageCompressionQuality($quality);
            $imagick->writeImage($webp_file);
        } elseif (extension_loaded('gd')) {
            $image = imagecreatefromstring(file_get_contents($file));
            imagewebp($image, $webp_file, $quality);
            imagedestroy($image);
        }

        // Update file information
        if (file_exists($webp_file)) {
            $upload['file'] = $webp_file;
            $upload['type'] = 'image/webp';
            $upload['url'] = str_replace(basename($file), basename($webp_file), $upload['url']);

            // Delete original image if option is enabled
            if ($delete_original) {
                @unlink($file);
            }
        }

        return $upload;
    }

    /**
     * Generate a unique WebP filename by adding a number if the file already exists
     *
     * @param string $original_file The original file path
     * @return string The unique WebP file path
     */
    private function generate_unique_webp_filename($original_file)
    {
        $path_parts = pathinfo($original_file);
        $base_name = strtolower($path_parts['filename']);
        $directory = $path_parts['dirname'];
        $extension = '.webp';

        $webp_file = $directory . '/' . $base_name . $extension;
        $counter = 1;

        // Check if file exists and add number until we find a unique filename
        while (file_exists($webp_file)) {
            $webp_file = $directory . '/' . $base_name . '-' . $counter . $extension;
            $counter++;
        }

        return $webp_file;
    }
}
