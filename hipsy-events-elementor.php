<?php
/**
 * Hipsy Events for Elementor
 *
 * @package       HIPSY_ELEMENTOR
 * @wordpress-plugin
 * Plugin Name:   Hipsy Events for Elementor
 * Plugin URI:    https://hipsy.nl
 * Description:   Elementor add-on voor Hipsy Events Core. Voegt Hipsy event widgets en dynamic tags toe aan Elementor.
 * Version:       1.0.2
 * Author:        Young Soul Business
 * Author URI:    https://www.youngsoulbusiness.com
 * Text Domain:   hipsy-events-elementor
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'hpelementor' ) ) {
    // Create a helper function for easy SDK access.
    function hpelementor() {
        global $hpelementor;

        if ( ! isset( $hpelementor ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';

            $hpelementor = fs_dynamic_init( array(
                'id'                  => '29370',
                'slug'                => 'hipsy-plugin-for-elementor',
                'premium_slug'        => 'hipsy-plugin-for-elementor-pro',
                'type'                => 'plugin',
                'public_key'          => 'pk_83e3d59fe7d4e02ac6958ad5f89df',
                'is_premium'          => true,
                'is_premium_only'     => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'is_org_compliant'    => true,
                // Automatically removed in the free version. If you're not using the
                // auto-generated free version, delete this line before uploading to wp.org.
                'wp_org_gatekeeper'   => 'OA7#BoRiBNqdf52FvzEf!!074aRLPs8fspif$7K1#4u4Csys1fQlCecVcUTOs2mcpeVHi#C2j9d09fOTvbC0HloPT7fFee5WdS3G',
                'trial'               => array(
                    'days'               => 7,
                    'is_require_payment' => false,
                ),
                'menu'                => array(
                    'first-path'     => 'plugins.php',
                    'contact'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $hpelementor;
    }

    // Init Freemius.
    hpelementor();
    // Signal that SDK was initiated.
    do_action( 'hpelementor_loaded' );
}

if ( ! defined( 'HIPSY_ELEMENTOR_VERSION' ) ) define( 'HIPSY_ELEMENTOR_VERSION', '1.0.2' );
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
