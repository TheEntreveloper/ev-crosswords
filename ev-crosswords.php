<?php
/**
 * Plugin Name: EV Crosswords
 * Plugin URI: https://entreveloper.com/
 * Description: Crossword Plugin for anyone to easily create and add Crosswords to their Wordpress website
 * Version: 1.0.3
 * Author: The Entreveloper
 * Author URI: https://github.com/TheEntreveloper
 * License: GPLv2 or later
 * Text Domain: ev-crosswords
 * Domain Path: /languages
 * @Package EvCrosswords
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2022-2023 Entreveloper.com
*/
if ( ! defined( 'ABSPATH' ) ) {
    echo(wp_kses_data('Perhaps another day...'));
    exit; // Exit if accessed directly.
}
define( 'EVCWV_PLUGIN_VERSION', '1.0.0' );
// Define EVCWV_PLUGIN_FILE.
if ( ! defined( 'EVCWV_PLUGIN' ) ) {
    define( 'EVCWV_PLUGIN', __FILE__ );
}
if ( ! defined( 'EVCWV_PLUGIN_DIR' ) ) {
    define( 'EVCWV_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
}
if ( ! class_exists('EvFormsClass')) {
    include_once dirname( __FILE__ ) . '/classes/EvCwPluginLauncher.php';
}

function evcwv() {
    return EvCwPluginLauncher::instantiatePlugin();
}

$GLOBALS['evcwv'] = evcwv();
