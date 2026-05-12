<?php
/**
 * Elementor Dynamic Tags
 * 
 * Registreert Hipsy Event fields in Elementor's Dynamic Tags dropdown.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register Elementor Dynamic Tags
 */
add_action( 'elementor/dynamic_tags/register', 'hipsy_register_elementor_dynamic_tags' );

function hipsy_register_elementor_dynamic_tags( $dynamic_tags ) {
    
    // Check of Elementor Dynamic Tags module bestaat
    if ( ! class_exists( '\Elementor\Core\DynamicTags\Tag' ) ) {
        return;
    }
    
    // Registreer tag group
    $dynamic_tags->register_group(
        'hipsy-events',
        array(
            'title' => 'Hipsy Events'
        )
    );
    
    // Laad tag classes
    require_once __DIR__ . '/elementor-tags.php';
    
    // Registreer tags
    $dynamic_tags->register( new \Hipsy_Event_Title_Tag() );
    $dynamic_tags->register( new \Hipsy_Event_Date_Tag() );
    $dynamic_tags->register( new \Hipsy_Event_Time_Tag() );
    $dynamic_tags->register( new \Hipsy_Event_Location_Tag() );
    $dynamic_tags->register( new \Hipsy_Event_Description_Tag() );
    $dynamic_tags->register( new \Hipsy_Event_Categories_Tag() );
    $dynamic_tags->register( new \Hipsy_Event_Price_Tag() );
    $dynamic_tags->register( new \Hipsy_Event_URL_Tag() );
    $dynamic_tags->register( new \Hipsy_Event_Image_Tag() );
}
