(function ( $ ) {
    $.fn.bmPnoAddress = {
        init: function(options) {
            var settings = $.extend({
                paymentFormSelector: '#payment_address_form',
                pnoAddressForm: '#bm-pno-address-form',
                pnoField: '#dcheckout_pno',
                pnoGetAddressButton: '#dcheckout_getaddress',
                errorBlock: '#dcheckout_getaddress-error'
            }, options );

            bmpathis = this;
            bmpathis.config = settings;
            if (bmpathis.isFieldExist()) {
               return;
            }

            bmpathis.addPnoForm();
            bmpathis.initGetAddressListeners();

            return bmpathis;
        },
        triggerCheckoutUpdate: function(triggerData) {
            bmpathis.init();
        },
        addPnoForm: function () {
            var pnoHtml = bmpathis.getPnoFormHtml();
            $(bmpathis.config.paymentFormSelector).prepend(pnoHtml);
        },
        getPnoFormHtml: function () {
            return $(bmpathis.config.pnoAddressForm).html();
        },
        initGetAddressListeners: function() {
            $(bmpathis.config.pnoGetAddressButton).on('click', function() {
                bmpathis.initMakeRequest();
            });

            $(bmpathis.config.pnoField).on('keypress', function(e) {
                if (e.which == 13) {
                    bmpathis.initMakeRequest();
                }
            });
            return false;
        },
        initMakeRequest: function() {
            var pnoValue = $(bmpathis.config.pnoField).val();
            if (pnoValue != '') {
                var requestData = {
                    pno: pnoValue
                };
                bmpathis.makeRequest(requestData);
            }
        },
        makeRequest: function(requestData) {
            $.ajax({
                url: 'index.php?route=payment/billmate_invoice/getaddress',
                data: requestData,
                type: 'post',
                success: function(response) {
                    bmpathis.clearError();
                    var result = JSON && JSON.parse(response) || $.parseJSON(response);
                    if (result.success) {
                        bmpathis.fillAddressForm(result.data);
                    } else {
                        bmpathis.showError(result.error);
                    }
                }
            })
        },
        fillAddressForm: function(pnoAddress) {
            if (typeof pnoAddress.company != 'undefined') {
                $('#payment_address_company').val(pnoAddress.company).change();
            }
            $('#payment_address_firstname').val(pnoAddress.firstname).change();
            $('#payment_address_lastname').val(pnoAddress.lastname).change();
            $('#payment_address_email').val(pnoAddress.email).change();
            $('#payment_address_email_confirm').val(pnoAddress.email).change();
            $('#payment_address_telephone').val(pnoAddress.phone).change();
            $('#payment_address_address_1').val(pnoAddress.street).change();
            $('#payment_address_city').val(pnoAddress.city).change();
            $('#payment_address_postcode').val(pnoAddress.zip).change();
            $('select[name="payment_address.country_id"]').val(pnoAddress.country_id).change();
        },
        showError: function(errorMessage) {
            $(bmpathis.config.errorBlock).html(errorMessage);
        },
        clearError: function() {
            $(bmpathis.config.errorBlock).html('');
        },
        isFieldExist: function () {
            return $(bmpathis.config.paymentFormSelector + ' ' +
                    bmpathis.config.pnoField).length ;
        }
    };
}( jQuery ));