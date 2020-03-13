(function($, window) {
    'use strict';

    $.plugin('PostcodeNl',{
        init: function() {
            var self = this;
            self.inputElement = this.$el.find('#autocompleteAddress, #autocompleteAddress2').first();

            if(self.inputElement.length == 0) {
                return;
            }

            self.autocomplete = new PostcodeNl.AutocompleteAddress(self.inputElement[0], {
                autocompleteUrl: '/PostcodenlApi/autocomplete',
                addressDetailsUrl: '/PostcodenlApi/address-details',
            });

            self.inputElement.on('autocomplete-select', function(e) {
                if(e.detail.precision == 'Address') {
                    self.autocomplete.getDetails(e.detail.context, function(json) {
                        self.$el.find('#street, #street2').first().val(json.address.street + " " + json.address.building);
                        self.$el.find('#number, #number2').first().val(json.address.buildingNumber);
                        self.$el.find('#number-addition, #number-addition2').first().val(json.address.buildingNumberAddition);
                        self.$el.find('#zipcode, #zipcode2').first().val(json.address.postcode);
                        self.$el.find('#city, #city2').first().val(json.address.locality);
                    });
                }
            });

            self.$el.find('.select--country').on('keyup change', function(e) {
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
                            self.autocomplete.setCountry(json.iso3);
                            //Show autocomplete field, hide others
                            self.$el.find('.postcodenl_autocomplete').css('display', 'block');
                            self.$el.find('.shopware_default').css('display', 'none');
                        } else {
                            //vice versa
                            self.$el.find('.postcodenl_autocomplete').css('display', 'none');
                            self.$el.find('.shopware_default').css('display', 'block');
                        }

                        // Reset address values
                        self.$el.find('#autocompleteAddress, #autocompleteAddress2').first().val('');
                        self.$el.find('#street, #street2').first().val('');
                        self.$el.find('#number, #number2').first().val('');
                        self.$el.find('#number-addition, #number-addition2').first().val('');
                        self.$el.find('#zipcode, #zipcode2').first().first().val('');
                        self.$el.find('#city, #city2').first().first().val('');
                    }
                });
            }).trigger('change');
        }
    });

    $(document).ready(function () {
        $('.register--address, .register--shipping, .address-form--panel').PostcodeNl();
        $.subscribe('plugin/swAddressEditor/onRegisterPlugins', function () {
            $(".register--address, .register--shipping, .address-form--panel").Postcodenl();
        });
    });

})(jQuery, window);
