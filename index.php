<?php
/*
Plugin Name: FacetWP - Alpha
Plugin URI: https://facetwp.com/
Description: Alphabetical letter facet
Version: 1.0.4
Author: FacetWP, LLC
Author URI: https://facetwp.com/
GitHub Plugin URI: https://github.com/FacetWP/facetwp-alpha

Copyright 2016 FacetWP, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


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
