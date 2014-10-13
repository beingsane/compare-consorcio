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

register_activation_hook( __FILE__, 'compare_plugin_activation' );

function compare_plugin_activation()
{
    // Para usarmos a função dbDelta() é necessário carregar este ficheiro
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Acesso ao objeto global de gestão de bases de dados
    global $wpdb;

    // Vamos checar se a nova tabela existe
    // A propriedade prefix é o prefixo de tabela escolhido na
    // instalação do WordPress

    $tablename = $wpdb->prefix . 'compare_consorcio';

    if ($wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename) {
        // Se a tabela não existe vamos criá-la
        $sql = "CREATE TABLE ".$tablename." (
            id mediumint(9) NsOT NULL AUTO_INCREMENT,
            nome varchar(150) NOT NULL,
            email varchar(150) NOT NULL,
            valor DECIMAL( 12, 2 ) NOT NULL,
            prazo varchar(40) NOT NULL,
            date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY (id)
        );";
        dbDelta( $sql );
    } else {
        //updates
        $columns = $wpdb->get_col( 'DESC '. $tablename, 0);

        if (!in_array('email', $columns)) {
            $sql = "ALTER TABLE  `".$tablename."` ADD  `email` VARCHAR( 150 ) NULL DEFAULT NULL AFTER  `nome` ;";
            dbDelta( $sql );
        }

        if (!in_array('telefone', $columns)) {
            $sql = "ALTER TABLE  `".$tablename."` ADD  `telefone` VARCHAR( 40 ) NULL DEFAULT NULL AFTER  `email` ;";
            dbDelta( $sql );
        }

    }

}

register_deactivation_hook( __FILE__, 'compare_plugin_deactivation' );

function compare_plugin_deactivation()
{
    // Vamos remover a tabela na desinstalação do plugin
    global $wpdb;

    // Vamos checar se a nova tabela existe
    // A propriedade prefix é o prefixo de tabela escolhido na
    // instalação do WordPress
    $tablename = $wpdb->prefix . 'compare_consorcio';
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$tablename'" ) != $tablename ) {

        $count = $wpdb->get_results('SELECT count(*) FROM '. $tablename. ';');

        if ($count['count'] == 0) {
            $sql = "DROP TABLE $tablename;";

            // Para usarmos a função dbDelta() é necessário carregar este ficheiro
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            // Esta função cria a tabela na base de dados e executa as otimizações
            // necessárias.
            dbDelta( $sql );
        }

    }
}

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
