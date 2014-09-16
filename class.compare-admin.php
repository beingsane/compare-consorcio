<?php

class Compare_Admin
{
    private static $initiated = false;

    public static function init()
    {
        if (! self::$initiated) {
            self::init_hooks();
        }
    }

    public static function init_hooks()
    {
        self::$initiated = true;
        add_action( 'admin_menu', array( 'Compare_Admin', 'admin_init' ) );

    }

    public static function admin_init()
    {
        add_options_page(
                'Comparação Consórcio x Financiamento',
                'Comparação Consórcio',
                'manage_options',
                'comparacao_consorcio',
                array('Compare_Admin', 'page_configuration')
            );
    }

    public static function page_configuration()
    {
        //must check that the user has the required capability
        if (!current_user_can('manage_options')) {
          wp_die( __('You do not have sufficient permissions to access this page.') );
        }

        // variables for the field and option names
        $hidden_field_name = 'mt_submit_hidden';
        $data_consorcio = 'comp_consorcio';
        $data_financiamento = 'comp_financiamento';
        $updated = '';

        // Read in existing option value from database
        $opt_val_consorcio = get_option( $data_consorcio );
        $opt_val_financiamento = get_option( $data_financiamento );

        // See if the user has posted us some information
        // If they did, this hidden field will be set to 'Y'
        if ( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
            // Read their posted value
            $opt_val_consorcio = $_POST[ $data_consorcio ];
            $opt_val_financiamento = $_POST[ $data_financiamento ];

            // Save the posted value in the database
            update_option( $data_consorcio, $opt_val_consorcio );
            update_option( $data_financiamento, $opt_val_financiamento );

            // Put an settings updated message on the screen

            $updated = '<div class="updated"><p><strong>'. __('settings saved.', 'compare-consorcio' ). '</strong></p></div>';

        }
        include( COMPARE_PLUGIN_DIR.'view/config.php');
    }
}
