<div class="buttons-container clearfix">
    <div class="ty-float-right">
        {include file="buttons/login.tpl" but_name="dispatch[verify_code.validation]" but_role="submit"}
    </div>
    <div class="ty-login__remember-me">
        <label for="remember_me_{$id}" class="ty-login__remember-me-label"><input class="checkbox" type="checkbox" name="remember_me" id="remember_me_{$id}" value="Y" />{__("remember_me")}</label>
    </div>
</div>