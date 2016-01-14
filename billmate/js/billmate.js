/**
 * Created by Jesper Johansson on 15-07-04.
 */
$('input[name=\'billmatebank-country\']').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=payment/billmate_invoice/country_autocomplete&token='+token+'&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['country_id']
                    }
                }));
            }
        });
    },
    'select': function(item) {
        $('input[name=\'billmatebank-country\']').val('');

        $('#billmatebank-country' + item['value']).remove();

        $('#billmatebank-country').append('<div id="billmatebank-country' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="billmatebank-country['+item['value']+'][name]" value="' + item['label'] + '" /></div>');
    }
});

$('#billmatebank-country').delegate('.fa-minus-circle', 'click', function() {
    $(this).parent().remove();
});
$('input[name=\'billmatecard-country\']').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=payment/billmate_cardpay/country_autocomplete&token='+token+'&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['country_id']
                    }
                }));
            }
        });
    },
    'select': function(item) {
        $('input[name=\'billmatecard-country\']').val('');

        $('#billmatecard-country' + item['value']).remove();

        $('#billmatecard-country').append('<div id="billmatecard-country' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="billmatecard-country['+item['value']+'][name]" value="' + item['label'] + '" /></div>');
    }
});

$('#billmatecard-country').delegate('.fa-minus-circle', 'click', function() {
    $(this).parent().remove();
});
$('input[name=\'billmateinvoice-country\']').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=payment/billmate_invoice/country_autocomplete&token='+token+'&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['country_id']
                    }
                }));
            }
        });
    },
    'select': function(item) {
        $('input[name=\'billmateinvoice-country\']').val('');

        $('#billmateinvoice-country' + item['value']).remove();

        $('#billmateinvoice-country').append('<div id="billmateinvoice-country' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="billmateinvoice-country['+item['value']+'][name]" value="' + item['label'] + '" /></div>');
    }
});

$('#billmateinvoice-country').delegate('.fa-minus-circle', 'click', function() {
    $(this).parent().remove();
});
$('input[name=\'billmatepartpayment-country\']').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=payment/billmate_invoice/country_autocomplete&token='+token+'&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['country_id']
                    }
                }));
            }
        });
    },
    'select': function(item) {
        $('input[name=\'billmatepartpayment-country\']').val('');

        $('#billmatepartpayment-country' + item['value']).remove();

        $('#billmatepartpayment-country').append('<div id="billmatepartpayment-country' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="billmatepartpayment-country['+item['value']+'][name]" value="' + item['label'] + '" /></div>');
    }
});

$('#billmatepartpayment-country').delegate('.fa-minus-circle', 'click', function() {
    $(this).parent().remove();
});



