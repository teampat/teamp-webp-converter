=== Teamp WebP Converter ===
Contributors: teampat
Tags: webp, images, optimization, media, performance
Requires at least: 6.7
Tested up to: 7.1
Stable tag: 1.0.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Convert newly uploaded JPG and PNG images to WebP on your own server.

== Description ==

Teamp WebP Converter turns new JPG and PNG uploads into WebP files on the same server that runs WordPress. Images are not sent to an external service.

Conversion uses the image editor built into WordPress, so it runs with Imagick or GD when that library can write WebP. If conversion fails, the original upload is left in place.

You can set the WebP quality, resize images that are larger than a maximum width or height, and choose whether the original file is deleted after a successful conversion. Deleting the original is enabled by default and cannot be undone.

The plugin converts files during upload. It does not scan or convert images that are already in the Media Library.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`, or install the zip from the Plugins screen.
2. Activate Teamp WebP Converter through the Plugins screen.
3. Open Settings, then Teamp WebP Converter.
4. Enable conversion and save the settings before uploading a new image.

== Frequently Asked Questions ==

= Does this plugin upload images to another service? =

No. Conversion happens on your server with WordPress, Imagick, or GD.

= What happens to the original file? =

When "Delete original image" is enabled, the uploaded JPG or PNG is permanently deleted after WebP conversion succeeds. Disable that setting to keep the original file.

= What if the server cannot create WebP files? =

The settings screen shows an error, and uploads stay in their original format. A failed conversion also keeps the original file.

= Which files are converted? =

New JPG, JPEG, and PNG uploads, when those types are selected. Files that are already WebP are skipped. Images already in the Media Library are not converted.

== Changelog ==

= 1.0.2 =
* Prepare the plugin for the WordPress.org directory.
* Store settings with a unique option prefix and migrate older settings automatically.
* Warn that deleting the original image is permanent.

= 1.0.1 =
* Save unchecked settings correctly.
* Use one set of default values.
* Keep the original file when conversion fails.
* Sanitize settings and check real WebP support.
* Remove settings when the plugin is uninstalled.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.2 =
Settings from earlier versions are moved to new option names automatically.
