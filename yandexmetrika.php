<?php
/**
 * Plugin Name: Simple Yandex Metrika
 * Plugin URI:
 * Description: Enables <a href="https://metrika.yandex.com">Yandex Metrika</a> on all pages.
 * Version:     1.0.0
 * Author:      hayk
 * Author URI:  https://hayk.500plus.org/
 * Text Domain: simple-yandexmetrika
 * Domain Path: /languages
 * License:     GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * Simple Yandex Metrika is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Simple Yandex Metrika is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Simple Yandex Metrika. If not, see https://www.gnu.org/licenses/gpl-3.0.txt.
 *
 */

function activate_simple_yandexmetrika() {
	if (!get_option('sym_tracking_id')) {
		add_option('sym_tracking_id', 'XXXXX');
	}
}

function deactive_simple_yandexmetrika() {

}

function uninstall_simple_yandexmetrika() {
	delete_option('sym_tracking_id');
}

function admin_init_simple_yandexmetrika() {
	register_setting('simple-yandexmetrika', 'sym_tracking_id');
}

function admin_menu_simple_yandexmetrika() {
	add_options_page('Simple Yandex Metrika', 'Simple Yandex Metrika', 'manage_options', 'simple-yandexmetrika', 'options_page_simple_yandexmetrika');
}

function options_page_simple_yandexmetrika() {
	include ( plugin_dir_path(__FILE__) . 'options.php' );
}

function simple_yandexmetrika() {

	if ($sym_tracking_id = get_option('sym_tracking_id')) {

		$enqueue_script = 'https://mc.yandex.ru/metrika/tag.js';

		$inline_script = <<<EOT
	window["ym"]=window["ym"]||function(){(window["ym"].a=window["ym"].a||[]).push(arguments)};
	window["ym"].l=1*new Date();
	ym(%s, "init", {
		clickmap:true,
		trackLinks:true,
		accurateTrackBounce:true
	});
EOT;

		$inline_noscript = '<noscript><div><img src="https://mc.yandex.ru/watch/%s" style="position:absolute; left:-9999px;" alt="" /></div></noscript>';
		wp_enqueue_script('simple_yandexmetrika', $enqueue_script);
		wp_add_inline_script('simple_yandexmetrika', sprintf($inline_script, $sym_tracking_id), 'before');
	}

}

register_activation_hook(__FILE__, 'activate_simple_yandexmetrika');
register_deactivation_hook(__FILE__, 'deactive_simple_yandexmetrika');

if (is_admin()) {
	add_action('admin_init', 'admin_init_simple_yandexmetrika');
	add_action('admin_menu', 'admin_menu_simple_yandexmetrika');
}

if (!function_exists('wp_get_current_user')) {
	include ( ABSPATH . 'wp-includes/pluggable.php' );
}

// Do not add analytics code if:
// - current request is not for an administrative interface page;
// - the query is not for a post or page preview;
// - current user is a site admin.
if (!is_admin() && !is_preview() && !is_super_admin()) {
	add_action('wp_head', 'simple_yandexmetrika');
}
