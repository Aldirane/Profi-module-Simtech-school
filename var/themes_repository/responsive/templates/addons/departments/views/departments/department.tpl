<style>
.table {
	width: 100%;
	border: none;
	margin-bottom: 20px;
}
.table thead th {
	font-weight: bold;
	text-align: left;
	border: none;
	padding: 10px 15px;
	background: #d8d8d8;
	font-size: 14px;
}
.table thead tr th:first-child {
	border-radius: 8px 0 0 8px;
}
.table thead tr th:last-child {
	border-radius: 0 8px 8px 0;
}
.table tbody td {
	text-align: left;
	border: none;
	padding: 10px 15px;
	font-size: 14px;
	vertical-align: top;
}
.table tbody tr:nth-child(even){
	background: #f3f3f3;
}
.table tbody tr td:first-child {
	border-radius: 8px 0 0 8px;
}
.table tbody tr td:last-child {
	border-radius: 0 8px 8px 0;
}
</style>
<div class="ty-feature">
    <div>
        {if $department_data.main_pair}
        <div class="ty-feature__image">
            {include 
                file="common/image.tpl" 
                images=$department_data.main_pair
                image_width=$settings.Thumbnails.product_lists_thumbnail_width 
                image_height=$settings.Thumbnails.product_lists_thumbnail_height 
                lazy_load=false
                }
        </div>
        {/if}
    </div>
    <div class="ty-feature__description ty-wysiwyg-content">
        <p><strong>Описание:</strong> {$department_data.description nofilter}</p>
        <p><strong>Руководитель:</strong> {$department_data.firstname} {$department_data.lastname}</p>
        <p><strong>Телефон:</strong> {$department_data.phone}</p>
    </div>
</div>


{if $department_data.user_ids}
    {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}
    {$rev=$smarty.request.content_id|default:"pagination_contents"}

    {$person_name_col_width = ($smarty.request.user_type == "UserTypes::CUSTOMER"|enum && $can_view_orders) ? "15%" : "23%"}
    {$email_col_width = ($smarty.request.user_type == "UserTypes::CUSTOMER"|enum && $can_view_orders) ? "15%" : "22%"}

    <div class="table-responsive-wrapper longtap-selection">
        <table 
            width="100%" class="table table-middle table--relative table-responsive table--overflow-hidden">
            <thead data-ca-bulkedit-default-object="true" data-ca-bulkedit-component="defaultObject">
            <tr>
                <th width="{$person_name_col_width}">
                    {include file="addons/departments/views/departments/components/table_col_head.tpl" type="name" text=__("person_name")}
                </th>
                <th width="{$email_col_width}">
                    {include file="addons/departments/views/departments/components/table_col_head.tpl" type="email"}
                </th>
                <th width="14%">
                    {include file="addons/departments/views/departments/components/table_col_head.tpl" type="last_login"}
                </th>
                <th width="15%">
                    {include file="addons/departments/views/departments/components/table_col_head.tpl" text=__("phone")}
                </th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$department_data.user_ids item=user}
                <tr>
                    <td width="{$person_name_col_width}" align="center" class="row-status wrap" data-th="{__("name")}">
                        {if $user.firstname || $user.lastname}
                            <p>{$user.lastname} {$user.firstname}</p>
                        {else}-
                        {/if}
                    </td>
                    <td width="{$email_col_width}" align="center" data-th="{__("email")}">
                        <a class="row-status" href="mailto:{$user.email|escape:url}">{$user.email}</a>
                    </td>
                    <td width="14%" align="center" class="row-status" data-th="{__("last_login")}">
                        {if $user.last_login}
                            {$user.last_login|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
                        {else}
                        {/if}
                    </td>
                    <td width="15%" align="center" class="row-status" data-th="{__("phone")}">
                        <a href="tel:{$user.phone}">
                            <bdi>{$user.phone}</bdi>
                        </a>
                    </td>
                </tr>
            {/foreach}
            </tbody>            
        </table>
    </div>
    {if !$no_pagination}
        {include file="common/pagination.tpl"}
    {/if}
{/if}
{capture name="mainbox_title"}{$department_data.department nofilter}{/capture}

