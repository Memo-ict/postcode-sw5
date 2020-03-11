
(function($, window) {
    'use strict';

    $.fn.PostcodeNl = function() {
        return this.each(function() {
            var self = $(this);
            var autocomplete;

            var inputElement = self.find('.register--field-autocompleteaddress').first();

            if(inputElement.length == 0) {
                return;
            }

            autocomplete = new PostcodeNl.AutocompleteAddress(inputElement[0], {
                autocompleteUrl: '/PostcodenlApi/autocomplete',
                addressDetailsUrl: '/PostcodenlApi/address-details',
            });

            inputElement.on('autocomplete-select', function(e) {
                if(e.detail.precision == 'Address') {
                    autocomplete.getDetails(e.detail.context, function(json) {
                        console.log(json);

                        self.find('.register--field-street').val(json.address.street);
                        self.find('.register--field-number').val(json.address.buildingNumber);
                        self.find('.register--field-number-addition').val(json.address.buildingNumberAddition);
                        self.find('.register--field-zipcode').val(json.address.postcode);
                        self.find('.register--field-city').val(json.address.locality);
                    });
                }
            });

            self.find('.select--country').on('keyup change', function(e) {
                if($(this).val() == null) {
                    return;
                }
                $.ajax({
                    url: "/PostcodenlApi/countrycheck",
                    dataType: "json",
                    data: {
                        country: $(this).val()
                    },
                    cache: false,
                    success: function(json) {
                        if(json.isSupported) {
                            autocomplete.setCountry(json.iso3);
                            //Show autocomplete field, hide others
                            self.find('.postcodenl_autocomplete').css('display', 'block');
                            self.find('.shopware_default').css('display', 'none');
                        } else {
                            //vice versa
                            self.find('.postcodenl_autocomplete').css('display', 'none');
                            self.find('.shopware_default').css('display', 'block');
                        }

                        // Reset address values
                        self.find('.register--field-autocompleteaddress').val('');
                        self.find('.register--field-street').val('');
                        self.find('.register--field-number').val('');
                        self.find('.register--field-number-addition').val('');
                        self.find('.register--field-zipcode').val('');
                        self.find('.register--field-city').val('');
                    }
                });
            }).trigger('change');
        });
    };

    $(document).ready(function () {
        $('.register--address, .register--shipping').PostcodeNl();
    });
})(jQuery, window);
