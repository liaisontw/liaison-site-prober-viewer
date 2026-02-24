<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/liaisontw/liaison-site-prober-viewer
 * @since             1.0.0
 * @package           liaison_site_prober_viewer
 *
 * @wordpress-plugin
 * Plugin Name:       liaison site prober viewer DEV
 * Plugin URI:        https://github.com/liaisontw/liaison-site-prober-viewer
 * Description:       Gutenberg Block for viewing logs in posts(out of admin panel)of liaison-site-prober plugin.
 * Version:           1.0.0
 * Author:            liason
 * Author URI:        https://github.com/liaisontw/
 * License: 		  GPLv3 or later  
 * License URI: 	  https://www.gnu.org/licenses/gpl-3.0.html  
 * Text Domain:       liaison-site-prober-viewer
 * Domain Path:       /languages
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function liaisipv_register_block() {
    register_block_type(
    __DIR__ . '/build',
    [
        'render_callback' => 'liaisipv_render_logs_block',
    ]
);
}
add_action( 'init', 'liaisipv_register_block' );

register_activation_hook( __FILE__, 'liaisipv_activation_check' );

register_activation_hook( __FILE__, 'liaisipv_activation_check' );

function liaisipv_activation_check() {

    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

    if ( ! defined( 'LIAISIPR_VERSION' ) ||
         ! version_compare( LIAISIPR_VERSION, '1.2.0', '>=' ) ) {

        deactivate_plugins( plugin_basename( __FILE__ ) );

        add_option( 'liaisipv_dependency_error', true );
    }
}

add_action( 'admin_notices', 'liaisipv_dependency_notice' );

function liaisipv_dependency_notice() {

    if ( get_option( 'liaisipv_dependency_error' ) ) {

        echo '<div class="notice notice-error"><p>';
        echo '<strong>Liaison Site Insight Pro</strong> requires ';
        echo '<strong>Liaison Site Prober</strong> version 1.2.0 or higher.';
        echo '</p></div>';

        delete_option( 'liaisipv_dependency_error' );
    }
}


/**
 * Render function for the Liaison Site Prober Viewer block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block inner content.
 * @param WP_Block $block      Block instance.
 * @return string              Rendered HTML.
 */
function liaisipv_render_logs_block( $attributes, $content, $block ) {
    global $wpdb;

    $cache_key   = 'liaisipv_logs_limit_50';
    $cache_group = 'liaisipv_activity';
    
    $rows = wp_cache_get( $cache_key, $cache_group );

    if ( false === $rows ) {
        $rows = $wpdb->get_results(
            $wpdb->prepare(
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
                LIMIT %d",
                50
            ),
            ARRAY_A
        );

        wp_cache_set( $cache_key, $rows, $cache_group, HOUR_IN_SECONDS );
    }

    if ( empty( $rows ) ) {
        return '<p>' . esc_html__( 'No logs found.', 'liaison-site-prober-viewer' ) . '</p>';
    }

    $wrapper_attributes = get_block_wrapper_attributes();

    ob_start();
    ?>
    <div <?php echo wp_kses_data( $wrapper_attributes ); ?>>
        <table class="splv-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Date', 'liaison-site-prober-viewer' ); ?></th>
                    <th><?php esc_html_e( 'User', 'liaison-site-prober-viewer' ); ?></th>
                    <th><?php esc_html_e( 'IP', 'liaison-site-prober-viewer' ); ?></th>
                    <th><?php esc_html_e( 'Action', 'liaison-site-prober-viewer' ); ?></th>
                    <th><?php esc_html_e( 'Type', 'liaison-site-prober-viewer' ); ?></th>
                    <th><?php esc_html_e( 'Description', 'liaison-site-prober-viewer' ); ?></th>
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
