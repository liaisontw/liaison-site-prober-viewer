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
 * Description:       Gutenberg Block for viewing logs in posts(out of admin panel)of liaison-site-prober plugin.
 * Version:           1.0.0
 * Author:            liason
 * Author URI:        https://github.com/liaisontw/
 * License: 		  GPLv3 or later  
 * License URI: 	  https://www.gnu.org/licenses/gpl-3.0.html  
 * Text Domain:       liaison-site-prober-viewer
 * Domain Path:       /languages
 */


function liaisipv_register_block() {
    register_block_type(
    __DIR__ . '/build',
    [
        'render_callback' => 'liaisipv_render_logs_block',
    ]
);
}
add_action( 'init', 'liaisipv_register_block' );


function liaisipv_render_logs_block( $attributes, $content, $block ) {
    global $wpdb;

    $rows = $wpdb->get_results(
        "SELECT
            id,
            created_at,
            user_id,
            ip,
            action,
            object_type,
            description
         FROM {$wpdb->wpsp_activity}
         ORDER BY created_at DESC
         LIMIT 50",
        ARRAY_A
    );

    if ( empty( $rows ) ) {
        return '<p>No logs found.</p>';
    }

    //loading from build/style-index.css
    $wrapper_attributes = get_block_wrapper_attributes();

    ob_start();
    ?>
    <div <?php echo $wrapper_attributes; ?>>
        <table class="splv-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>IP</th>
                    <th>Action</th>
                    <th>Type</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $rows as $row ) : ?>
                    <tr>
                        <td><?php echo esc_html( $row['created_at'] ); ?></td>
                        <td><?php echo esc_html( $row['user_id'] ); ?></td>
                        <td><?php echo esc_html( $row['ip'] ); ?></td>
                        <td><?php echo esc_html( $row['action'] ); ?></td>
                        <td><?php echo esc_html( $row['object_type'] ); ?></td>
                        <td><?php echo esc_html( $row['description'] ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}
