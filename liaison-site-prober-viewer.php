<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/liaisontw
 * @since             1.0.0
 * @package           liaison_site_prober_viewer
 *
 * @wordpress-plugin
 * Plugin Name:       liaison site prober viewer
 * Plugin URI:        https://github.com/liaisontw/
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            liason
 * Author URI:        https://github.com/liaisontw/
 * License: 		  GPLv3 or later  
 * License URI: 	  https://www.gnu.org/licenses/gpl-3.0.html  
 * Text Domain:       liaison-site-prober-viewer
 * Domain Path:       /languages
 */

//1. Copy package.json from wordpress block example
//2. node -v, v24.12.0 (>v18.x)
//3. npm install
//4. npm run build





function liaisipv_register_block() {
    register_block_type( __DIR__ . '/build'  );
}
add_action( 'init', 'liaisipv_register_block' );
