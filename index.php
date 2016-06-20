<?php
/*
Plugin Name: FacetWP - Alpha
Plugin URI: https://facetwp.com/
Description: Alphabetical letter facet
Version: 1.1
Author: FacetWP, LLC
GitHub URI: facetwp/facetwp-alpha
*/

defined( 'ABSPATH' ) or exit;

/**
 * WordPress init hook
 */
add_action( 'init' , 'fwpalpha_init' );


/**
 * Intialize facet registration and any assets
 */
function fwpalpha_init() {
    add_filter( 'facetwp_facet_types', 'fwpalpha_facet_types' );
}


/**
 * Register the facet type
 */
function fwpalpha_facet_types( $facet_types ) {
    include( dirname( __FILE__ ) . '/facet-alpha.php' );
    $facet_types['alpha'] = new FacetWP_Facet_Alpha();
    return $facet_types;
}
