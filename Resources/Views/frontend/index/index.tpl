{extends file="parent:frontend/index/index.tpl"}

{*Add controller to registration page to handle API Communication*}
{block  name="frontend_index_header_javascript_inline" append}
    controller['postcodenl_api'] = "{url controller=PostcodenlApi}";
{/block}
