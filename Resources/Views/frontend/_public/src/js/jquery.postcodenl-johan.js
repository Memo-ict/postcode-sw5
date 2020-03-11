;(function($, window) {
    'use strict';

    console.log('Postcodenl plugin loaded');

    $.plugin('Postcodenl',{
            validate: function (values) {
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
                                    $('#city').val('')
                                    $('#street').val('Geen overeenkomst gevonden')
                                }
                            }
                            else if (values.type === 'shipping') {
                                $('#street2').val(street + " " + number + " " + addition);
                                $('#city2').val(city);
                            } else {
                                $('#city2').val('')
                                $('#street2').val('Geen overeenkomst gevonden')
                            }
                        }
                    }
                });

            },

            getAddress: function(type){

                var values = {};

                values['country'] = $('#country').val();
                values['zipcode'] = $('#zipcode').val();
                values['number'] = $('#number').val();
                values['addition'] = $('#number-addition').val();
                values['type'] = type;

                if(values['zipcode'] && values['number']) {
                    this.validate(values);
                }
            },

            init: function () {
                var me = this;

                me.applyDataAttributes();

                $('#zipcode,#number,#number-addition,#country').on('keyup change',function(){

                    me.getAddress('billing')

                });


                $('#zipcode2,#number2,#number-addition2,#country2').on('keyup change',function(){

                    me.getAddress('shipping')

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
