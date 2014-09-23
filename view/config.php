<div class="wrap">
    <?php screen_icon(); ?>
    <h2><?php echo __( 'Configurações para comparação entre consórcio e financiamentos', 'compare-consorcio' ); ?> </h2>
    <?php echo $updated; ?>
    <form name="form1" method="post" action="">
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
        <hr />
        <h3><?php _e('Consórcio', 'compare-consorcio') ?></h3>
        <table class="form-table">
            <tbody>
                <?php
                    $fieldName = $data_consorcio. '[taxa_juros]';
                    $dataField = $opt_val_consorcio['taxa_juros'];
                ?>
                <tr>
                    <th scope="row"><label for="<?php echo $fieldName; ?>"><?php _e("Taxa de Juros (ao mês):", 'compare-consorcio' ); ?></label></th>
                    <td><input name="<?php echo $fieldName; ?>" type="text" id="<?php echo $fieldName; ?>" value="<?php echo $dataField; ?>" class="regular-text" ></td>
                </tr>

                <?php
                    $fieldName = $data_consorcio. '[taxa_administracao]';
                    $dataField = $opt_val_consorcio['taxa_administracao'];
                ?>
                <tr>
                    <th scope="row"><label for="<?php echo $fieldName; ?>"><?php _e("Taxa de Administração (ao mês):", 'compare-consorcio' ); ?></label></th>
                    <td><input name="<?php echo $fieldName; ?>" type="text" id="<?php echo $fieldName; ?>" value="<?php echo $dataField; ?>" class="regular-text" ></td>
                </tr>

                <?php
                    $fieldName = $data_consorcio. '[fundo_reserva]';
                    $dataField = $opt_val_consorcio['fundo_reserva'];
                ?>
                <tr>
                    <th scope="row"><label for="<?php echo $fieldName; ?>"><?php _e("Fundo de reserva (ao mês):", 'compare-consorcio' ); ?></label></th>
                    <td><input name="<?php echo $fieldName; ?>" type="text" id="<?php echo $fieldName; ?>" value="<?php echo $dataField; ?>" class="regular-text" ></td>
                </tr>
            </tbody>
        </table>
        <hr />
        <h3><?php _e('Financiamento', 'compare-consorcio') ?></h3>

        <table class="form-table">
            <tbody>
                <?php
                    $fieldName = $data_financiamento. '[taxa_juros]';
                    $dataField = $opt_val_financiamento['taxa_juros'];
                ?>
                <tr>
                    <th scope="row"><label for="<?php echo $fieldName; ?>"><?php _e("Taxa de Juros (ao mês):", 'compare-consorcio' ); ?></label></th>
                    <td><input name="<?php echo $fieldName; ?>" type="text" id="<?php echo $fieldName; ?>" value="<?php echo $dataField; ?>" class="regular-text" ></td>
                </tr>

                <?php
                    $fieldName = $data_financiamento. '[taxa_administracao]';
                    $dataField = $opt_val_financiamento['taxa_administracao'];
                ?>
                <tr>
                    <th scope="row"><label for="<?php echo $fieldName; ?>"><?php _e("Taxa de Administração (ao mês):", 'compare-consorcio' ); ?></label></th>
                    <td><input name="<?php echo $fieldName; ?>" type="text" id="<?php echo $fieldName; ?>" value="<?php echo $dataField; ?>" class="regular-text" ></td>
                </tr>

                <?php
                    $fieldName = $data_financiamento. '[fundo_reserva]';
                    $dataField = $opt_val_financiamento['fundo_reserva'];
                ?>
                <tr>
                    <th scope="row"><label for="<?php echo $fieldName; ?>"><?php _e("Fundo de reserva (ao mês):", 'compare-consorcio' ); ?></label></th>
                    <td><input name="<?php echo $fieldName; ?>" type="text" id="<?php echo $fieldName; ?>" value="<?php echo $dataField; ?>" class="regular-text" ></td>
                </tr>

            </tbody>
        </table>

        <p class="submit">
        <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
        </p>

    </form>
</div>
