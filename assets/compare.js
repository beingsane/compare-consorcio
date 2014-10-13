jQuery(document).ready(function(){
    jQuery('#compare-valor').maskMoney({prefix:'R$ ', allowNegative: false, thousands:'.', decimal:','});

    jQuery("#compare-telefone").mask("(99) 9999-9999?9");
});
