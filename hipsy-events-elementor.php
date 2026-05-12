<?php
/**
 * Hipsy Events for Elementor
 *
 * @package       HIPSY_ELEMENTOR
 * @wordpress-plugin
 * Plugin Name:   Hipsy Events for Elementor
 * Plugin URI:    https://hipsy.nl
 * Description:   Elementor add-on voor Hipsy Events Core. Voegt Hipsy event widgets en dynamic tags toe aan Elementor.
 * Version:       1.0.1
 * Author:        How About Yes
 * Author URI:    https://howaboutyes.com
 * Text Domain:   hipsy-events-elementor
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'HIPSY_ELEMENTOR_VERSION' ) ) define( 'HIPSY_ELEMENTOR_VERSION', '1.0.1' );
if ( ! defined( 'HIPSY_ELEMENTOR_PATH' ) ) define( 'HIPSY_ELEMENTOR_PATH', plugin_dir_path( __FILE__ ) );
if ( ! defined( 'HIPSY_ELEMENTOR_URL' ) ) define( 'HIPSY_ELEMENTOR_URL', plugin_dir_url( __FILE__ ) );

function hipsy_elementor_admin_notice( $message, $type = 'warning' ) {
    add_action( 'admin_notices', function() use ( $message, $type ) {
        echo '<div class="notice notice-' . esc_attr( $type ) . '"><p><strong>Hipsy Events for Elementor:</strong> ' . wp_kses_post( $message ) . '</p></div>';
    } );
}

add_action( 'plugins_loaded', function() {
    if ( ! defined( 'HIPSY_EVENTS_CORE_VERSION' ) ) {
        hipsy_elementor_admin_notice( 'Hipsy Events Core is vereist. Activeer eerst de core plugin.' );
        return;
    }

    if ( ! did_action( 'elementor/loaded' ) ) {
        hipsy_elementor_admin_notice( 'Elementor is vereist. Activeer Elementor om de Hipsy widgets te gebruiken.', 'info' );
        return;
    }

    if ( file_exists( HIPSY_ELEMENTOR_PATH . 'functions/elementorWidgets.php' ) ) {
        require_once HIPSY_ELEMENTOR_PATH . 'functions/elementorWidgets.php';
    }

    if ( file_exists( HIPSY_ELEMENTOR_PATH . 'integrations/elementor/elementor-dynamic-tags.php' ) ) {
        require_once HIPSY_ELEMENTOR_PATH . 'integrations/elementor/elementor-dynamic-tags.php';
    }

    if ( file_exists( HIPSY_ELEMENTOR_PATH . 'integrations/elementor/elementor-tags.php' ) ) {
        require_once HIPSY_ELEMENTOR_PATH . 'integrations/elementor/elementor-tags.php';
    }

    if ( file_exists( HIPSY_ELEMENTOR_PATH . 'integrations/elementor/filter-bar-widget.php' ) ) {
        add_action( 'elementor/widgets/register', function( $widgets_manager ) {
            require_once HIPSY_ELEMENTOR_PATH . 'integrations/elementor/filter-bar-widget.php';
            if ( class_exists( 'Hipsy_Filter_Bar_Widget' ) ) {
                $widgets_manager->register( new Hipsy_Filter_Bar_Widget() );
            }
        } );
    }
}, 20 );
