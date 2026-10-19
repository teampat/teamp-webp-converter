<?php
if (!defined('ABSPATH')) {
    exit;
}

class Teamp_WebP_Converter_Settings
{
    const OPTION_GROUP = 'teamp_webp_settings';
    const OPTION_ENABLED = 'teamp_webp_enabled';
    const OPTION_QUALITY = 'teamp_webp_quality';
    const OPTION_DELETE_ORIGINAL = 'teamp_webp_delete_original';
    const OPTION_RESIZE_ENABLED = 'teamp_webp_resize_enabled';
    const OPTION_MAX_WIDTH = 'teamp_webp_max_width';
    const OPTION_MAX_HEIGHT = 'teamp_webp_max_height';
    const OPTION_IMAGE_TYPES = 'teamp_webp_image_types';

    public static function defaults()
    {
        return array(
            'enabled' => '0',
            'quality' => '75',
            'delete_original' => '1',
            'resize_enabled' => '1',
            'max_width' => '2048',
            'max_height' => '2048',
            'image_types' => array('jpg', 'jpeg', 'png'),
        );
    }

    public static function option_map()
    {
        return array(
            'enabled' => self::OPTION_ENABLED,
            'quality' => self::OPTION_QUALITY,
            'delete_original' => self::OPTION_DELETE_ORIGINAL,
            'resize_enabled' => self::OPTION_RESIZE_ENABLED,
            'max_width' => self::OPTION_MAX_WIDTH,
            'max_height' => self::OPTION_MAX_HEIGHT,
            'image_types' => self::OPTION_IMAGE_TYPES,
        );
    }

    public static function legacy_option_map()
    {
        return array(
            'enabled' => 'swc_enabled',
            'quality' => 'swc_quality',
            'delete_original' => 'swc_delete_original',
            'resize_enabled' => 'swc_resize_enabled',
            'max_width' => 'swc_max_width',
            'max_height' => 'swc_max_height',
            'image_types' => 'swc_image_types',
        );
    }

    public static function migrate_legacy_options()
    {
        $legacy = self::legacy_option_map();

        foreach (self::option_map() as $key => $option_name) {
            $legacy_value = get_option($legacy[$key], null);
            if (null === $legacy_value) {
                continue;
            }

            if (null === get_option($option_name, null)) {
                add_option($option_name, $legacy_value);
            }

            delete_option($legacy[$key]);
        }
    }

    public static function get($key)
    {
        $defaults = self::defaults();
        $option_map = self::option_map();

        if (!isset($defaults[$key], $option_map[$key])) {
            return null;
        }

        return get_option($option_map[$key], $defaults[$key]);
    }

    public static function add_default_options()
    {
        $defaults = self::defaults();

        foreach (self::option_map() as $key => $option_name) {
            add_option($option_name, $defaults[$key]);
        }
    }

