{extends file='parent:frontend/address/form.tpl'}


{block name="frontend_address_form_fieldset_address"}
    {* Salutation *}
    {block name='frontend_address_form_input_salutation'}
        <div class="address--salutation field--select select-field">

            {getSalutations variable="salutations"}

            <select name="{$inputPrefix}[salutation]"
                    id="salutation"
                    required="required"
                    aria-required="true"
                    class="is--required{if $error_flags.salutation} has--error{/if}">
                <option value="" disabled="disabled"{if $formData.salutation eq ""} selected="selected"{/if}>{s name='RegisterPlaceholderSalutation' namespace="frontend/register/personal_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}</option>

                {foreach $salutations as $key => $label}
                    <option value="{$key}"{if $formData.salutation eq $key} selected="selected"{/if}>{$label}</option>
                {/foreach}
            </select>
        </div>
    {/block}

    {* Title *}
    {block name='frontend_register_personal_fieldset_input_title'}
        {if {config name="displayprofiletitle"}}
            <div class="register--title">
                <input autocomplete="section-personal title"
                       name="{$inputPrefix}[title]"
                       type="text"
                       placeholder="{s name='RegisterPlaceholderTitle' namespace="frontend/register/personal_fieldset"}{/s}"
                       id="title"
                       value="{$formData.title|escape}"
                       class="address--field{if $error_flags.title} has--error{/if}" />
            </div>
        {/if}
    {/block}

    {* Firstname *}
    {block name='frontend_address_form_input_firstname'}
        <div class="address--firstname">
            <input autocomplete="section-billing billing given-name"
                   name="{$inputPrefix}[firstname]"
                   type="text"
                   required="required"
                   aria-required="true"
                   placeholder="{s name='RegisterShippingPlaceholderFirstname' namespace="frontend/register/shipping_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                   id="firstname2"
                   value="{$formData.firstname|escape}"
                   class="address--field is--required{if $error_flags.firstname} has--error{/if}"/>
        </div>
    {/block}

    {* Lastname *}
    {block name='frontend_address_form_input_lastname'}
        <div class="address--lastname">
            <input autocomplete="section-billing billing family-name"
                   name="{$inputPrefix}[lastname]"
                   type="text"
                   required="required"
                   aria-required="true"
                   placeholder="{s name='RegisterShippingPlaceholderLastname' namespace="frontend/register/shipping_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                   id="lastname2"
                   value="{$formData.lastname|escape}"
                   class="address--field is--required{if $error_flags.lastname} has--error{/if}"/>
        </div>
    {/block}

    {* Country *}
    {block name='frontend_address_form_input_country'}
        <div class="address--country field--select select-field">
            <select name="{$inputPrefix}[country]"
                    data-address-type="address"
                    id="country"
                    required="required"
                    aria-required="true"
                    class="select--country is--required{if $error_flags.country} has--error{/if}">
                <option disabled="disabled" value="" selected="selected">{s name='RegisterBillingPlaceholderCountry' namespace="frontend/register/billing_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}</option>
                {foreach $countryList as $country}
                    {block name="frontend_address_form_input_country_option"}
                        {if $isShipping && $country.allow_shipping || !$isShipping}
                            <option value="{$country.id}" {if $country.id eq $formData.country.id}selected="selected"{/if} {if $country.states}stateSelector="country_{$country.id}_states"{/if}>
                                {$country.countryname}
                            </option>
                        {/if}
                    {/block}
                {/foreach}
            </select>
        </div>
    {/block}

    {* Country state *}
    {block name='frontend_address_form_input_country_states'}
        <div class="country-area-state-selection">
            {foreach $countryList as $country}
                {block name="frontend_address_form_input_country_states_item"}
                    {if $country.states}
                        <div data-country-id="{$country.id}"
                             data-address-type="address"
                             class="address--state-selection field--select select-field{if $country.id != $formData.country.id} is--hidden{/if}">
                            <select {if $country.id != $formData.country.id}disabled="disabled"{/if}
                                    name="{$inputPrefix}[state]"{if $country.force_state_in_registration}
                                required="required"
                                aria-required="true"{/if}
                                    class="select--state {if $country.force_state_in_registration}is--required{/if}{if $error_flags.state} has--error{/if}">
                                <option value="" selected="selected"{if $country.force_state_in_registration} disabled="disabled"{/if}>{s name='RegisterBillingLabelState' namespace="frontend/register/billing_fieldset"}{/s}{if $country.force_state_in_registration}{s name="RequiredField" namespace="frontend/register/index"}{/s}{/if}</option>
                                {foreach $country.states as $state}
                                    {block name="frontend_address_form_input_country_states_item_option"}
                                        <option value="{$state.id}" {if $state.id eq $formData.state.id}selected="selected"{/if}>
                                            {$state.name}
                                        </option>
                                    {/block}
                                {/foreach}
                            </select>
                        </div>
                    {/if}
                {/block}
            {/foreach}
        </div>
    {/block}

    <div class="postcodenl_autocomplete">
        {block name="frontend_address_form_input_autocomplete"}
            <div>
                <input type="text"
                       class="address--field address--autocompleteaddress is--required{if isset($error_flags.street)} has--error{/if}"
                       id="autocompleteAddress"
                       required="required"
                       aria-required="true"
                       placeholder="{s name='RegisterBillingPlaceholderStreet'}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                       name="{$inputPrefix}[attribute][postcodenlAutocompleteSupport]"
                       data-value="{$formData.attribute.postcodenlAutocompleteSupport|escape}"
                       value="{$formData.attribute.postcodenlAutocompleteSupport|escape}">
                {include file="frontend/_includes/messages.tpl" type="warning" content="Please select a valid address from the dropdown list."}
            </div>
        {/block}
    </div>

    <div class="postcodenl_dutch-address">
        <div class="address--zip-city">
            {block name="frontend_address_form_input_dutch-address_zipcode"}
                <input type="text"
                       class="address--field address--spacer address--field-dutch-address_zipcode is--required{if isset($error_flags.street)} has--error{/if}"
                       id="dutchAddressZipcode"
                       required="required"
                       aria-required="true"
                       placeholder="{s name='RegisterShippingPlaceholderZipcode'}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                       name="{$inputPrefix}[zipcode]"
                       value="{$formData.zipcode|escape}">
            {/block}
            {block name="frontend_address_form_input_dutch-address_housenumber"}
                <input type="text"
                       class="address--field address--spacer address--field-dutch-address_housenumber is--required{if isset($error_flags.street)} has--error{/if}"
                       id="dutchAddressHousenumber"
                       required="required"
                       aria-required="true"
                       placeholder="{s name='housenumber' namespace="frontend/postcodenl"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                       name="{$inputPrefix}[attribute][postcodenlHousenumber]"
                       value="{$formData.attribute.postcodenlHousenumber|escape}">
            {/block}
            {block name="frontend_address_form_input_dutch-address_housenumber-addition"}
                <input type="text"
                       class="address--field address--field-dutch-address_housenumber-addition{if isset($error_flags.street)} has--error{/if}"
                       id="dutchAddressHousenumberAddition"
                       placeholder="{s name='addition' namespace="frontend/postcodenl"}{/s}"
                       name="{$inputPrefix}[attribute][postcodenlHousenumberAddition]"
                       value="{$formData.attribute.postcodenlHousenumberAddition|escape}">
            {/block}
        </div>
        {if {config name=memoAllowDutchAddressOverride}}
            <div class="register--street-city">
                {block name='frontend_address_form_input_street'}
                    <input autocomplete="section-billing billing street-address"
                           name="{$inputPrefix}[attribute][postcodenlStreetname]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='street' namespace="frontend/postcodenl"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="dutchAddressStreet"
                           value="{$formData.attribute.postcodenlStreetname|escape}"
                           class="address--field address--spacer address--field-street is--required{if isset($error_flags.street)} has--error{/if}" />
                {/block}
                {block name='frontend_address_form_input_city'}
                    <input autocomplete="section-billing billing city"
                           name="{$inputPrefix}[city]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterBillingPlaceholderCity'}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="dutchAddressCity"
                           value="{$formData.city|escape}"
                           class="address--field address--field-city is--required{if isset($error_flags.street)} has--error{/if}" />
                {/block}
            </div>
        {else}
            {include file="frontend/_includes/messages.tpl" type="warning"}
            {include file="frontend/_includes/messages.tpl" type="success"}
        {/if}
    </div>

    <div class="shopware_default">
        {* Street *}
        {block name='frontend_address_form_input_street'}
            <div class="address--street">
                <input autocomplete="section-billing billing street-address"
                       name="{$inputPrefix}[street]"
                       type="text"
                       required="required"
                       aria-required="true"
                       placeholder="{s name='RegisterBillingPlaceholderStreet' namespace="frontend/register/billing_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                       id="street"
                       value="{$formData.street|escape}"
                       class="address--field address--field-street is--required{if $error_flags.street} has--error{/if}"/>
            </div>
        {/block}

        {* Additional Address Line 1 *}
        {block name='frontend_address_form_input_addition_address_line1'}
            {if {config name=showAdditionAddressLine1}}
                <div class="address--additional-line1">
                    <input autocomplete="section-billing billing address-line2"
                           name="{$inputPrefix}[additionalAddressLine1]"
                           type="text"
                            {if {config name=requireAdditionAddressLine1}} required="required" aria-required="true"{/if}
                           placeholder="{s name='RegisterLabelAdditionalAddressLine1'  namespace="frontend/register/shipping_fieldset"}{/s}{if {config name=requireAdditionAddressLine1}}{s name="RequiredField" namespace="frontend/register/index"}{/s}{/if}"
                           id="additionalAddressLine1"
                           value="{$formData.additionalAddressLine1|escape}"
                           class="address--field{if {config name=requireAdditionAddressLine1}} is--required{/if}{if $error_flags.additionalAddressLine1 && {config name=requireAdditionAddressLine1}} has--error{/if}"/>
                </div>
            {/if}
        {/block}

        {* Additional Address Line 2 *}
        {block name='frontend_address_form_input_addition_address_line2'}
            {if {config name=showAdditionAddressLine2}}
                <div class="address--additional-field2">
                    <input autocomplete="section-billing billing address-line3"
                           name="{$inputPrefix}[additionalAddressLine2]"
                           type="text"
                            {if {config name=requireAdditionAddressLine2}} required="required" aria-required="true"{/if}
                           placeholder="{s name='RegisterLabelAdditionalAddressLine2'  namespace="frontend/register/shipping_fieldset"}{/s}{if {config name=requireAdditionAddressLine2}}{s name="RequiredField" namespace="frontend/register/index"}{/s}{/if}"
                           id="additionalAddressLine2"
                           value="{$formData.additionalAddressLine2|escape}"
                           class="address--field{if {config name=requireAdditionAddressLine2}} is--required{/if}{if $error_flags.additionalAddressLine2 && {config name=requireAdditionAddressLine2}} has--error{/if}"/>
                </div>
            {/if}
        {/block}

        {* Zip + City *}
        {block name='frontend_address_form_input_zip_and_city'}
            <div class="address--zip-city">
                {if {config name=showZipBeforeCity}}
                    <input autocomplete="section-billing billing postal-code"
                           name="{$inputPrefix}[zipcode]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterBillingPlaceholderZipcode' namespace="frontend/register/billing_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="zipcode"
                           value="{$formData.zipcode|escape}"
                           class="address--field address--spacer address--field-zipcode is--required{if $error_flags.zipcode} has--error{/if}"/>
                    <input autocomplete="section-billing billing address-level2"
                           name="{$inputPrefix}[city]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterBillingPlaceholderCity' namespace="frontend/register/billing_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="city"
                           value="{$formData.city|escape}"
                           size="25"
                           class="address--field address--field-city is--required{if $error_flags.city} has--error{/if}"/>
                {else}
                    <input autocomplete="section-billing billing address-level2"
                           name="{$inputPrefix}[city]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterBillingPlaceholderCity' namespace="frontend/register/billing_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="city"
                           value="{$formData.city|escape}"
                           size="25"
                           class="address--field address--spacer address--field-city is--required{if $error_flags.city} has--error{/if}"/>
                    <input autocomplete="section-billing billing postal-code"
                           name="{$inputPrefix}[zipcode]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterBillingPlaceholderZipcode' namespace="frontend/register/billing_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="zipcode"
                           value="{$formData.zipcode|escape}"
                           class="address--field address--field-zipcode is--required{if $error_flags.zipcode} has--error{/if}"/>
                {/if}
            </div>
        {/block}
    </div>

    {* Phone *}
    {block name='frontend_address_form_input_phone'}
        {if {config name=showPhoneNumberField}}
            <div class="address--phone">
                <input autocomplete="section-personal tel" name="{$inputPrefix}[phone]"
                       type="tel"
                        {if {config name=requirePhoneField}} required="required" aria-required="true"{/if}
                       placeholder="{s name='RegisterPlaceholderPhone' namespace="frontend/register/personal_fieldset"}{/s}{if {config name=requirePhoneField}}{s name="RequiredField" namespace="frontend/register/index"}{/s}{/if}"
                       id="phone"
                       value="{$formData.phone|escape}"
                       class="address--field{if {config name=requirePhoneField}} is--required{/if}{if $error_flags.phone && {config name=requirePhoneField}} has--error{/if}"/>
            </div>
        {/if}
    {/block}

    {block name='frontend_address_form_input_set_default_shipping'}
        {if !$formData.id || $sUserData.additional.user.default_shipping_address_id != $formData.id}
            <div class="address--default-shipping">
                <input type="checkbox"
                       id="set_default_shipping"
                       name="{$inputPrefix}[additional][setDefaultShippingAddress]"
                       value="1" />
                <label for="set_default_shipping">{s name="AddressesSetAsDefaultShippingAction"}{/s}</label>
            </div>
        {/if}
    {/block}

    {block name='frontend_address_form_input_set_default_billing'}
        {if !$formData.id || $sUserData.additional.user.default_billing_address_id != $formData.id}
            <div class="address--default-billing">
                <input type="checkbox"
                       id="set_default_billing"
                       name="{$inputPrefix}[additional][setDefaultBillingAddress]"
                       value="1" />
                <label for="set_default_billing">{s name="AddressesSetAsDefaultBillingAction"}{/s}</label>
            </div>
        {/if}
    {/block}
{/block}
