<?php

/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

use Tygh\Languages\Languages;
use Tygh\Registry;
use Tygh\Tygh;
use Tygh\Navigation\LastView;
use Tygh\Enum\UserTypes;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_get_department_data($params, $lang_code = CART_LANGUAGE, $get_user_data=false)
{
    $department = [];
    if (!empty($params['department_id'])) {
        list($departments) = fn_get_departments($params, 1, $lang_code);
        if (!empty($departments)) {
            $department = reset($departments);
            $department['user_ids'] = fn_department_get_links([
                'department_id' => $department['department_id']
            ]);
        }
    }
    return $department;
}

function fn_get_departments($params = [], $items_per_page = 3, $lang_code = CART_LANGUAGE)
{
    $default_params = array(
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);

    if ($_SERVER['SCRIPT_NAME']=='/' . Registry::get('config.customer_index')) {
        $params['items_per_page'] = 3;
    }

    if (AREA == 'C') {
        $params['status'] = 'A';
        $params['has_director'] = true;
    }

    $sortings = array(
        'timestamp' => '?:departments.timestamp',
        'name' => '?:department_descriptions.department',
        'status' => '?:departments.status',
    );

    $condition = $limit = $join = '';
    
    if (!empty($params['limit'])) {
        $limit = db_quote(' LIMIT 0, ?i', $params['limit']);
    }
    
    $sorting = db_sort($params, $sortings, 'name', 'asc');

    if (!empty($params['user_ids'])) {
        $condition .= db_quote(' AND ?:departments.department_id IN (?n)', explode(',', $params['user_ids']));
    }

    if (!empty($params['department_id'])) {
        $condition .= db_quote(' AND ?:departments.department_id = ?i', $params['department_id']);
    }

    if (!empty($params['has_director'])) {
        $condition .= db_quote(' AND ?:departments.director_id != ?i', 0);
    }

    if (!empty($params['director_id'])) {
        $condition .= db_quote(' AND ?:departments.director_id = ?i', $params['director_id']);
    }
    if (!empty($params['user_id'])) {
        $condition .= db_quote(' AND ?:department_links.user_id = ?i', $params['user_id']);
    }

    if (!empty($params['status'])) {
        $condition .= db_quote(' AND ?:departments.status = ?s', $params['status']);
    }
    
    if (isset($params['department_name']) && fn_string_not_empty($params['department_name'])) {
        $condition .= db_quote(' AND ?:department_descriptions.department LIKE ?l', '%' . trim($params['department_name']) . '%');
    }
    if (isset($params['status']) && fn_string_not_empty($params['status'])) {
        $condition .= db_quote(' AND ?:departments.status LIKE ?l', '%' . trim($params['status']) . '%');
    }

    $fields = array(
        '?:departments.department_id',
        '?:departments.director_id',
        '?:departments.status',
        '?:departments.timestamp',
        '?:department_descriptions.department',
        '?:department_descriptions.description',
        '?:users.user_id',
        '?:users.firstname',
        '?:users.lastname',
        '?:users.phone',
    );

    $join .= db_quote(' LEFT JOIN ?:department_descriptions ON ?:department_descriptions.department_id = ?:departments.department_id AND ?:department_descriptions.lang_code = ?s', $lang_code);
    $join .= db_quote(' LEFT JOIN ?:users ON ?:users.user_id = ?:departments.director_id ');

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:departments $join WHERE 1 $condition");
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }
    $departments = db_get_hash_array(
        "SELECT ?p FROM ?:departments " .
        $join .
        "WHERE 1 ?p ?p ?p",
        'department_id',
        implode(', ', $fields),
        $condition,
        $sorting,
        $limit
    );

    $department_image_ids = array_keys($departments);
    $images = fn_get_image_pairs($department_image_ids, 'department', 'M', true, false, $lang_code);

    foreach ($departments as $department_id => $department) {
        $departments[$department_id]['main_pair'] = !empty($images[$department_id]) ? reset($images[$department_id]) : array();
    }
    return array($departments, $params);
}

