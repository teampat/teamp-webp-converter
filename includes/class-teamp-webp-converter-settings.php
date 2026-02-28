<?php
if (!defined('ABSPATH')) {
    exit;
}

class Teamp_WebP_Converter_Settings
{
    public function add_admin_menu()
    {
        add_options_page(
            'Teamp WebP Converter Settings',
            'Teamp WebP Converter',
            'manage_options',
            'teamp-webp-converter',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings()
    {
        register_setting('swc_settings', 'swc_enabled');
        register_setting('swc_settings', 'swc_quality');
        register_setting('swc_settings', 'swc_delete_original');
        register_setting('swc_settings', 'swc_resize_enabled');
        register_setting('swc_settings', 'swc_max_width');
        register_setting('swc_settings', 'swc_max_height');
        register_setting('swc_settings', 'swc_image_types');
    }

    public function render_settings_page()
    {
        // Check WebP support
        $webp_supported = extension_loaded('imagick') || extension_loaded('gd');
?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <?php if (!$webp_supported): ?>
                <div class="notice notice-error">
                    <p><?php _e('WebP conversion is not supported on your server. Please install Imagick or GD extension with WebP support.', 'teamp-webp-converter'); ?>
                    </p>
                </div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php
                settings_fields('swc_settings');
                do_settings_sections('swc_settings');
                ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Enable WebP Conversion', 'teamp-webp-converter'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="swc_enabled" value="1"
                                    <?php checked(get_option('swc_enabled'), '1'); ?> <?php disabled(!$webp_supported); ?>>
                                <?php _e('Convert new upload images to WebP format', 'teamp-webp-converter'); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Image Quality', 'teamp-webp-converter'); ?></th>
                        <td>
                            <input type="number" name="swc_quality"
                                value="<?php echo esc_attr(get_option('swc_quality', 80)); ?>" min="0" max="100">
                            <p class="description">
                                <?php _e('Set the quality of WebP conversion (0-100)', 'teamp-webp-converter'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Delete Original Image', 'teamp-webp-converter'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="swc_delete_original" value="1"
                                    <?php checked(get_option('swc_delete_original'), '1'); ?>>
                                <?php _e('Delete original image', 'teamp-webp-converter'); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Enable Image Resize', 'teamp-webp-converter'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="swc_resize_enabled" value="1"
                                    <?php checked(get_option('swc_resize_enabled'), '1'); ?>>
                                <?php _e('Resize original image', 'teamp-webp-converter'); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Maximum Width', 'teamp-webp-converter'); ?></th>
                        <td>
                            <input type="number" name="swc_max_width"
                                value="<?php echo esc_attr(get_option('swc_max_width', 1920)); ?>" min="1">
                            <p class="description"><?php _e('Maximum width in pixels', 'teamp-webp-converter'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Maximum Height', 'teamp-webp-converter'); ?></th>
                        <td>
                            <input type="number" name="swc_max_height"
                                value="<?php echo esc_attr(get_option('swc_max_height', 1080)); ?>" min="1">
                            <p class="description"><?php _e('Maximum height in pixels', 'teamp-webp-converter'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Image Types to Convert', 'teamp-webp-converter'); ?></th>
                        <td>
                            <?php
                            // Define available types (only JPG and PNG)
                            $image_types = get_option('swc_image_types', array('jpg', 'jpeg', 'png'));
                            $display_types = array(
                                'jpg' => __('JPG', 'teamp-webp-converter'),
                                'png' => __('PNG', 'teamp-webp-converter')
                            );

                            foreach ($display_types as $type => $label) {
                                $is_checked = in_array($type, $image_types) || ($type === 'jpg' && in_array('jpeg', $image_types));
                            ?>
                                <label style="display: block; margin-bottom: 5px;">
                                    <input type="checkbox" name="swc_image_types[]" value="<?php echo esc_attr($type); ?>"
                                        <?php checked($is_checked); ?>>
                                    <?php echo esc_html($label); ?>
                                </label>
                                <?php
                                // Add hidden input for JPEG when JPG is selected
                                if ($type === 'jpg') {
                                ?>
                                    <input type="hidden" name="swc_image_types[]" value="jpeg" <?php disabled(!$is_checked); ?>>
                            <?php
                                }
                            }
                            ?>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
<?php
    }
}
