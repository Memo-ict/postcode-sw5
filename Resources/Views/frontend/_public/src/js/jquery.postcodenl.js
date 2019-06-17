;(function($, window) {
    'use strict';

    console.log('Postcodenl plugin loaded');

    $.plugin('Postcodenl',{

        checkZipcode: function(zipcode, countryId){

            var isValid = true;

            switch (countryId) {
                case "2": //Country is germany

                    break;
                case "7": // Country is Belgium



                    break;
                case "21":// Country is Netherlands
                    var regex = /^[1-9][0-9]{3}[\s]?[A-Za-z]{2}$/i;
                    isValid = regex.test(zipcode);
                    break;
                default:
                    break;
            }
            
            return isValid;
        },


        validate: function (values) {

            if (this.checkZipcode(values.zipcode, values.city) === true) {
                $.ajax({
                    'url': '/PostcodenlApi',
                    'dataType': 'json',
                    'data': values,
                    'cache': false,
                    'success': function (address) {

                        if (address !== false) {
                            var street = address.addressData.street;
                            var number = address.addressData.houseNumber;
                            var addition = address.addressData.houseNumberAddition;
                            var city = address.addressData.city;

                            if (values.type === 'billing') {
                                if (street){
                                $('#street').val(street + " " + number + " " + addition);
                                $('#city').val(city);
                            } else {
                                    $('#street').val('Geen overeenkomst gevonden')
                                }
                        }
                        else if (values.type === 'shipping') {
                            $('#street2').val(street + " " + number + " " + addition);
                            $('#city2').val(city);
                        }

                        }
                    }
                });
            }
        },

        init: function () {
            var me = this;

            me.applyDataAttributes();

            $('#zipcode,#number,#number-addition').keyup(function(){


                var values = {};

                values['country'] = $('#country').val();
                values['zipcode'] = $('#zipcode').val();
                values['number'] = $('#number').val();
                values['addition'] = $('#number-addition').val();
                values['type'] = 'billing';

                me.validate(values);

            });

            $('#zipcode2,#number2,#number-addition2').keyup(function(){

                var values = {};
                values['country'] = $('#country2').val();
                values['zipcode'] = $('#zipcode2').val();
                values['number'] = $('#number2').val();
                values['addition'] = $('#number-addition2').val();
                values['type'] = 'shipping';

                me.validate(values);

            });

        }
    }
    );




    $(document).ready(function () {
    $("#registration").Postcodenl();
    $("form[name='frmAddresses']").Postcodenl();
    $.subscribe('plugin/swAddressEditor/onRegisterPlugins', function () {
        $("form[name='frmAddresses']").Postcodenl();

    });

});

})(jQuery, window);