function fn_update_department($data, $department_id, $lang_code = DESCR_SL)
{   
    $data['timestamp'] = isset($data['timestamp']) ? fn_parse_date($data['timestamp']) : TIME;

    if (!empty($department_id)) {
        db_query("UPDATE ?:departments SET ?u WHERE department_id = ?i", $data, $department_id);
        db_query("UPDATE ?:department_descriptions SET ?u WHERE department_id = ?i AND lang_code = ?s", $data, $department_id, $lang_code);
    } else {
        $department_id = $data['department_id'] = db_replace_into('departments', $data);
        $new_data = [];
        foreach (Languages::getAll() as $lang) {
            $new_data[] = [
                            'lang_code' => $lang['lang_code'],
                            'department' => $data['department'],
                            'department_id' => $data['department_id'],
                            'description' => $data['description'],
                        ];
        }
        db_query("INSERT INTO ?:department_descriptions ?m", $new_data);
    }
    if (!empty($department_id)) {
        fn_attach_image_pairs('department', 'department', $department_id, $lang_code);
    }

    $user_ids = !empty($data['user_ids']) ? $data['user_ids'] : [];
    
    fn_department_delete_links($department_id);
    
    fn_department_add_links($department_id, $user_ids, $data['director_id']);

    return $department_id;
}


function fn_delete_department($department_id)
{
    if (!empty($department_id)) {
        db_query('DELETE FROM ?:departments WHERE department_id = ?i', $department_id);
        db_query('DELETE FROM ?:department_descriptions WHERE department_id = ?i', $department_id);
        fn_department_delete_links($department_id);
    }
}

function fn_department_delete_links($department_id)
{
    db_query('DELETE FROM ?:department_links WHERE department_id = ?i', $department_id);
}

function fn_department_add_links($department_id, $user_ids, $director_id)
{
    if (!empty($department_id) && !empty($director_id)) { 
        if (!empty($user_ids)) {
            $user_ids = explode(',',$user_ids);
            $data = [];
            foreach ($user_ids as $user_id) {
                $data[] = [
                            'user_id' => $user_id,
                            'department_id' => $department_id,
                        ];
            }
            db_query("INSERT INTO ?:department_links ?m", $data);
        } 
    }
}

function fn_department_get_links($params, $get_user_data=false, $items_per_page=7)
{
    if ($get_user_data==true) {
        $default_params = array(
            'page' => 1,
            'items_per_page' => $items_per_page
        );
        $params = array_merge($default_params, $params);
        $sortings = array(
            'timestamp' => '?:users.last_login',
            'name' => '?:users.firstname',
        );
        $condition=$limit='';
        $sorting = db_sort($params, $sortings, 'name', 'asc');
        $fields = array(
            '?:users.user_id',
            '?:users.status',
            '?:users.last_login',
            '?:users.firstname',
            '?:users.lastname',
            '?:users.email',
            '?:users.phone',
        );
        $condition = db_quote(' AND ?:department_links.department_id = ?i', $params['department_id']);

        $join = db_quote("LEFT JOIN ?:users ON ?:users.user_id = ?:department_links.user_id ");
        if (!empty($params['items_per_page'])) {
            $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:department_links $join WHERE 1 $condition");
            $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        }
        $users = db_get_hash_array("SELECT ?p FROM ?:department_links " . $join . " WHERE 1 ?p ?p ?p", 'user_id', implode(', ',$fields), $condition, $sorting, $limit);
        return array($users, $params);
    }
    return !empty( $params['department_id']) ? db_get_fields('select ?:department_links.user_id from ?:department_links where ?:department_links.department_id= ?i', $params['department_id']) : [];
}