    public function add_admin_menu()
    {
        add_options_page(
            __('Teamp WebP Converter Settings', 'teamp-webp-converter'),
            __('Teamp WebP Converter', 'teamp-webp-converter'),
            'manage_options',
            'teamp-webp-converter',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings()
    {
        $checkbox_args = array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
        );

        register_setting(self::OPTION_GROUP, self::OPTION_ENABLED, array_merge($checkbox_args, array(
            'default' => self::defaults()['enabled'],
        )));
        register_setting(self::OPTION_GROUP, self::OPTION_QUALITY, array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_quality'),
            'default' => self::defaults()['quality'],
        ));
        register_setting(self::OPTION_GROUP, self::OPTION_DELETE_ORIGINAL, array_merge($checkbox_args, array(
            'default' => self::defaults()['delete_original'],
        )));
        register_setting(self::OPTION_GROUP, self::OPTION_RESIZE_ENABLED, array_merge($checkbox_args, array(
            'default' => self::defaults()['resize_enabled'],
        )));
        register_setting(self::OPTION_GROUP, self::OPTION_MAX_WIDTH, array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_dimension'),
            'default' => self::defaults()['max_width'],
        ));
        register_setting(self::OPTION_GROUP, self::OPTION_MAX_HEIGHT, array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_dimension'),
            'default' => self::defaults()['max_height'],
        ));
        register_setting(self::OPTION_GROUP, self::OPTION_IMAGE_TYPES, array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_image_types'),
            'default' => self::defaults()['image_types'],
        ));
    }

    public function sanitize_checkbox($value)
    {
        return ($value === '1' || $value === 1 || $value === true) ? '1' : '0';
    }

    public function sanitize_quality($value)
    {
        return (string) min(100, max(0, intval($value)));
    }

    public function sanitize_dimension($value)
    {
        return (string) max(1, intval($value));
    }

    public function sanitize_image_types($types)
    {
        if (!is_array($types)) {
            return array();
        }

        $allowed = array('jpg', 'jpeg', 'png');
        $types = array_map('strtolower', $types);
        $types = array_values(array_intersect($types, $allowed));

        if (in_array('jpg', $types, true) && !in_array('jpeg', $types, true)) {
            $types[] = 'jpeg';
        }

        return array_values(array_unique($types));
    }

    public function render_settings_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $webp_supported = Teamp_WebP_Converter::supports_webp();
        $image_types = self::get('image_types');
        $display_types = array(
            'jpg' => __('JPG', 'teamp-webp-converter'),
            'png' => __('PNG', 'teamp-webp-converter'),
        );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <?php if (!$webp_supported): ?>
                <div class="notice notice-error">
                    <p><?php esc_html_e('WebP conversion is not supported on your server. Please install Imagick or GD extension with WebP support.', 'teamp-webp-converter'); ?></p>
                </div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php
                settings_fields(self::OPTION_GROUP);
                do_settings_sections(self::OPTION_GROUP);
                ?>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e('Enable WebP Conversion', 'teamp-webp-converter'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo esc_attr(self::OPTION_ENABLED); ?>" value="1"
                                    <?php checked(self::get('enabled'), '1'); ?> <?php disabled(!$webp_supported); ?>>
                                <?php esc_html_e('Convert new upload images to WebP format', 'teamp-webp-converter'); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e('Image Quality', 'teamp-webp-converter'); ?></th>
                        <td>
                            <input type="number" name="<?php echo esc_attr(self::OPTION_QUALITY); ?>"
                                value="<?php echo esc_attr(self::get('quality')); ?>" min="0" max="100">
                            <p class="description"><?php esc_html_e('Set the quality of WebP conversion (0-100)', 'teamp-webp-converter'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e('Delete Original Image', 'teamp-webp-converter'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo esc_attr(self::OPTION_DELETE_ORIGINAL); ?>" value="1"
                                    <?php checked(self::get('delete_original'), '1'); ?>>
                                <?php esc_html_e('Delete original image', 'teamp-webp-converter'); ?>
                            </label>
                            <p class="description"><?php esc_html_e('This permanently deletes the uploaded JPG or PNG after a successful conversion.', 'teamp-webp-converter'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e('Enable Image Resize', 'teamp-webp-converter'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo esc_attr(self::OPTION_RESIZE_ENABLED); ?>" value="1"
                                    <?php checked(self::get('resize_enabled'), '1'); ?>>
                                <?php esc_html_e('Resize images larger than the maximum dimensions', 'teamp-webp-converter'); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e('Maximum Width', 'teamp-webp-converter'); ?></th>
                        <td>
                            <input type="number" name="<?php echo esc_attr(self::OPTION_MAX_WIDTH); ?>"
                                value="<?php echo esc_attr(self::get('max_width')); ?>" min="1">
                            <p class="description"><?php esc_html_e('Maximum width in pixels', 'teamp-webp-converter'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e('Maximum Height', 'teamp-webp-converter'); ?></th>
                        <td>
                            <input type="number" name="<?php echo esc_attr(self::OPTION_MAX_HEIGHT); ?>"
                                value="<?php echo esc_attr(self::get('max_height')); ?>" min="1">
                            <p class="description"><?php esc_html_e('Maximum height in pixels', 'teamp-webp-converter'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e('Image Types to Convert', 'teamp-webp-converter'); ?></th>
                        <td>
                            <input type="hidden" name="<?php echo esc_attr(self::OPTION_IMAGE_TYPES); ?>[]" value="">
                            <?php foreach ($display_types as $type => $label) :
                                $is_checked = in_array($type, (array) $image_types, true)
                                    || ($type === 'jpg' && in_array('jpeg', (array) $image_types, true));
                                ?>
                                <label style="display: block; margin-bottom: 5px;">
                                    <input type="checkbox" name="<?php echo esc_attr(self::OPTION_IMAGE_TYPES); ?>[]" value="<?php echo esc_attr($type); ?>"
                                        <?php checked($is_checked); ?>>
                                    <?php echo esc_html($label); ?>
                                </label>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
