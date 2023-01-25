{assign var="id" value=$id|default:"main_login"}

{capture name="login"}
<form name="{$id}_form" action="{""|fn_url}" method="post">

    <input type="hidden" name="return_url" value="{$smarty.request.return_url|default:$config.current_url}" />
    <input type="hidden" name="redirect_url" value="{$redirect_url|default:$config.current_url}" />

    <div class="ty-control-group">
        <label for="login_{$id}" class="ty-control-group__label">{__("code")}</label>
        <input type="text" id="login_{$id}" name="code" size="30"/>
    </div>

    <div class="buttons-container clearfix">
        <div class="ty-float-right">
            {include file="buttons/login.tpl" but_name="dispatch[verify_code.get_validation]" but_role="submit"}
        </div>
        <div class="ty-float-left">
            <a class="ty-btn__primary ty-btn" href={"verify_code.get_validation"|fn_url}>{__("send_new_code")}</a>
        </div>
    </div>
</form>
{/capture}


{if $style == "popup"}
    <div id="{$id}_login_popup_form_container">
        {$smarty.capture.login nofilter}
    <!--{$id}_login_popup_form_container--></div>
{else}
    <div class="ty-login">
        {$smarty.capture.login nofilter}
    </div>

    {capture name="mainbox_title"}{__("sign_in")}{/capture}
{/if}