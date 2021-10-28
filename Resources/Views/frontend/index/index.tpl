{extends file="parent:frontend/index/index.tpl"}

{*Add controller to registration page to handle API Communication*}
{block  name="frontend_index_header_javascript_inline" append}
    controller['postcode_eu_api'] = "{url controller=PostcodeEuApi}";
{/block}
