/**
 * Created by Jesper Johansson on 15-07-04.
 */

$('input[name=\'billmatebank-country\']').autocomplete({
    delay: 500,
    source: function(request, response) {
        $.ajax({
            url: 'index.php?route=payment/billmate_invoice/country_autocomplete&token='+token+'&filter_name=' +  encodeURIComponent(request.term),
            dataType: 'json',
            success: function(json) {
                console.log(json);
                response($.map(json, function(item) {
                    return {
                        label: item.name,
                        value: item.country_id
                    }
                }));
            }
        });
    },
    select: function(event, ui) {
        $('#billmatebank-country' + ui.item.value).remove();
        $('#billmatebank-country').append('<div id="billmatebank-country' + ui.item.value + '">' + ui.item.label + '<img src="view/image/delete.png" alt="" /><input type="hidden" name="billmatebank-country['+ui.item.value+'][name]" value="' + ui.item.label + '" /></div>');
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
$('#billmatebank-country div img').live('click', function() {
    $(this).parent().remove();
    $('#billmatebank-country div:odd').attr('class', 'odd');
    $('#billmatebank-country div:even').attr('class', 'even');
});

$('input[name=\'billmatecard-country\']').autocomplete({
    delay: 500,
    source: function(request, response) {
        $.ajax({
            url: 'index.php?route=payment/billmate_cardpay/country_autocomplete&token='+token+'&filter_name=' +  encodeURIComponent(request.term),
            dataType: 'json',
            success: function(json) {
                console.log(json);
                response($.map(json, function(item) {
                    return {
                        label: item.name,
                        value: item.country_id
                    }
                }));
            }
        });
    },
    select: function(event, ui) {
        $('#billmatecard-country' + ui.item.value).remove();
        $('#billmatecard-country').append('<div id="billmatecard-country' + ui.item.value + '">' + ui.item.label + '<img src="view/image/delete.png" alt="" /><input type="hidden" name="billmatecard-country['+ui.item.value+'][name]" value="' + ui.item.label + '" /></div>');
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
$('#billmatecard-country div img').live('click', function() {
    $(this).parent().remove();
    $('#billmatecard-country div:odd').attr('class', 'odd');
    $('#billmatecard-country div:even').attr('class', 'even');
});

$('input[name=\'billmate-country\']').autocomplete({
    delay: 500,
    source: function(request, response) {
        $.ajax({
            url: 'index.php?route=payment/billmate_invoice/country_autocomplete&token='+token+'&filter_name=' +  encodeURIComponent(request.term),
            dataType: 'json',
            success: function(json) {
                console.log(json);
                response($.map(json, function(item) {
                    return {
                        label: item.name,
                        value: item.country_id
                    }
                }));
            }
        });
    },
    select: function(event, ui) {
        $('#billmate-country' + ui.item.value).remove();
        $('#billmate-country').append('<div id="billmate-country' + ui.item.value + '">' + ui.item.label + '<img src="view/image/delete.png" alt="" /><input type="hidden" name="billmate-country['+ui.item.value+'][name]" value="' + ui.item.label + '" /></div>');
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
$('#billmate-country div img').live('click', function() {
    $(this).parent().remove();
    $('#billmate-country div:odd').attr('class', 'odd');
    $('#billmate-country div:even').attr('class', 'even');
});

$('input[name=\'billmatepart-country\']').autocomplete({
    delay: 500,
    source: function(request, response) {
        $.ajax({
            url: 'index.php?route=payment/billmate_invoice/country_autocomplete&token='+token+'&filter_name=' +  encodeURIComponent(request.term),
            dataType: 'json',
            success: function(json) {
                console.log(json);
                response($.map(json, function(item) {
                    return {
                        label: item.name,
                        value: item.country_id
                    }
                }));
            }
        });
    },
    select: function(event, ui) {
        $('#billmatepart-country' + ui.item.value).remove();
        $('#billmatepart-country').append('<div id="billmatepart-country' + ui.item.value + '">' + ui.item.label + '<img src="view/image/delete.png" alt="" /><input type="hidden" name="billmatepart-country['+ui.item.value+'][name]" value="' + ui.item.label + '" /></div>');
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
$('#billmatepart-country div img').live('click', function() {
    $(this).parent().remove();
    $('#billmatepart-country div:odd').attr('class', 'odd');
    $('#billmatepart-country div:even').attr('class', 'even');
});