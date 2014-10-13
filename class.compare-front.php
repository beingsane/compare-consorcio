<?php

class Compare_Front
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
        add_shortcode( 'compare_form', array('Compare_Front','shortcode_form') );
        add_action( 'wp_enqueue_scripts', array('Compare_Front','my_scripts'), 999 );
    }

    public static function my_scripts()
    {
        wp_enqueue_script( 'my-compare-maskMoney', plugins_url( 'assets/jquery.maskMoney.min.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'my-compare-maskedinput', plugins_url( 'assets/jquery.maskedinput.min.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'my-compare-script', plugins_url( 'assets/compare.js', __FILE__ ), array( 'jquery', 'my-compare-maskMoney', 'my-compare-maskedinput' ), '1.0.0', true );
    }

    public static function shortcode_form($attrs)
    {
        extract( shortcode_atts( array(
            'label' => __( 'Financiamento x Consórcio', 'compare-consorcio' )
        ), $attrs ) );

        ob_start();
        self::html_form_code();

        return ob_get_clean();
    }

    public static function html_form_code()
    {
        $data_consorcio = 'comp_consorcio';
        $data_financiamento = 'comp_financiamento';
        $opt_val_consorcio = get_option( $data_consorcio );
        $opt_val_financiamento = get_option( $data_financiamento );
        $invalid = array();

        if (isset($_POST['comp-submitted'])) {
            if (empty($_POST['comp-name'])) {
                $invalid['comp-name'] = 'Por favor preencha este campo obrigatório.';
            }
            if (empty($_POST['comp-email'])) {
                $invalid['comp-email'] = 'Por favor preencha este campo obrigatório.';
            }
            if (empty($_POST['comp-valor'])) {
                $invalid['comp-valor'] = 'Por favor preencha este campo obrigatório.';
            }
            if (empty($_POST['comp-prazo'])) {
                $invalid['comp-prazo'] = 'Por favor preencha este campo obrigatório.';
            }
            if (empty($_POST['comp-telefone'])) {
                $invalid['comp-telefone'] = 'Por favor preencha este campo obrigatório.';
            }
            if (!empty($_POST['comp-email']) && !is_email($_POST['comp-email'])) {
                $invalid['comp-email'] = 'Por favor preencha com um e-mail valido.';
            }

            $valor = str_replace('.', '', $_POST['comp-valor']);
            $valor = str_replace(',', '.', $valor);
            $valor = (float) str_replace('R$ ', '', $valor);
            if (!empty($_POST['comp-valor']) && $valor < 10 ) {
                $invalid['comp-valor'] = 'Número invalido.';
            }

            if (empty($invalid)) {
                self::registraOperacao($_POST);
            }
        }

        include( COMPARE_PLUGIN_DIR.'view/form.php');
    }

    protected static function registraOperacao($post)
    {
        global $wpdb;
        $table = $wpdb->prefix . "compare_consorcio";
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM ".$table." WHERE nome = '%s' AND email = '%s' AND valor = '%s' ;",
                $post['comp-name'],
                $post['comp-email'],
                $post['comp-valor']
            ));

        if (empty($result)) {
            $valor = str_replace('.', '', $post['comp-valor']);
            $valor = str_replace(',', '.', $valor);
            $valor = (float) str_replace('R$ ', '', $valor);
            $wpdb->insert( $table, array(
                    'valor' => $valor,
                    'nome' => $post['comp-name'],
                    'email' => $post['comp-email'],
                    'telefone' => $post['comp-telefone'],
                    'prazo' => $post['comp-prazo'],
                    'date' => current_time( 'mysql' ),
                ) );
        }
    }

    protected static function calcFinanciamentoSac($valor, $meses)
    {
        $opt_val_financiamento = get_option( 'comp_financiamento' );
        $taxa_juros = floatval($opt_val_financiamento['taxa_juros'])/100;
        $taxa_administracao = floatval($opt_val_financiamento['taxa_administracao']);
        $fundo_reserva = floatval($opt_val_financiamento['fundo_reserva']);

        $amortização = $valor / $meses;
        $valorTotal = 0;
        $jurosTotal = 0;
        for ($i=0; $i < $meses; $i++) {
            $jurosParcela = $taxa_juros * ($valor - $i*$amortização);
            $parcela = $amortização + $jurosParcela;
            $valorTotal += $parcela;
            $jurosTotal += $jurosParcela;
        }

        return array(
            'jurosTotal' => $jurosTotal,
            'valorTotal' => $valorTotal,
            'valor' => $valor,
            'meses' => $meses
        );
    }

    protected static function calcFinanciamentoPrice($valor, $meses)
    {
        $opt_val_financiamento = get_option( 'comp_financiamento' );
        $taxa_juros = floatval($opt_val_financiamento['taxa_juros'])/100;
        $taxa_administracao = floatval($opt_val_financiamento['taxa_administracao']);
        $fundo_reserva = floatval($opt_val_financiamento['fundo_reserva']);

        $parcela = self::PMT($taxa_juros, $meses, -$valor);
        $valorTotal = 0;
        $jurosTotal = 0;
        $apv = $valor;
        for ($i=0; $i < $meses; $i++) {
            $amortizacao = $parcela - $apv * $taxa_juros;
            $jurosParcela = ($apv * $taxa_juros);
            $valorTotal += $parcela;
            $jurosTotal += $jurosParcela;
            $apv -= $amortizacao;
        }

        return array(
            'parcela' => $parcela,
            'jurosTotal' => $jurosTotal,
            'valorTotal' => $valorTotal,
            'valor' => $valor,
            'meses' => $meses
        );
    }

    private static function PMT($i, $n, $p)
    {
        return $i * $p * pow((1 + $i), $n) / (1 - pow((1 + $i), $n));
    }

    protected static function calcConsorcio($valor, $meses)
    {
        $opt_val_consorcio = get_option( 'comp_consorcio' );
        //$taxa_juros = floatval($opt_val_consorcio['taxa_juros']);
        $taxa_administracao = floatval($opt_val_consorcio['taxa_administracao'])/100;
        $fundo_reserva = floatval($opt_val_consorcio['fundo_reserva'])/100;

        $amortização = $valor / $meses;
        $valorTotal = 0;
        $jurosTotal = 0;
        for ($i=0; $i < $meses; $i++) {

            $jurosParcela = ($taxa_administracao * $valor) + ($fundo_reserva * $valor);
            $parcela = $amortização + $jurosParcela;
            $valorTotal += $parcela;
            $jurosTotal += $jurosParcela;
        }

        return array(
            'jurosTotal' => $jurosTotal,
            'valorTotal' => $valorTotal,
            'parcela' => $parcela,
            'valor' => $valor,
            'meses' => $meses
        );
    }
}
