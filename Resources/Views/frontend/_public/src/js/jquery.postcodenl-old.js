// ;(function($, window) {
//     'use strict';
//
//     console.log('Postcodenl plugin loaded');
    //
    // $.plugin('Postcodenl',{
    //
    //     validate: function (values) {
    //
    //         $.ajax({
    //             'url': '/PostcodenlApi',
    //             'dataType': 'json',
    //             'data': values,
    //             'cache': false,
    //             'success': function (address) {
    //
    //                 if (address !== false) {
    //                     var street = address.addressData.street;
    //                     var number = address.addressData.houseNumber;
    //                     var addition = address.addressData.houseNumberAddition;
    //                     var city = address.addressData.city;
    //
    //                     if (values.type === 'billing') {
    //                         if (street){
    //                         $('#street').val(street + " " + number + " " + addition);
    //                         $('#city').val(city);
    //                     } else {
    //                         $('#city').val('');
    //                         $('#street').val('Geen overeenkomst gevonden');
    //                         }
    //                     }
    //                     else if (values.type === 'shipping') {
    //                         $('#street2').val(street + " " + number + " " + addition);
    //                         $('#city2').val(city);
    //                     } else {
    //                         $('#city2').val('');
    //                         $('#street2').val('Geen overeenkomst gevonden');
    //                     }
    //                 }
    //             }
    //         });
    //
    //     },
    //
    //     getAddress: function(type){
    //
    //         var values = {};
    //
    //         values['country'] = $('#country').val();
    //         values['zipcode'] = $('#zipcode').val();
    //         values['number'] = $('#number').val();
    //         values['addition'] = $('#number-addition').val();
    //         values['type'] = type;
    //
    //         if(values['zipcode'] && values['number']) {
    //             this.validate(values);
    //         }
    //     },
    //
    //     init: function () {
    //         var me = this;
    //
    //         me.applyDataAttributes();
    //
    //         $('#zipcode,#number,#number-addition,#country').on('keyup change',function(){
    //             me.getAddress('billing');
    //         });
    //
    //         $('#zipcode2,#number2,#number-addition2,#country2').on('keyup change',function(){
    //             me.getAddress('shipping');
    //         });
    //     }
    // });

//     $(document).ready(function () {
//         // $("#registration").Postcodenl();
//         // $("form[name='frmAddresses']").Postcodenl();
//         // $.subscribe('plugin/swAddressEditor/onRegisterPlugins', function () {
//         //     $("form[name='frmAddresses']").Postcodenl();
//         // });
//
//         $("#country").on('change', function() {
//             console.log($(this).val());
//             $.ajax({
//                 url: "/PostcodenlApi/countrycheck",
//                 dataType: "json",
//                 data: {
//                     country: $('#country').val()
//                 },
//                 cache: false,
//                 success: function(data) {
//                     console.log(data);
//                 }
//             })
//         });
//
//         var inputElement = $("#autocompleteAddress").first();
//         var autocomplete = new PostcodeNl.AutocompleteAddress(inputElement[0], {
//             autocompleteUrl: '/PostcodenlApi/autocomplete',
//             addressDetailsUrl: '/PostcodenlApi/address-details',
//         });
//
//         inputElement.on('autocomplete-select', function(e) {
//             console.log(e);
//
//             if(e.detail.precision == 'Address') {
//                 autocomplete.getDetails(e.detail.context, function(json) {
//                     console.log(json);
//
//                     //$('#' + prefix + 'AddressStreet').val(json.address.street + ' ' + json.address.building);
//                     //$('#' + prefix + 'AddressZipcode').val(json.address.postcode);
//                     //$('#' + prefix + 'AddressCity').val(json.address.locality);
//                 });
//             }
//         });
//     });
// })(jQuery, window);


(function($, window) {
    'use strict';

    $.fn.PostcodeNl = function() {
        return this.each(function() {
            var self = $(this);
            //var prefix;
            var autocomplete;

            // if((prefix = self.data('address-type')) === null) {
            //     return;
            // }

            console.log(self, self.find('.register--field-autocompleteaddress'));
            var inputElement = self.find('.register--field-autocompleteaddress').first();


            autocomplete = new PostcodeNl.AutocompleteAddress(inputElement[0], {
                autocompleteUrl: '/api/v1/memo/postcodenl/autocomplete',
                addressDetailsUrl: '/api/v1/memo/postcodenl/address-details',
            });

            inputElement.on('autocomplete-select', function(e) {
                console.log(e);

                if(e.detail.precision == 'Address') {
                    autocomplete.getDetails(e.detail.context, function(json) {
                        console.log(json);

                        $('#' + prefix + 'AddressStreet').val(json.address.street + ' ' + json.address.building);
                        $('#' + prefix + 'AddressZipcode').val(json.address.postcode);
                        $('#' + prefix + 'AddressCity').val(json.address.locality);
                    });
                }
            });

            self.find('.select--country').on('keyup change', function(e) {
                console.log($(this).val());
                $.ajax({
                    url: "/PostcodenlApi/countrycheck",
                    dataType: "json",
                    data: {
                        country: $(this).val()
                    },
                    cache: false,
                    success: function(json) {
                        console.log(json);

                        if(json.isSupported) {
                            autocomplete.setCountry(json.iso3);
                            //Show autocomplete field, hide others
                            self.children('.postcodenl_autocomplete').css('display', 'flex');
                            self.children('.shopware_default').css('display', 'none');
                        } else {
                            //vice versa
                            self.children('.postcodenl_autocomplete').css('display', 'none');
                            self.children('.shopware_default').css('display', 'flex');
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
        $('.register--company, .register--shipping').PostcodeNl();
    });
})(jQuery, window);
