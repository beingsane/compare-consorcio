<?php
/*
Plugin Name: Criar comparação entre consorcios e financiamentos
Plugin URI: http://midiadeimpacto.com.br/
Description: Plugin para criação de uma comparação entre consórcios e financiamentos
Version: 1.0
Author: Leandro Lugaresi
Author URI: http://www.leandrolugaresi.com.br/
License: GPLv2
*/

if (! defined('ABSPATH')) {
    die('');
}

define( 'COMPARE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'COMPARE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// register_activation_hook( __FILE__, array( 'Akismet', 'plugin_activation' ) );
// register_deactivation_hook( __FILE__, array( 'Akismet', 'plugin_deactivation' ) );

require_once( COMPARE_PLUGIN_DIR . 'class.compare-front.php' );
add_action( 'init', array( 'Compare_Front', 'init' ) );

require_once( COMPARE_PLUGIN_DIR . 'class.compare-admin.php' );
add_action( 'init', array( 'Compare_Admin', 'init' ) );

add_action( 'init', 'compare_load_plugin_textdomain' );

function compare_load_plugin_textdomain()
{
    $locale = apply_filters( 'plugin_locale', get_locale(), 'compare-consorcio' );

    load_textdomain( 'compare-consorcio', trailingslashit( WP_LANG_DIR ) . 'compare-consorcio/compare-consorcio-' . $locale . '.mo' );
    load_plugin_textdomain( 'compare-consorcio', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
