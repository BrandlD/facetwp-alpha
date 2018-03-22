<?php
/*
Plugin Name: FacetWP - Alpha
Description: Alphabetical letter facet
Version: 1.2.3
Author: FacetWP, LLC
Author URI: https://facetwp.com/
GitHub URI: facetwp/facetwp-alpha
*/

defined( 'ABSPATH' ) or exit;


/**
 * FacetWP registration hook
 */
function fwp_alpha_facet( $facet_types ) {
    $facet_types['alpha'] = new FacetWP_Facet_Alpha();
    return $facet_types;
}
add_filter( 'facetwp_facet_types', 'fwp_alpha_facet' );


/**
 * Alpha facet class
 */
class FacetWP_Facet_Alpha
{

    function __construct() {
        $this->label = __( 'Alphabet', 'fwp' );
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {
        global $wpdb;

        $output = '';
        $facet = $params['facet'];
        $selected_values = (array) $params['selected_values'];

        // Simulate "OR" mode (ignore this facet's selection)
        if ( isset( FWP()->or_values ) && ( 1 < count( FWP()->or_values ) || ! isset( FWP()->or_values[ $facet['name'] ] ) ) ) {
            $post_ids = array();
            $or_values = FWP()->or_values; // Preserve the original
            unset( $or_values[ $facet['name'] ] );

            $counter = 0;
            foreach ( $or_values as $name => $vals ) {
                $post_ids = ( 0 == $counter ) ? $vals : array_intersect( $post_ids, $vals );
                $counter++;
            }

            // Return only applicable results
            $post_ids = array_intersect( $post_ids, FWP()->unfiltered_post_ids );
        }
        else {
            $post_ids = FWP()->unfiltered_post_ids;
        }

        $post_ids = empty( $post_ids ) ? array( 0 ) : $post_ids;
        $where_clause = ' AND post_id IN (' . implode( ',', $post_ids ) . ')';

        $sql = "
        SELECT DISTINCT UPPER(LEFT(facet_display_value, 1)) AS letter
        FROM {$wpdb->prefix}facetwp_index
        WHERE facet_name = '{$facet['name']}' $where_clause
        ORDER BY letter";
        $results = $wpdb->get_col( $sql );

        // Remove accents
        if ( ! empty( $results ) ) {
            $results = array_map( 'remove_accents', $results );
            $results = array_unique( $results );
        }

        $available_chars = array( '#', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
            'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
            'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' );

        $output .= '<span class="facetwp-alpha available" data-id="">' . __( 'Any', 'fwp' ) . '</span>';

        foreach ( $available_chars as $char ) {
            $match = false;
            $active = in_array( $char, $selected_values );

            if ( '#' == $char ) {
                foreach ( array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 ) as $num ) {
                    if ( in_array( (string) $num, $results ) ) {
                        $match = true;
                        break;
                    }
                }
            }
            elseif ( in_array( $char, $results ) ) {
                $match = true;
            }

            if ( $active ) {
                $output .= '<span class="facetwp-alpha selected" data-id="' . $char . '">' . $char . '</span>';
            }
            elseif ( $match ) {
                $output .= '<span class="facetwp-alpha available" data-id="' . $char . '">' . $char . '</span>';
            }
            else {
                $output .= '<span class="facetwp-alpha" data-id="' . $char . '">' . $char . '</span>';
            }
        }

        return $output;
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $selected_values = $params['selected_values'];
        $selected_values = is_array( $selected_values ) ? $selected_values[0] : $selected_values;

        // The "#" character is an alias for all numbers
        if ( '#' == $selected_values ) {
            $selected_values = array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 );
            $selected_values = implode( "','", $selected_values );
        }

        $sql = "
        SELECT DISTINCT post_id FROM {$wpdb->prefix}facetwp_index
        WHERE facet_name = '{$facet['name']}' AND UPPER(SUBSTR(facet_display_value, 1, 1)) IN ('$selected_values')";
        return $wpdb->get_col( $sql );
    }


    /**
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('facetwp/load/alpha', function($this, obj) {
        $this.find('.facet-source').val(obj.source || 'post_title');
    });

    wp.hooks.addFilter('facetwp/save/alpha', function(obj, $this) {
        obj['source'] = $this.find('.facet-source').val();
        return obj;
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output any front-end scripts
     */
    function front_scripts() {
?>
<style>
.facetwp-type-alpha {
    margin-bottom: 20px;
}
.facetwp-alpha {
    display: inline-block;
    color: #ddd;
    margin-right: 8px;
    cursor: default;
}
.facetwp-alpha.available {
    color: #333;
    cursor: pointer;
}
.facetwp-alpha.selected {
    color: #333;
    font-weight: bold;
}
</style>
<script>
(function($) {
    $(function() {
        wp.hooks.addAction('facetwp/refresh/alpha', function($this, facet_name) {
            FWP.facets[facet_name] = $this.find('.facetwp-alpha.selected').attr('data-id') || '';
        });
    });

    $(document).on('click', '.facetwp-alpha.available', function() {
        $parent = $(this).closest('.facetwp-facet');
        $parent.find('.facetwp-alpha').removeClass('selected');
        var facet_name = $parent.attr('data-name');
        $(this).addClass('selected');

        if ('' !== $(this).attr('data-id')) {
            FWP.frozen_facets[facet_name] = 'soft';
        }
        FWP.refresh();
    });
})(jQuery);
</script>
<?php
    }
}
