{extends file="parent:frontend/register/index.tpl"}

{*Add controller to registration page to handle API Communication*}
{block  name="frontend_index_header_javascript_inline" append}
    var postcodenl_api = "{url controller=PostcodenlApi}";
{/block}