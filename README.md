# Teamp WebP Converter

A WordPress plugin that automatically converts uploaded images to WebP format with customizable options for better performance and smaller file sizes.

## Features

- Automatic conversion of uploaded images to WebP format
- Customizable quality settings (default: 75%)
- Option to delete original images after conversion (enabled by default)
- Image resizing capabilities with max width/height settings (enabled by default)
- Supports JPG, JPEG, and PNG source formats
- Simple and intuitive settings page
- Lightweight and efficient

## Requirements

- WordPress 6.7 or higher
- PHP 7.4 or higher
- PHP Imagick or GD extension with WebP support

## Installation

1. Download the plugin zip file
2. Go to WordPress admin panel > Plugins > Add New
3. Click "Upload Plugin" and select the downloaded zip file
4. Click "Install Now" and then "Activate"

## Configuration

1. Navigate to Settings > Teamp WebP Converter
2. Configure the following options:
   - Enable/Disable automatic conversion
   - Set WebP quality (0-100)
   - Choose whether to delete original images
   - Enable/disable image resizing
   - Set maximum dimensions for resized images
   - Select image types to convert

## Default Settings

- Automatic conversion: Disabled
- Quality: 75%
- Delete Original Images: Enabled
- Image Resize: Enabled
- Max Width: 2048px
- Max Height: 2048px
- Supported formats: JPG, JPEG, PNG

## How It Works

1. When an image is uploaded to WordPress, the plugin automatically detects it
2. If the image matches the configured formats, it's converted to WebP
3. The converted image maintains the original filename with .webp extension
4. Original images are optionally deleted based on settings
5. Images are optionally resized based on maximum dimension settings

## Benefits

- Reduced file sizes (typically 25-35% smaller than JPEG)
- Improved page load times
- Better SEO performance
- Reduced bandwidth usage
- Modern image format support

## Support

For support, feature requests, or bug reports, please visit our [GitHub repository](https://github.com/teampat/teamp-webp-converter) or create an issue there.

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed by [teampat](https://github.com/teampat)

## Changelog

### 1.0.1

- Save unchecked settings correctly
- Use consistent default values across the plugin
- Convert with the WordPress image editor so failed conversions keep the original file
- Sanitize settings and check actual WebP support
- Remove leftover settings on uninstall

### 1.0.0

- Initial release
- Basic WebP conversion functionality
- Settings page implementation
- Image resize capabilities
- Original image deletion option
