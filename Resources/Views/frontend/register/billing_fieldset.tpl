{extends file='parent:frontend/register/billing_fieldset.tpl'}

{* Change order of fields for typing zipcode first *}

{block name='frontend_register_billing_fieldset_body'}
    <div class="panel--body is--wide">
        {* Country *}
        {block name='frontend_register_billing_fieldset_input_country'}
            <div class="register--country field--select select-field">
                <select name="register[billing][country]"
                        data-address-type="billing"
                        id="country"
                        required="required"
                        aria-required="true"
                        class="select--country is--required{if isset($error_flags.country)} has--error{/if}">


                    {foreach $country_list as $country}
                        <option value="{$country.id}" {if $country.id eq $form_data.country}selected="selected"{/if} {if $country.states}stateSelector="country_{$country.id}_states"{/if}>
                            {$country.countryname}
                        </option>
                    {/foreach}
                </select>
            </div>
        {/block}

        {* Zip + City *}
        {block name='frontend_register_billing_fieldset_input_zip_and_city'}
            <div class="register--zip-city">
                {if {config name=showZipBeforeCity}}
                    <input autocomplete="section-billing billing postal-code"
                           name="register[billing][zipcode]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterBillingPlaceholderZipcode'}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="zipcode"
                           value="{$form_data.zipcode|escape}"
                           class="register--field register--spacer register--field-zipcode is--required{if isset($error_flags.zipcode)} has--error{/if}" />

                    <input autocomplete="section-billing billing housenumber"
                           name="register[billing][number]"
                           type="number"
                           aria-required="true"
                           placeholder="{s name='RegisterBillingPlaceholderNumber'}00{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="number"
                           value="{$form_data.number|escape}"
                           class="register--field register--spacer register--field-number is--required" />

                    <input autocomplete="section-billing billing number-addition"
                           name="register[billing][number-addition]"
                           type="text"
                           aria-required="true"
                           placeholder="{s name='RegisterBillingPlaceholderNumberAddition'}A,01{/s}"
                           id="number-addition"
                           value="{$form_data.numberAddition|escape}"
                           class="register--field register--spacer register--field-number-addition" />

                    <input autocomplete="section-billing billing address-level2"
                           name="register[billing][city]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterBillingPlaceholderCity'}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="city"
                           value="{$form_data.city|escape}"
                           size="25"
                           class="register--field register--field-city is--required{if isset($error_flags.city)} has--error{/if}" />
                {else}
                    <input autocomplete="section-billing billing address-level2"
                           name="register[billing][city]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterBillingPlaceholderCity'}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="city"
                           value="{$form_data.city|escape}"
                           size="25"
                           class="register--field register--spacer register--field-city is--required{if isset($error_flags.city)} has--error{/if}" />

                    <input autocomplete="section-billing billing postal-code"
                           name="register[billing][zipcode]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterBillingPlaceholderZipcode'}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="zipcode"
                           value="{$form_data.zipcode|escape}"
                           class="register--field register--field-zipcode is--required{if isset($error_flags.zipcode)} has--error{/if}" />
                {/if}
            </div>
        {/block}

        {* Street *}
        {block name='frontend_register_billing_fieldset_input_street'}
            <div class="register--street">
                <input autocomplete="section-billing billing street-address"
                       name="register[billing][street]"
                       type="text"
                       required="required"
                       aria-required="true"
                       placeholder="{s name='RegisterBillingPlaceholderStreet'}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                       id="street"
                       value="{$form_data.street|escape}"
                       class="register--field register--field-street is--required{if isset($error_flags.street)} has--error{/if}" />
            </div>
        {/block}

        {* Additional Address Line 1 *}
        {block name='frontend_register_billing_fieldset_input_addition_address_line1'}
            {if {config name=showAdditionAddressLine1}}
                <div class="register--additional-line1">
                    <input autocomplete="section-billing billing address-line2"
                           name="register[billing][additionalAddressLine1]"
                           type="hidden"{if {config name=requireAdditionAddressLine1}} required="required" aria-required="true"{/if}
                           placeholder="{s name='RegisterLabelAdditionalAddressLine1'}{/s}{if {config name=requireAdditionAddressLine1}}{s name="RequiredField" namespace="frontend/register/index"}{/s}{/if}"
                           id="additionalAddressLine1"
                           value="{$form_data.additionalAddressLine1|escape}"
                           class="register--field{if {config name=requireAdditionAddressLine1}} is--required{/if}{if isset($error_flags.additionalAddressLine1) && {config name=requireAdditionAddressLine1}} has--error{/if}" />
                </div>
            {/if}
        {/block}

        {* Additional Address Line 2 *}
        {block name='frontend_register_billing_fieldset_input_addition_address_line2'}
            {if {config name=showAdditionAddressLine2}}
                <div class="register--additional-field2">
                    <input autocomplete="section-billing billing address-line3"
                           name="register[billing][additionalAddressLine2]"
                           type="hidden"{if {config name=requireAdditionAddressLine2}} required="required" aria-required="true"{/if}
                           placeholder="{s name='RegisterLabelAdditionalAddressLine2'}{/s}{if {config name=requireAdditionAddressLine2}}{s name="RequiredField" namespace="frontend/register/index"}{/s}{/if}"
                           id="additionalAddressLine2"
                           value="{$form_data.additionalAddressLine2|escape}"
                           class="register--field{if {config name=requireAdditionAddressLine2}} is--required{/if}{if isset($error_flags.additionalAddressLine2) && {config name=requireAdditionAddressLine2}} has--error{/if}" />
                </div>
            {/if}
        {/block}





        {* Country state *}
        {block name='frontend_register_billing_fieldset_input_country_states'}
            <div class="country-area-state-selection">
                {foreach $country_list as $country}
                    {if $country.states}
                        <div data-country-id="{$country.id}" data-address-type="billing" class="register--state-selection field--select select-field{if $country.id != $form_data.country} is--hidden{/if}">
                            <select {if $country.id != $form_data.country}disabled="disabled"{/if} name="register[billing][country_state_{$country.id}]"{if $country.force_state_in_registration} required="required" aria-required="true"{/if} class="select--state {if $country.force_state_in_registration}is--required{/if}{if isset($error_flags.state)} has--error{/if}">
                                <option value="" selected="selected"{if $country.force_state_in_registration} disabled="disabled"{/if}>{s name='RegisterBillingLabelState'}{/s}{if $country.force_state_in_registration}{s name="RequiredField" namespace="frontend/register/index"}{/s}{/if}</option>
                                {assign var="stateID" value="country_state_`$country.id`"}
                                {foreach $country.states as $state}
                                    <option value="{$state.id}" {if $state.id eq $form_data['state']}selected="selected"{/if}>
                                        {$state.name}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    {/if}
                {/foreach}
            </div>
        {/block}

        {* Alternative *}
        {block name='frontend_register_billing_fieldset_different_shipping'}
            {if !$update}
                <div class="register--alt-shipping">
                    <input name="register[billing][shippingAddress]" type="checkbox" id="register_billing_shippingAddress" value="1" {if $form_data.shippingAddress}checked="checked"{/if} />
                    <label for="register_billing_shippingAddress">{s name='RegisterBillingLabelShipping'}{/s}</label>
                </div>
            {/if}
        {/block}
    </div>
{/block}