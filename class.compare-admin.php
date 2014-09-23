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
        add_action('admin_init', array('Compare_Admin', 'admin_export'));

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

        add_options_page(
                'Registro de consultas',
                'Registro de consultas',
                'manage_options',
                'registro_consultas',
                array('Compare_Admin', 'registro_consultas')
            );
    }

    public static function admin_export()
    {
        if (isset($_GET['comprareexport'])) {
            global $wpdb;
            $query = "SELECT * FROM ". $wpdb->prefix ."compare_consorcio ORDER BY nome;";

            $results = $wpdb->get_results($query);
            //output the headers for the CSV file
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Description: File Transfer');
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=consultas.csv");
            header("Expires: 0");
            header("Pragma: public");

            //open the file stream
            $fh = @fopen( 'php://output', 'w' );

            fputcsv($fh, array(
                'Nome',
                'E-mail',
                'Valor',
                'Prazo (meses)',
                'Data'
            ));
            foreach ($results as $result) {
                $data = array(
                    $result->nome,
                    $result->email,
                    $result->valor,
                    $result->prazo,
                    $result->date,
                );
                fputcsv($fh, $data);
            }
            // Close the file stream
            fclose($fh);
            // Make sure nothing else is sent, our file is done
            exit;
        }

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

    public static function registro_consultas()
    {

        include ( COMPARE_PLUGIN_DIR.'class.compare-list-table.php');

        ?>
        <div class="wrap">

            <div id="icon-users" class="icon32"><br/></div>
            <h2>Lista de consultas da comparação Consórcio x Financiamento</h2>
            <?php
                //Create an instance of our package class...
                $listTable = new Compare_List_Table();
                //Fetch, prepare, sort, and filter our data...
                $listTable->prepare_items();
            ?>
            <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
                <p>Abaixo estão todas os registros de pessoas que realizaram a comparação.</p>
                <p>Clique <a target="_blank" href="/wp-admin/options-general.php?page=registro_consultas&comprareexport=1" style="text-decoration:none;">aqui</a> para fazer o download dos registros no formato csv.</p>
            </div>

            <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
            <form id="movies-filter" method="get">
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <!-- Now we can render the completed list table -->
                <?php $listTable->display() ?>
            </form>

        </div>
    <?php
    }
}
