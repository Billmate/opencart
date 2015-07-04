/**
 * Created by Jesper Johansson on 15-07-04.
 */

$('input[name=\'billmatebank-country\']').autocomplete({
    delay: 500,
    source: function(request, response) {
        $.ajax({

            url: 'index.php?route=payment/billmate_invoice/country_autocomplete&token='+token+'&filter_name=' +  encodeURIComponent(request),

            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item.name,
                        value: item.country_id
                    }
                }));
            }
        });
    },
    // TODO Fix support for both old and new function
    select: function(item) {
        if($('#billmatebank-country' + item.value))
            $('#billmatebank-country' + item.value).remove();

        $('#billmatebank-country').append('<div id="billmatebank-country' + item.value + '">' + item.label + '<img src="view/image/delete.png" alt="" /><input type="hidden" name="billmatebank-country['+item.value+'][name]" value="' + item.label + '" /></div>');

        $('#billmatebank-country div:odd').attr('class', 'odd');
        $('#billmatebank-country div:even').attr('class', 'even');

        return false;
    },
    focus: function(event, ui) {
        return false;
    }
}).autocomplete("instance")._renderItem = function(ul, item){
    return $("<li>").append("<a>"+item.label +"</a>").appendTo(ul);
};

$('input[name=\'billmatecard-country\']').autocomplete({
    delay: 500,
    source: function(request, response) {
        $.ajax({

            url: 'index.php?route=payment/billmate_cardpay/country_autocomplete&token='+token+'&filter_name=' +  encodeURIComponent(request),

            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item.name,
                        value: item.country_id
                    }
                }));
            }
        });
    },
    // TODO Fix support for both old and new function
    select: function(item) {
        if($('#billmatecard-country' + item.value))
            $('#billmatecard-country' + item.value).remove();

        $('#billmatecard-country').append('<div id="billmatecard-country' + item.value + '">' + item.label + '<img src="view/image/delete.png" alt="" /><input type="hidden" name="billmatecard-country['+item.value+'][name]" value="' + item.label + '" /></div>');

        $('#billmatecard-country div:odd').attr('class', 'odd');
        $('#billmatecard-country div:even').attr('class', 'even');

        return false;
    },
    focus: function(event, ui) {
        return false;
    }
}).autocomplete("instance")._renderItem = function(ul, item){
    return $("<li>").append("<a>"+item.label +"</a>").appendTo(ul);
};

$('input[name=\'billmate-country\']').autocomplete({
    delay: 500,
    source: function(request, response) {
        $.ajax({

            url: 'index.php?route=payment/billmate_invoice/country_autocomplete&token='+token+'&filter_name=' +  encodeURIComponent(request),

            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item.name,
                        value: item.country_id
                    }
                }));
            }
        });
    },
    // TODO Fix support for both old and new function
    select: function(item) {
        if($('#billmate-country' + item.value))
            $('#billmate-country' + item.value).remove();

        $('#billmate-country').append('<div id="billmate-country' + item.value + '">' + item.label + '<img src="view/image/delete.png" alt="" /><input type="hidden" name="billmate-country['+item.value+'][name]" value="' + item.label + '" /></div>');

        $('#billmate-country div:odd').attr('class', 'odd');
        $('#billmate-country div:even').attr('class', 'even');

        return false;
    },
    focus: function(event, ui) {
        return false;
    }
}).autocomplete("instance")._renderItem = function(ul, item){
    return $("<li>").append("<a>"+item.label +"</a>").appendTo(ul);
};

$('input[name=\'billmatepart-country\']').autocomplete({
    delay: 500,
    source: function(request, response) {
        $.ajax({

            url: 'index.php?route=payment/billmate_invoice/country_autocomplete&token='+token+'&filter_name=' +  encodeURIComponent(request),

            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item.name,
                        value: item.country_id
                    }
                }));
            }
        });
    },
    // TODO Fix support for both old and new function
    select: function(item) {
        if($('#billmatepart-country' + item.value))
            $('#billmatepart-country' + item.value).remove();

        $('#billmatepart-country').append('<div id="billmatepart-country' + item.value + '">' + item.label + '<img src="view/image/delete.png" alt="" /><input type="hidden" name="billmatepart-country['+item.value+'][name]" value="' + item.label + '" /></div>');

        $('#billmatepart-country div:odd').attr('class', 'odd');
        $('#billmatepart-country div:even').attr('class', 'even');

        return false;
    },
    focus: function(event, ui) {
        return false;
    }
}).autocomplete("instance")._renderItem = function(ul, item){
    return $("<li>").append("<a>"+item.label +"</a>").appendTo(ul);
};
