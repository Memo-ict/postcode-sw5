(function($, window) {
    'use strict';

    $.plugin('PostcodeEu',{
        defaults: {
            registerShipping: false,
        },

        init: function() {
            console.log('hello');
            var self = this;
            var debounceTimeout;
            self.inputElement = this.$el.find('#autocompleteAddress, #autocompleteAddress2').first();

            if(self.inputElement.length == 0) {
                return;
            }

            var dutchAddressStreetCityWrapper = this.$el.find('.register--street-city, .address--street-city').first();
            var dutchAddressStreetElement = this.$el.find('#dutchAddressStreet, #dutchAddressStreet2').first();
            var dutchAddressCityElement = this.$el.find('#dutchAddressCity, #dutchAddressCity2').first();
            var dutchAddressZipcodeElement = this.$el.find('#dutchAddressZipcode, #dutchAddressZipcode2').first();
            var dutchAddressHousenumberElement = this.$el.find('#dutchAddressHousenumber, #dutchAddressHousenumber2').first();
            var dutchAddressAdditionElement = this.$el.find('#dutchAddressHousenumberAddition, #dutchAddressHousenumberAddition2').first();
            var dutchAddressNotifications = this.$el.find('.postcode-eu_dutch-address .alert');
            var dutchAddressInputElements = dutchAddressZipcodeElement.add(dutchAddressHousenumberElement).add(dutchAddressAdditionElement);

            var dutchAddressOptions = this.$el.find('.postcode-eu_dutch-address').data();

            dutchAddressInputElements
                .on('keyup', function() {

                    clearTimeout(debounceTimeout);
                    debounceTimeout = setTimeout(function() {
                        dutchAddressStreetCityWrapper.addClass('is--hidden');
                        const zipcode = dutchAddressZipcodeElement.val().trim();
                        const housenumber = dutchAddressHousenumberElement.val().trim();
                        const addition = dutchAddressAdditionElement.val().trim();

                        if(zipcode === '' || housenumber === '') return;

                        $.ajax({
                            url: `${window.controller.postcode_eu_api}/dutch-address`,
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
                                } else if(addition !== null && addition !== '') {
                                    streetParts.push(addition);
                                }


                                self.$el.find('#street, #street2').val(streetParts.join(' '));
                                self.$el.find('#zipcode, #zipcode2').val(json.postcode);
                                self.$el.find('#city, #city2').val(json.city);
                                dutchAddressStreetElement.val(json.street);
                                dutchAddressCityElement.val(json.city);
                                dutchAddressZipcodeElement.val(json.postcode);

                                dutchAddressStreetElement.filter(':not(:focus)').trigger('blur');
                                dutchAddressCityElement.filter(':not(:focus)').trigger('blur');

                                self.$el.closest('form').find('button:submit, input:submit').attr('disabled', false);

                                if(dutchAddressOptions.configOverrideAllow && !dutchAddressStreetCityWrapper.hasClass('is--hidden')) {
                                    return;
                                }

                                dutchAddressNotifications
                                    .css('display', 'none')
                                    .filter('.alert.is--success')
                                    .css('display', 'flex')
                                    .find('.alert--content')
                                    .text(streetParts.join(' ') + ', ' + json.city);
                            },
                            error: function(jqxhr) {
                                dutchAddressNotifications
                                    .css('display', 'none');

                                if(dutchAddressOptions.configOverrideAllow && !dutchAddressStreetCityWrapper.hasClass('is--hidden')) {
                                    return;
                                }

                                let $warningContent = dutchAddressNotifications
                                    .filter('.alert.is--warning')
                                    .css('display', 'flex')
                                    .find('.alert--content')
                                    .text(jqxhr.responseJSON.error.message);

                                if((jqxhr.responseJSON.error.code | 0b100000) > 0 &&
                                    dutchAddressOptions.configOverrideAllow &&
                                    dutchAddressStreetCityWrapper.hasClass('is--hidden'))
                                {
                                    self.$el.closest('form').find('button:submit, input:submit').attr('disabled', true);
                                    $('<a/>').addClass('overrideButton')
                                        .text(self.$el.find('.postcode-eu_dutch-address').data('configOverrideButton'))
                                        .on('click', function() {
                                            dutchAddressNotifications.css('display', 'none');
                                            dutchAddressStreetCityWrapper.removeClass('is--hidden');
                                            self.$el.closest('form').find('button:submit, input:submit').attr('disabled', false);
                                        })
                                        .appendTo($warningContent);
                                }

                                self.$el.find('#zipcode, #zipcode2').val(dutchAddressZipcodeElement.val().toUpperCase());
                                dutchAddressStreetElement.trigger('keyup');
                                dutchAddressCityElement.trigger('keyup');
                            }
                        })
                    }, 500);
                }).trigger('keyup');

            dutchAddressStreetElement
                .on('keyup blur', function() {
                    const streetParts = [];
                    if(dutchAddressStreetElement.val() !== '') {
                        streetParts.push(dutchAddressStreetElement.val());
                    }
                    if(dutchAddressHousenumberElement.val() !== '') {
                        streetParts.push(dutchAddressHousenumberElement.val());
                    }
                    if(dutchAddressAdditionElement.val() !== '') {
                        streetParts.push(dutchAddressAdditionElement.val());
                    }

                    self.$el.find('#street, #street2').val(streetParts.join(' '));
                });
            dutchAddressCityElement
                .on('keyup blur', function() {
                    self.$el.find('#city, #city2').val(dutchAddressCityElement.val());
                });

            self.autocomplete = new PostcodeNl.AutocompleteAddress(self.inputElement[0], {
                autocompleteUrl: `${window.controller.postcode_eu_api}/autocomplete`,
                addressDetailsUrl: `${window.controller.postcode_eu_api}/address-details`,
                autoSelect: true,
            });

            self.inputElement
                .on('change keyup', function(e) {
                    self.inputElement.removeClass('is--valid');
                    self.inputElement.removeClass('is--existing');

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

            self.$el.find('.postcode-eu_autocomplete').each(function() {
                $(this).find(' .alert .alert--content').text($(this).data('autocompleteWarning'));
            })

            let country = null;
            self.$el.find('.select--country').on('keyup change', function(e) {
                if($(this).val() == null) {
                    return;
                }
                $.ajax({
                    url: `${window.controller.postcode_eu_api}/countrycheck`,
                    dataType: "json",
                    data: {
                        country: $(this).val()
                    },
                    cache: false,
                    success: function(json) {
                        self.$el.find('.postcode-eu_autocomplete').find('.is--required').attr('required', false);
                        self.$el.find('.postcode-eu_dutch-address').find('.is--required').attr('required', false);
                        self.$el.find('.shopware_default').find('.is--required').attr('required', false);

                        if(country !== null && country !== json.iso3) {
                            // Reset address values
                            self.$el.find('#autocompleteAddress, #autocompleteAddress2').first().val('');
                            self.$el.find('#street, #street2').first().val('');
                            self.$el.find('#zipcode, #zipcode2').first().val('');
                            self.$el.find('#city, #city2').first().val('');
                            dutchAddressZipcodeElement.val('');
                            dutchAddressHousenumberElement.val('');
                            dutchAddressAdditionElement.val('');
                            dutchAddressStreetElement.val('');
                            dutchAddressCityElement.val('');
                        }

                        country = json.iso3;

                        const required = self.opts.registerShipping
                            ? !self.$el.hasClass('is--hidden')
                            : true

                        if(json.isSupported) {
                            if(json.iso3 === 'NLD' && !json.useAutocomplete) {
                                self.$el.find('.postcode-eu_autocomplete').css('display', 'none');
                                self.$el.find('.postcode-eu_dutch-address').css('display', 'block')
                                    .find('.is--required').attr('required', required);
                                self.$el.find('.shopware_default').css('display', 'none');
                            } else {
                                self.autocomplete.setCountry(json.iso3);
                                //Show autocomplete field, hide others
                                self.$el.find('.postcode-eu_autocomplete').css('display', 'block')
                                    .find('.is--required').attr('required', required);
                                self.$el.find('.postcode-eu_dutch-address').css('display', 'none');
                                self.$el.find('.shopware_default').css('display', 'none');
                            }
                        } else {
                            //vice versa
                            self.$el.find('.postcode-eu_autocomplete').css('display', 'none');
                            self.$el.find('.postcode-eu_dutch-address').css('display', 'none');
                            self.$el.find('.shopware_default').css('display', 'block')
                                .find('.is--required').attr('required', required);
                        }

                    }
                });
            }).filter(function() {
                return ($(this).val() != null);
            }).trigger('change');

            self.$el.find('[name="register[billing][shippingAddress]"]').on('change', function() {
                $('[name="register[shipping][country]"]').trigger('change');
            });
        }
    });

    $(document).ready(function () {
        $('.register--address, .address-form--panel').PostcodeEu();
        $('.register--shipping').PostcodeEu({registerShipping:true});
        $.subscribe('plugin/swAddressEditor/onRegisterPlugins', function () {
            $('.register--address, .address-form--panel').PostcodeEu();
            $('.register--shipping').PostcodeEu({registerShipping:true});
        });
    });

})(jQuery, window);
