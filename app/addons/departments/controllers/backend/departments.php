<?php

use Tygh\Http;
use Tygh\Registry;
use Tygh\Tools\Url;
use Tygh\Languages\Languages;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @var string $mode
 * @var array $auth
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $suffix = '';

    // Define trusted variables that shouldn't be stripped
    fn_trusted_vars(
        'department_data',
    );

    if ($mode == 'update_department') {
        $department_id = !empty($_REQUEST['department_id']) ? $_REQUEST['department_id'] : 0;
        $data = !empty($_REQUEST['department_data']) ? $_REQUEST['department_data'] : [];
        $department_id = fn_update_department($data, $department_id);
        if (!empty($department_id)) {
            $suffix = ".update_department?department_id={$department_id}";
        } else {
            $suffix = ".add_department";
        }
    } elseif ($mode == 'update_departments') {
        if (!empty($_REQUEST['departments_ids'])) {
            foreach ($_REQUEST['departments_ids'] as $department_id) {
                fn_update_department($_REQUEST['department_data'][$department_id], $department_id);
            }
        }
        $suffix = ".manage_departments";
    } elseif ($mode == 'delete_department') {
        $department_id = !empty($_REQUEST['department_id']) ? $_REQUEST['department_id'] : 0;
        fn_delete_department($department_id);
        $suffix = ".manage_departments";
    } elseif ($mode == 'delete_departments') {
        if (!empty($_REQUEST['departments_ids'])) {
            foreach ($_REQUEST['departments_ids'] as $department_num => $department_id) {
                fn_delete_department($department_id);
            }
        }
        $suffix = ".manage_departments";
    }

    return [CONTROLLER_STATUS_OK, 'departments' . $suffix];
}

if ($mode == 'add_department' || $mode == 'update_department') {
    $params['department_id'] = !empty($_REQUEST['department_id']) ? $_REQUEST['department_id'] : 0;
    $department_data = fn_get_department_data($params, DESCR_SL);


    if (empty($department_data) && $mode == 'update_department') {
        return [CONTROLLER_STATUS_NO_PAGE];
    }
    Tygh::$app['view']->assign([
        'department_data'=> $department_data,
        'director_info'=> !empty($department_data['director_id']) ? fn_get_user_short_info($department_data['director_id']) : [],
    ]);
} elseif ($mode == 'manage_departments') {
    list($departments, $search) = fn_get_departments($_REQUEST, Registry::get('settings.Appearance.products_per_page'), DESCR_SL);
    Tygh::$app['view']->assign('departments', $departments);
    Tygh::$app['view']->assign('search', $search);
} elseif ($mode == 'picker') {
    $params = $_REQUEST;
    $params['exclude_user_types'] = empty($params['department_director']) ? array ('A', 'V') : array ('C', 'V');
    $params['skip_view'] = 'Y';

    list($users, $search) = fn_get_department_users($params, $auth, Registry::get('settings.Appearance.admin_elements_per_page'));
    Tygh::$app['view']->assign('users', $users);
    Tygh::$app['view']->assign('search', $search);

    Tygh::$app['view']->assign('countries', fn_get_simple_countries(true, CART_LANGUAGE));
    Tygh::$app['view']->assign('states', fn_get_all_states());
    Tygh::$app['view']->assign('usergroups', fn_get_usergroups(array('status' => array('A', 'H')), CART_LANGUAGE));

    Tygh::$app['view']->display('pickers/users/picker_contents.tpl');
    exit;
}