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

            var dutchAddressStreetCityWrapper = this.$el.find('.register--street-city, .address--street-city').first();
            var dutchAddressStreetElement = this.$el.find('#dutchAddressStreet, #dutchAddressStreet2').first();
            var dutchAddressCityElement = this.$el.find('#dutchAddressCity, #dutchAddressCity2').first();
            var dutchAddressZipcodeElement = this.$el.find('#dutchAddressZipcode, #dutchAddressZipcode2').first();
            var dutchAddressHousenumberElement = this.$el.find('#dutchAddressHousenumber, #dutchAddressHousenumber2').first();
            var dutchAddressAdditionElement = this.$el.find('#dutchAddressHousenumberAddition, #dutchAddressHousenumberAddition2').first();
            var dutchAddressNotifications = this.$el.find('.postcodenl_dutch-address .alert');
            var dutchAddressInputElements = dutchAddressZipcodeElement.add(dutchAddressHousenumberElement).add(dutchAddressAdditionElement);

            dutchAddressInputElements
                .on('keyup blur', function() {
                    clearTimeout(debounceTimeout);
                    debounceTimeout = setTimeout(function() {
                        const zipcode = dutchAddressZipcodeElement.val().trim();
                        const housenumber = dutchAddressHousenumberElement.val().trim();
                        const addition = dutchAddressAdditionElement.val().trim();

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
                                } else if(addition !== null && addition !== '') {
                                    streetParts.push(addition);
                                }

                                self.$el.find('#street, #street2').val(streetParts.join(' '));
                                self.$el.find('#zipcode, #zipcode2').val(json.postcode);
                                self.$el.find('#city, #city2').val(json.city);
                                dutchAddressStreetElement.val(json.street);
                                dutchAddressCityElement.val(json.city);
                                dutchAddressZipcodeElement.val(json.postcode);

                                if(!dutchAddressStreetCityWrapper.hasClass('is--hidden')) {
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

                                if(!dutchAddressStreetCityWrapper.hasClass('is--hidden')) {
                                    return;
                                }

                                let $warningContent = dutchAddressNotifications
                                    .filter('.alert.is--warning')
                                    .css('display', 'flex')
                                    .find('.alert--content')
                                    .text(jqxhr.responseJSON.error.message);

                                if((jqxhr.responseJSON.error.code | 0b100000) > 0 &&
                                    dutchAddressStreetCityWrapper.hasClass('is--hidden'))
                                {
                                    $('<a/>').addClass('overrideButton')
                                        .text(self.$el.find('.postcodenl_dutch-address').data('configOverrideButton'))
                                        .on('click', function() {
                                            dutchAddressNotifications.css('display', 'none');
                                            dutchAddressStreetCityWrapper.removeClass('is--hidden');
                                        })
                                        .appendTo($warningContent);
                                }

                                self.$el.find('#zipcode, #zipcode2').val(dutchAddressZipcodeElement.val().toUpperCase());
                                dutchAddressStreetElement.trigger('keyup');
                                dutchAddressCityElement.trigger('keyup');
                            }
                        })
                    }, 500);
                })

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
                autocompleteUrl: '/PostcodenlApi/autocomplete',
                addressDetailsUrl: '/PostcodenlApi/address-details',
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

            self.$el.find('.postcodenl_autocomplete').each(function() {
                $(this).find(' .alert .alert--content').text($(this).data('autocompleteWarning'));
            })

            let country = null;
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

                    }
                });
            }).trigger('change');
        }
    });

    $(document).ready(function () {
        $('.register--address, .register--shipping, .address-form--panel').PostcodeNl();
        $.subscribe('plugin/swAddressEditor/onRegisterPlugins', function () {
            $(".register--address, .register--shipping, .address-form--panel").PostcodeNl();
        });
    });

})(jQuery, window);
