<?php
/**
 * Plugin Name: WP Thomalex Extension
 * Plugin URI: https://bookityourway.com/
 * Description: Full of professional features for travel booking tools for travel agencies.
 * Author: Snexus
 * Version: 1.0.0
 * Author URI: https://bookityourway.com/
 */


function custom_enqueue()
{
    wp_enqueue_style('thomalex-bootstrap_css', plugins_url('/assets/css/bootstrap.min.css', __FILE__), array(), 'true');
    wp_enqueue_script('thomalex-bootstrap_min_js', plugins_url('/assets/js/bootstrap.min.js', __FILE__), array(), 'true');
    wp_enqueue_script('thomalex-exten-jsnew', plugins_url('/assets/js/thomalex_exten.js', __FILE__), array(), 'true');
    wp_enqueue_style('thomalex-style_css', plugins_url('/assets/css/style.css', __FILE__), array(), 'true');
}
add_action('wp_enqueue_scripts', 'custom_enqueue');


// function wporg_shortcode($atts = [], $content = null)
// {
//     $content = 'The island of Jamaica, lying on its southeast coast. In the city center, the Bob Marley Museum is housed in the reggae singer’s former home. Nearby, Devon House is a colonial-era mansion with period furnishings. Hope Botanical Gardens & Zoo showcases native flora and fauna. Northeast of the city, the Blue Mountains are a renowned coffee-growing region with trails and waterfalls.';
//     return $content;
// }
// add_shortcode('thomalex_jamaica', 'wporg_shortcode');



function guestarea($atts)
{
    ob_start();
    $atts = shortcode_atts(
        array(
            'destination' => '0',
        ),
        $atts,
        'thomalex'
    );

    if ($atts['destination'] != '0') {
        require("pages/hotelSearch.php");
    }
    return ob_get_clean();
}
add_shortcode('thomalex', 'guestarea');



function hotelDetails()
{
    ob_start();
    require("pages/hotelDetails.php");
    return ob_get_clean();
}
add_shortcode('thomalex_hotelDetails', 'hotelDetails');