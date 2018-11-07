<?php
/*
Plugin Name: FacetWP - Alpha
Description: Filter by first letter
Version: 1.2.4
Author: FacetWP, LLC
Author URI: https://facetwp.com/
GitHub URI: facetwp/facetwp-alpha
*/

defined( 'ABSPATH' ) or exit;


/**
 * FacetWP registration hook
 */
add_filter( 'facetwp_facet_types', function( $types ) {
    include( dirname( __FILE__ ) . '/class-alpha.php' );
    $types['alpha'] = new FacetWP_Facet_Alpha_Addon();
    return $types;
} );
