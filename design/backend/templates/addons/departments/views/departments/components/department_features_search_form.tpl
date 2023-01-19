<div class="sidebar-row">
    <h6>{__("admin_search_title")}</h6>

    <form action="{""|fn_url}" name="product_features_search_form" method="get">

        {capture name="simple_search"}
            <div class="sidebar-field">
                <label for="dname">{__("name")}:</label>
                <input type="text" name="department_name" id="dname" value="{$search.department_name}" size="30" />
            </div>
            <div class="control-group">
                <label for="status" class="control-label">{__("status")}:</label>
                <div class="controls">
                    <select name="status" id="status">
                        <option value="">{__("all")}</option>
                        <option value="A">{__("Active")}</option>
                        <option value="D">{__("Disabled")}</option>
                        <option value="H">{__("Hidden")}</option>
                    </select>
                </div>
            </div>
        {/capture}
        {include 
            file="common/advanced_search.tpl" 
            simple_search=$smarty.capture.simple_search 
            advanced_search=$smarty.capture.advanced_search 
            dispatch=$dispatch 
            view_type="product_features" 
            method="GET"
        }
    </form>
</div>