<?php
/*
Plugin Name: FacetWP - Alpha
Description: Alphabetical letter facet
Version: 1.1
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
        $where_clause = $params['where_clause'];
        $where_clause = str_replace( 'post_id', 'ID', $where_clause );

        $sql = "
        SELECT DISTINCT UPPER(LEFT(post_title, 1)) AS letter
        FROM {$wpdb->posts}
        WHERE 1 $where_clause
        ORDER BY letter";
        $results = $wpdb->get_col( $sql );

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
        SELECT DISTINCT ID FROM {$wpdb->posts}
        WHERE UPPER(SUBSTR(post_title, 1, 1)) = '$selected_values'";
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
    });

    wp.hooks.addFilter('facetwp/save/alpha', function($this, obj) {
        return obj;
    });

    wp.hooks.addAction('facetwp/change/alpha', function($this) {
        $this.closest('.facetwp-facet').find('.name-source').hide();
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
    wp.hooks.addAction('facetwp/refresh/alpha', function($this, facet_name) {
        FWP.facets[facet_name] = $this.find('.facetwp-alpha.selected').attr('data-id') || '';
    });

    $(document).on('click', '.facetwp-alpha.available', function() {
        $parent = $(this).closest('.facetwp-facet');
        $parent.find('.facetwp-alpha').removeClass('selected');
        $(this).addClass('selected');
        FWP.static_facet = $parent.attr('data-name');
        FWP.refresh();
    });
})(jQuery);
</script>
<?php
    }
}
