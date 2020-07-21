(function($, window) {
    'use strict';

    $.plugin('PostcodeNl',{
        init: function() {
            var self = this;
            var debounceTimeout;
            self.inputElement = this.$el.find('#autocompleteAddress, #autocompleteAddress2').first();

            if(self.inputElement.length == 0) {
                return;
            }

            // this.$el.find('[required]').data('required', true);

            var dutchAddressZipcodeElement = this.$el.find('#dutchAddressZipcode').first();
            var dutchAddressHousenumberElement = this.$el.find('#dutchAddressHousenumber').first();
            var dutchAddressAdditionElement = this.$el.find('#dutchAddressHousenumberAddition').first();
            var dutchAddressNotifications = this.$el.find('.postcodenl_dutch-address .alert');
            var dutchAddressInputElements = dutchAddressZipcodeElement.add(dutchAddressHousenumberElement).add(dutchAddressAdditionElement);

            dutchAddressInputElements
                .on('keyup blur', function() {
                    clearTimeout(debounceTimeout);
                    debounceTimeout = setTimeout(function() {
                        const zipcode = dutchAddressZipcodeElement.val();
                        const housenumber = dutchAddressHousenumberElement.val();
                        const addition = dutchAddressAdditionElement.val();

                        if(zipcode === '' || housenumber === '') return;

                        $.ajax({
                            url: "/PostcodenlApi/dutch-address",
                            dataType: "json",
                            data: {
                                zipcode: zipcode,
                                housenumber: housenumber,
                                addition: addition,
                            },
                            cache: false,
                            success: function(json) {
                                let streetParts = [];
                                if(json.street !== null && json.street !== '') {
                                    streetParts.push(json.street);
                                }
                                if(json.houseNumber !== null && json.houseNumber !== '') {
                                    streetParts.push(json.houseNumber);
                                }
                                if(json.houseNumberAddition !== null && json.houseNumberAddition !== '') {
                                    streetParts.push(json.houseNumberAddition);
                                }

                                dutchAddressNotifications
                                    .css('display', 'none')
                                    .filter('.alert.is--success')
                                    .css('display', 'flex')
                                    .find('.alert--content')
                                    .text(streetParts.join(' ') + ', ' + json.city)

                                self.$el.find('#street, #street2').val(streetParts.join(' '));
                                self.$el.find('#zipcode, #zipcode2').val(json.postcode);
                                self.$el.find('#city, #city2').val(json.city);
                            },
                            error: function(jqxhr) {
                                dutchAddressNotifications
                                    .css('display', 'none')
                                    .filter('.alert.is--warning')
                                    .css('display', 'flex')
                                    .find('.alert--content')
                                    .text(jqxhr.responseJSON.error);

                                self.$el.find('#street, #street2').val('');
                                self.$el.find('#zipcode, #zipcode2').val('');
                                self.$el.find('#city, #city2').val('');
                            }
                        })
                    }, 500);
                })
            dutchAddressZipcodeElement
                .filter(function() {
                    return ($(this).val() != null);
                })
                .trigger('blur');

            self.autocomplete = new PostcodeNl.AutocompleteAddress(self.inputElement[0], {
                autocompleteUrl: '/PostcodenlApi/autocomplete',
                addressDetailsUrl: '/PostcodenlApi/address-details',
            });
            self.autocomplete.reset();

            self.inputElement
                .on('change keyup', function(e) {
                    self.inputElement.removeClass('is--valid');
                    self.$el.find('#street, #street2').first().val("");
                    self.$el.find('#zipcode, #zipcode2').first().val("");
                    self.$el.find('#city, #city2').first().val("");
                })
                .on('autocomplete-select', function(e) {
                    if(e.detail.precision == 'Address') {
                        self.autocomplete.getDetails(e.detail.context, function(json) {
                            self.inputElement.addClass('is--valid').blur();
                            self.$el.find('#street, #street2').first().val(json.address.street + " " + json.address.building);
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
                        self.$el.find('.postcodenl_autocomplete').find('.is--required').attr('required', false);
                        self.$el.find('.postcodenl_dutch-address').find('.is--required').attr('required', false);
                        self.$el.find('.shopware_default').find('.is--required').attr('required', false);

                        if(json.isSupported) {
                            if(json.iso3 === 'NLD' && !json.useAutocomplete) {
                                self.$el.find('.postcodenl_autocomplete').css('display', 'none');
                                self.$el.find('.postcodenl_dutch-address').css('display', 'block')
                                    .find('.is--required').attr('required', true);
                                self.$el.find('.shopware_default').css('display', 'none');
                            } else {
                                self.autocomplete.setCountry(json.iso3);
                                //Show autocomplete field, hide others
                                self.$el.find('.postcodenl_autocomplete').css('display', 'block')
                                    .find('.is--required').attr('required', true);
                                self.$el.find('.postcodenl_dutch-address').css('display', 'none');
                                self.$el.find('.shopware_default').css('display', 'none');
                            }
                        } else {
                            //vice versa
                            self.$el.find('.postcodenl_autocomplete').css('display', 'none');
                            self.$el.find('.postcodenl_dutch-address').css('display', 'none');
                            self.$el.find('.shopware_default').css('display', 'block')
                                .find('.is--required').attr('required', true);
                        }

                        // Reset address values
                        self.$el.find('#autocompleteAddress, #autocompleteAddress2').first().val('');
                        self.$el.find('#street, #street2').first().val('');
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
