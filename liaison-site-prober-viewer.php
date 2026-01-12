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

//1. Copy package.json from wordpress block example
//2. node -v, v24.12.0 (>v18.x)
//3. npm install
//4. npm run build





function liaisipv_register_block() {
    register_block_type(
    __DIR__ . '/build',
    [
        'render_callback' => 'liaisipv_render_logs_block',
    ]
);
}
add_action( 'init', 'liaisipv_register_block' );

/*
function liaisipv_render_logs_block( $attributes ) {
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
        ORDER BY created_at 
        DESC LIMIT 50",
        ARRAY_A
    );

    if ( empty( $rows ) ) {
        return '<p>No logs found.</p>';
    }

    ob_start();
    ?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th class="manage-column column-date"       >Date</th>
                <th class="manage-column column-user"       >User</th>
                <th class="manage-column column-ip"         >IP</th>
                <th class="manage-column column-action"     >Action</th>
                <th class="manage-column column-object"     >Type</th>
                <th class="manage-column column-description">Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $rows as $row ) : ?>
                <tr>
                    <td class="column-date">       <?php echo esc_html( $row['created_at'] ); ?></td>
                    <td class="column-user">       <?php echo esc_html( $row['user_id'] ); ?></td>
                    <td class="column-ip">         <?php echo esc_html( $row['ip'] ); ?></td>
                    <td class="column-action">     <?php echo esc_html( $row['action'] ); ?></td>
                    <td class="column-object">     <?php echo esc_html( $row['object_type'] ); ?></td>
                    <td class="column-description"><?php echo esc_html( $row['description'] ); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    return ob_get_clean();
}
    */

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
