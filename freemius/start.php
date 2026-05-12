<?php
/**
 * Minimal Freemius SDK loader placeholder.
 * Replace this directory with the full official Freemius WordPress SDK before production release.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'fs_dynamic_init' ) ) {
    function fs_dynamic_init( $module ) {
        return (object) array(
            'module' => $module,
            'is_plan' => function() { return false; },
            'can_use_premium_code' => function() { return false; },
        );
    }
}
