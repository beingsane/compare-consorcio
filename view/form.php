<div class="">
    <form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="post">
        <p>
            Nome (Obrigatório)<br>
            <span class="wpcf7-form-control-wrap your-name">
                <input type="text" name="comp-name" value="<?php echo isset( $_POST["comp-name"] ) ? esc_attr( $_POST["comp-name"] ) : ''; ?>" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required wpcf7-not-valid" aria-required="true">
                <?php if (array_key_exists('comp-name', $invalid)): ?>
                    <span role="alert" class="wpcf7-not-valid-tip"><?php echo $invalid['comp-name'] ?></span>
                <?php endif ?>
            </span>
        </p>
        <p>
            E-mail (Obrigatório)<br>
            <span class="wpcf7-form-control-wrap">
                <input type="email" name="comp-email" value="<?php echo isset( $_POST["comp-email"] ) ? esc_attr( $_POST["comp-email"] ) : ''; ?>" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email" aria-required="true">
                <?php if (array_key_exists('comp-email', $invalid)): ?>
                    <span role="alert" class="wpcf7-not-valid-tip"><?php echo $invalid['comp-email'] ?></span>
                <?php endif ?>
            </span>
        </p>
        <p>
            Valor<br>
            <span class="wpcf7-form-control-wrap">
                <input type="text" id="compare-valor" name="comp-valor" value="<?php echo isset( $_POST["comp-valor"] ) ? esc_attr( $_POST["comp-valor"] ) : ''; ?>" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false">
                <?php if (array_key_exists('comp-valor', $invalid)): ?>
                    <span role="alert" class="wpcf7-not-valid-tip"><?php echo $invalid['comp-valor'] ?></span>
                <?php endif ?>
            </span>
        </p>
        <p>
            Prazo<br>
            <span class="wpcf7-form-control-wrap">
                <select name="comp-prazo" class="required">
                    <option value="25">25 meses</option>
                    <option value="36">36 meses</option>
                    <option value="50">50 meses</option>
                    <option value="60">60 meses</option>
                    <option value="70">70 meses</option>
                    <option value="80">80 meses</option>
                    <option value="100">100 meses</option>
                    <option value="120">120 meses</option>
                    <option value="135">135 meses</option>
                    <option value="150">150 meses</option>
                    <option value="180">180 meses</option>
                </select>
                <?php if (array_key_exists('comp-prazo', $invalid)): ?>
                    <span role="alert" class="wpcf7-not-valid-tip"><?php echo $invalid['comp-prazo'] ?></span>
                <?php endif ?>
            </span>
        </p>
        <input type="submit" name="comp-submitted" value="Calcular">
    </form>
</div>
<?php if (isset($_POST['comp-submitted']) && empty($invalid)): ?>
    <h3 style="text-align: left;">Consórcio</h3>
    <div class="column one">
        <table>
            <thead>
                <tr>
                    <th>Valor do Crédito</th>
                    <th>Prazo</th>
                    <th>Parcela Mensal</th>
                    <th>Total em Taxas</th>
                    <th>Custo Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $valor = str_replace('.', '', $_POST['comp-valor']);
                    $valor = str_replace(',', '.', $valor);
                    $valor = (float) str_replace('R$ ', '', $valor);

                    setlocale(LC_MONETARY, 'pt_BR');
                    //Variaveis para o calculo
                    $taxa_juros = floatval($opt_val_consorcio['taxa_juros']);
                    $taxa_administracao = floatval($opt_val_consorcio['taxa_administracao']);
                    $fundo_reserva = floatval($opt_val_consorcio['fundo_reserva']);
                    $dados_consorcio = self::calcConsorcio($valor, $_POST['comp-prazo']);
                ?>
                <tr>
                    <td><?php echo money_format('%n', $valor); ?></td>
                    <td><?php echo $_POST['comp-prazo']; ?> meses</td>
                    <td><?php echo money_format('%n', $dados_consorcio['parcela']); ?></td>
                    <td><?php echo money_format('%n', $dados_consorcio['jurosTotal']); ?></td>
                    <td><?php echo money_format('%n', $dados_consorcio['valorTotal']); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 style="text-align: left;">Financiamento</h3>
    <div class="column one">
        <table>
            <thead>
                <tr>
                    <th>Valor do Crédito</th>
                    <th>Prazo</th>
                    <th>Parcela Mensal</th>
                    <th>Total em Juros</th>
                    <th>Custo Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    setlocale(LC_MONETARY, 'pt_BR');
                    //Variaveis para o calculo
                    $taxa_juros = floatval($opt_val_financiamento['taxa_juros']);
                    $taxa_administracao = floatval($opt_val_financiamento['taxa_administracao']);
                    $fundo_reserva = floatval($opt_val_financiamento['fundo_reserva']);
                    $dados_financiamento = self::calcFinanciamentoPrice($valor, $_POST['comp-prazo']);
                ?>
                <tr>
                    <td><?php echo money_format('%n', $valor); ?></td>
                    <td style="text-align: center;"><?php echo $_POST['comp-prazo']; ?> meses</td>
                    <td><?php echo money_format('%n', $dados_financiamento['parcela']); ?></td>
                    <td><?php echo money_format('%n', $dados_financiamento['jurosTotal']); ?></td>
                    <td><?php echo money_format('%n', $dados_financiamento['valorTotal']); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
<?php endif ?>
