jQuery(document).ready(function(){
    jQuery('#compare-valor').maskMoney({prefix:'R$ ', allowNegative: false, thousands:'.', decimal:','});
});