function fn_get_department_users($params, &$auth, $items_per_page = 0, $custom_view = '')
{
    /**
     * Actions before getting users list
     *
     * @param array  $params         Params list
     * @param array  $auth           Auth data
     * @param int    $items_per_page Items per page
     * @param string $custom_view    Custom view
     */
    fn_set_hook('get_users_pre', $params, $auth, $items_per_page, $custom_view);

    // Init filter
    $_view = !empty($custom_view) ? $custom_view : 'users';
    $params = LastView::instance()->update($_view, $params);

    // Set default values to input params
    $default_params = [
        'page'            => 1,
        'items_per_page'  => $items_per_page,
        'extended_search' => true,
    ];

    $params = array_merge($default_params, $params);

    // Define fields that should be retrieved
    $fields = [
        'user_id'    => '?:users.user_id',
        'user_login' => '?:users.user_login',
        'is_root'    => '?:users.is_root',
        'timestamp'  => '?:users.timestamp',
        'user_type'  => '?:users.user_type',
        'status'     => '?:users.status',
        'firstname'  => '?:users.firstname',
        'lastname'   => '?:users.lastname',
        'email'      => '?:users.email',
        'company'    => '?:users.company',
        'company_id' => '?:users.company_id',
        'phone'      => '?:users.phone',
        'last_login' => '?:users.last_login',
    ];

    // Define sort fields
    $sortings = [
        'id'         => '?:users.user_id',
        'username'   => '?:users.user_login',
        'email'      => '?:users.email',
        'name'       => ['?:users.firstname', '?:users.lastname'],
        'date'       => '?:users.timestamp',
        'type'       => '?:users.user_type',
        'status'     => '?:users.status',
        'phone'      => '?:users.phone',
        'last_login' => '?:users.last_login'
    ];

    if ($params['extended_search']) {
        $fields['company_name'] = '?:companies.company as company_name';
        $sortings['company'] = 'company_name';
    }

    if (isset($params['compact']) && $params['compact'] == YesNo::YES) {
        $union_condition = ' OR ';
    } else {
        $union_condition = ' AND ';
    }

    $condition = $compact_fields = array();
    $join = $group = '';

    $group .= ' GROUP BY ?:users.user_id';

    if (isset($params['company']) && fn_string_not_empty($params['company'])) {
        $condition['company'] = db_quote(' AND ?:users.company LIKE ?l', '%' . trim($params['company']) . '%');
    }

    if (isset($params['storefront_id']) && isset($params['user_type']) && UserTypes::isAdmin($params['user_type'])) {
        $condition['storefront_id'] = db_quote(' AND ?:users.storefront_id = ?i', $params['storefront_id']);
    }

    if (!$params['extended_search'] && isset($params['search_query'])) {
        $is_customer_list_search = true;
        $params['name'] = $params['search_query'];
    }

    if (isset($params['name']) && fn_string_not_empty($params['name'])) {
        $arr = fn_explode(' ', trim($params['name']));
        $arr = array_values(array_filter($arr, 'fn_string_not_empty'));

        $like_expression = ' AND (';

        $search_string = '%' . trim($params['name']) . '%';

        if (sizeof($arr) == 2) {
            $like_expression .= db_quote('(?:users.firstname LIKE ?l', '%' . $arr[0] . '%');
            $like_expression .= db_quote(' AND ?:users.lastname LIKE ?l)', '%' . $arr[1] . '%');
            $like_expression .= db_quote(' OR (?:users.firstname LIKE ?l', '%' . $arr[1] . '%');
            $like_expression .= db_quote(' AND ?:users.lastname LIKE ?l)', '%' . $arr[0] . '%');
        } else {
            $like_expression .= db_quote('?:users.firstname LIKE ?l', $search_string);
            $like_expression .= db_quote(' OR ?:users.lastname LIKE ?l', $search_string);
        }

        if (!empty($is_customer_list_search)) {
            $like_expression .= db_quote(' OR ?:users.email LIKE ?l', $search_string);
            $like_expression .= db_quote(' OR ?:users.phone LIKE ?l', $search_string);
            $like_expression .= db_quote(' OR ?:user_profiles.b_phone LIKE ?l', $search_string);
            $like_expression .= db_quote(' OR ?:user_profiles.s_phone LIKE ?l', $search_string);
        }

        $like_expression .= ')';
        $condition['name'] = $like_expression;

        $compact_fields[] = 'name';
    }

    if (isset($params['user_login']) && fn_string_not_empty($params['user_login'])) {
        $condition['user_login'] = db_quote(' ?p ?:users.user_login LIKE ?l', $union_condition, '%' . trim($params['user_login']) . '%');
        $compact_fields[] = 'user_login';
    }

    if (!empty($params['tax_exempt'])) {
        $condition['tax_exempt'] = db_quote(' AND ?:users.tax_exempt = ?s', $params['tax_exempt']);
    }

    if (isset($params['usergroup_id']) && $params['usergroup_id'] != ALL_USERGROUPS) {
        if (!empty($params['usergroup_id'])) {
            $join .= db_quote(' LEFT JOIN ?:usergroup_links ON ?:usergroup_links.user_id = ?:users.user_id AND ?:usergroup_links.usergroup_id = ?i', $params['usergroup_id']);
            $condition['usergroup_links'] = db_quote(' AND ?:usergroup_links.status = ?s', ObjectStatuses::ACTIVE);
        } else {
            $join .= db_quote(' LEFT JOIN ?:usergroup_links ON ?:usergroup_links.user_id = ?:users.user_id AND ?:usergroup_links.status = ?s', ObjectStatuses::ACTIVE);
            $condition['usergroup_links'] = ' AND ?:usergroup_links.user_id IS NULL';
        }
    }

    if (!empty($params['status'])) {
        $condition['status'] = db_quote(' AND ?:users.status = ?s', $params['status']);
    }

    if (isset($params['email']) && fn_string_not_empty($params['email'])) {
        $condition['email'] = db_quote(' ?p ?:users.email LIKE ?l', $union_condition, '%' . trim($params['email']) . '%');
        $compact_fields[] = 'email';
    }

    if (isset($params['address']) && fn_string_not_empty($params['address'])) {
        $condition['address'] = db_quote(' AND (?:user_profiles.b_address LIKE ?l OR ?:user_profiles.s_address LIKE ?l)', '%' . trim($params['address']) . '%', '%' . trim($params['address']) . '%');
    }

    if (isset($params['zipcode']) && fn_string_not_empty($params['zipcode'])) {
        $condition['zipcode'] = db_quote(' AND (?:user_profiles.b_zipcode LIKE ?l OR ?:user_profiles.s_zipcode LIKE ?l)', '%' . trim($params['zipcode']) . '%', '%' . trim($params['zipcode']) . '%');
    }

    if (!empty($params['country'])) {
        $condition['country'] = db_quote(' AND (?:user_profiles.b_country LIKE ?l OR ?:user_profiles.s_country LIKE ?l)', "%$params[country]%", "%$params[country]%");
    }

    if (isset($params['state']) && fn_string_not_empty($params['state'])) {
        $condition['state'] = db_quote(' AND (?:user_profiles.b_state LIKE ?l OR ?:user_profiles.s_state LIKE ?l)', '%' . trim($params['state']) . '%', '%' . trim($params['state']) . '%');
    }

    if (isset($params['state_code']) && fn_string_not_empty($params['state_code'])) {
        $condition['state'] = db_quote(' AND (?:user_profiles.b_state = ?s OR ?:user_profiles.s_state = ?s)', $params['state_code'], $params['state_code']);
    }

    if (isset($params['city']) && fn_string_not_empty($params['city'])) {
        $condition['city'] = db_quote(' AND (?:user_profiles.b_city LIKE ?l OR ?:user_profiles.s_city LIKE ?l)', '%' . trim($params['city']) . '%', '%' . trim($params['city']) . '%');
    }

    if (!empty($params['user_id'])) {
        $condition['user_id'] = db_quote(' AND ?:users.user_id IN (?n)', $params['user_id']);
    }

    if (!empty($params['exclude_user_ids'])) {
        $condition['exclude_user_ids'] = db_quote(' AND ?:users.user_id NOT IN (?n) ', $params['exclude_user_ids']);
    }

    if (!empty($params['is_root'])) {
        $condition['is_root'] = db_quote(' AND ?:users.is_root = ?s', $params['is_root']);
    }

    if (isset($params['phone']) && fn_string_not_empty($params['phone'])) {
        $phone = trim($params['phone']);
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (!empty($phone)) {
            $phone = implode('[\(]*[\)]*[\-]*[\ ]*', str_split($phone));
            $condition['phone'] = db_quote(
                ' AND (?:users.phone REGEXP ?l OR ?:user_profiles.b_phone REGEXP ?l OR ?:user_profiles.s_phone REGEXP ?l)',
                $phone,
                $phone,
                $phone
            );
        }
    }

    if (!empty($params['p_ids']) || !empty($params['product_view_id'])) {
        $arr = (strpos($params['p_ids'], ',') !== false || !is_array($params['p_ids'])) ? explode(',', $params['p_ids']) : $params['p_ids'];
        if (empty($params['product_view_id'])) {
            $condition['order_product_id'] = db_quote(' AND ?:order_details.product_id IN (?n)', $arr);
        } else {
            $condition['order_product_id'] = db_quote(' AND ?:order_details.product_id IN (?n)', db_get_fields(fn_get_products(['view_id' => $params['product_view_id'], 'get_query' => true])));
        }

        $join .= db_quote(' LEFT JOIN ?:orders ON ?:orders.user_id = ?:users.user_id AND ?:orders.is_parent_order != ?s LEFT JOIN ?:order_details ON ?:order_details.order_id = ?:orders.order_id', YesNo::YES);
    }

    if (
        !empty($auth['user_type'])
        && $auth['user_type'] === UserTypes::ADMIN
        && isset($params['user_type'])
        && !fn_check_permission_manage_profiles($params['user_type'])
    ) {
        $condition['manage_admins'] = db_quote(' AND (?:users.user_type != ?s OR (?:users.user_type = ?s AND ?:users.user_id = ?i))', $auth['user_type'], $auth['user_type'], $auth['user_id']);
    }

    // sometimes other vendor's admins could buy products from other vendors.
    if (!empty($params['user_type']) && (!($params['user_type'] == 'C' && Registry::get('runtime.company_id')) || fn_allowed_for('ULTIMATE'))) {
        $condition['user_type'] = db_quote(' AND ?:users.user_type = ?s', $params['user_type']);
    } else {

        // Get active user types
        $user_types = array_keys(fn_get_user_types());

        // Select only necessary groups frm all available
        if (!empty($params['user_types'])) {
            $user_types = array_intersect($user_types, $params['user_types']);
        }

        if (!empty($params['exclude_user_types'])) {
            $user_types = array_diff($user_types, $params['exclude_user_types']);
        }

        $condition['user_type'] = db_quote(' AND ?:users.user_type IN(?a)', $user_types);
    }

    $join .= db_quote(' LEFT JOIN ?:user_profiles ON ?:user_profiles.user_id = ?:users.user_id');

    if ($params['extended_search']) {
        $join .= db_quote(' LEFT JOIN ?:companies ON ?:companies.company_id = ?:users.company_id');
    }

    if (!empty($params['company_ids']) && is_array($params['company_ids'])) {
        $condition['company_id'] = fn_get_company_condition('?:users.company_id', true, $params['company_ids']);
    }

    /**
     * Prepare params for getting users query
     *
     * @param array  $params    Params list
     * @param array  $fields    Fields list
     * @param array  $sortings  Sorting variants
     * @param array  $condition Conditions set
     * @param string $join      Joins list
     * @param array  $auth      Auth data
     */
    fn_set_hook('get_users', $params, $fields, $sortings, $condition, $join, $auth);

    if (!empty($params['compact']) && $params['compact'] == YesNo::YES) {
        $compact_conditions = [];
        foreach ($compact_fields as $compact_field) {
            $compact_conditions[$compact_field] = $condition[$compact_field];
            unset($condition[$compact_field]);
        }
        $compact_sign = isset($compact_conditions['name']) ? '1' : '0';
        $condition['compact'] = ' AND (' . $compact_sign . ' ' . implode('', $compact_conditions) . ')';
    }

    $sorting = db_sort($params, $sortings, 'name', 'asc');

    // Used for Extended search
    if (!empty($params['get_conditions'])) {
        return [$fields, $join, $condition];
    }

    // Paginate search results
    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field('SELECT COUNT(DISTINCT(?:users.user_id)) FROM ?:users ?p WHERE 1 ' . implode(' ', $condition), $join);
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $users = db_get_array('SELECT ' . implode(', ', $fields) . ' FROM ?:users ?p WHERE 1' . implode('', $condition) . ' ?p ?p ?p', $join, $group, $sorting, $limit);

    LastView::instance()->processResults('users', $users, $params);

    /**
     * Actions after getting users list
     *
     * @param array $users  Users list
     * @param array $params Params list
     * @param array $auth   Auth data
     */
    fn_set_hook('get_users_post', $users, $params, $auth);

    return [$users, $params];
}