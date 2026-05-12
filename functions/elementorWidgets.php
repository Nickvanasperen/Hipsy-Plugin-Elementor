<?php
/** Elementor widget loader for Hipsy Events for Elementor. */
if ( ! defined( 'ABSPATH' ) ) exit;

// FIX: defined() guards zodat dubbele activatie geen fatal error geeft
if ( ! defined( 'HIPSY_EW_PATH' ) )    define( 'HIPSY_EW_PATH',    HIPSY_ELEMENTOR_PATH );
if ( ! defined( 'HIPSY_EW_URL' ) )     define( 'HIPSY_EW_URL',     HIPSY_ELEMENTOR_URL );
if ( ! defined( 'HIPSY_EW_VERSION' ) ) define( 'HIPSY_EW_VERSION', HIPSY_ELEMENTOR_VERSION );

add_action( 'wp_enqueue_scripts', function() {
    $emoji_css = 'img.emoji{height:1em!important;width:1em!important;max-width:1em!important;min-width:unset!important;max-height:1em!important;vertical-align:-0.1em!important;display:inline!important;margin:0 0.05em!important;padding:0!important;box-shadow:none!important;border:none!important;background:none!important;border-radius:0!important;float:none!important;}';
    foreach ( ['wp-block-library', 'elementor-frontend', 'elementor-post'] as $handle ) {
        if ( wp_style_is( $handle, 'enqueued' ) || wp_style_is( $handle, 'registered' ) ) {
            wp_add_inline_style( $handle, $emoji_css );
            break;
        }
    }
    if ( ! wp_style_is( 'hipsy-emoji-fix', 'registered' ) ) {
        wp_register_style( 'hipsy-emoji-fix', false, [], HIPSY_EW_VERSION );
        wp_enqueue_style( 'hipsy-emoji-fix' );
        wp_add_inline_style( 'hipsy-emoji-fix', $emoji_css );
    }
});

function hipsy_ew_enqueue_swiper() {
    if ( ! wp_script_is( 'hipsy-swiper', 'enqueued' ) ) {
        wp_enqueue_style( 'hipsy-swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.css', [], '11.0.5' );
        wp_enqueue_script( 'hipsy-swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js', [], '11.0.5', true );
    }
}
add_action( 'elementor/editor/after_enqueue_scripts', 'hipsy_ew_enqueue_swiper' );

require_once HIPSY_EW_PATH . 'includes/helpers.php';

add_action( 'elementor/widgets/register', function( $manager ) {
    foreach ( [
        'hipsy-events-grid.php'        => 'Hipsy_Events_Grid_Widget',
        'hipsy-event-titel.php'        => 'Hipsy_Event_Titel_Widget',
        'hipsy-event-datum.php'        => 'Hipsy_Event_Datum_Widget',
        'hipsy-event-tijd.php'         => 'Hipsy_Event_Tijd_Widget',
        'hipsy-event-locatie.php'      => 'Hipsy_Event_Locatie_Widget',
        'hipsy-event-beschrijving.php' => 'Hipsy_Event_Beschrijving_Widget',
        'hipsy-event-afbeelding.php'   => 'Hipsy_Event_Afbeelding_Widget',
        'hipsy-event-tickets.php'      => 'Hipsy_Event_Tickets_Widget',
        'hipsy-event-ticketknop.php'   => 'Hipsy_Event_Ticketknop_Widget',
        'hipsy-zoek-filter.php'        => 'Hipsy_Zoek_Filter_Widget',
        'hipsy-reviews.php'            => 'Hipsy_Reviews_Widget',
    ] as $file => $class ) {
        $path = HIPSY_EW_PATH . 'widgets/' . $file;
        if ( file_exists( $path ) ) {
            require_once $path;
            if ( class_exists( $class ) ) {
                $manager->register( new $class() );
            }
        }
    }
});
