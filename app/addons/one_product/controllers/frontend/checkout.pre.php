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

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

defined('BOOTSTRAP') or die('Access denied');

/** @var string $controller */
/** @var string $mode */
/** @var array $auth */

fn_enable_checkout_mode();

fn_define('ORDERS_TIMEOUT', 60);

// Cart is empty, create it
if (empty(Tygh::$app['session']['cart'])) {
    fn_clear_cart(Tygh::$app['session']['cart']);
}

/** @var array $cart */
$cart = &Tygh::$app['session']['cart'];

/** @var \Tygh\SmartyEngine\Core $view */
$view = Tygh::$app['view'];

if ($mode === 'checkout') {
    
    if ($is_anonymous_checkout_disabled && empty($auth['user_id'])) {
        return [CONTROLLER_STATUS_REDIRECT, 'checkout.login_form'];
    }
    list($cart_products, $product_groups) = fn_calculate_cart_content($cart, $auth, 'E', true, 'F', true);

    if (!empty($product_ids = fn_check_one_product($cart_products, $auth))) {
        fn_set_notification('W', __('warning'), __('products_already_bought_cart_cleared'));
        foreach ($cart['products'] as $cart_id => $cart_val) {
            if (in_array($cart_val['product_id'], $product_ids)) {
                fn_delete_cart_product($cart, $cart_id);
                fn_save_cart_content($cart, $auth['user_id']);
            }
        }
        $cart['recalculate'] = true;
        fn_calculate_cart_content($cart, $auth, 'A', true, 'F', true);
        return [CONTROLLER_STATUS_OK, 'checkout.cart'];
    }
}

if ($mode == 'complete') {

    if (!empty($_REQUEST['order_id'])) {
        if (empty($auth['user_id']) && empty($auth['order_ids'])) {
            return [
                CONTROLLER_STATUS_REDIRECT,
                'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')),
            ];
        }

        if (!fn_is_order_allowed($_REQUEST['order_id'], $auth)) {
            return [CONTROLLER_STATUS_DENIED];
        }

        $order_info = fn_get_order_info($_REQUEST['order_id']);

        if (!empty($order_info['is_parent_order']) && $order_info['is_parent_order'] == 'Y') {
            $child_ids = db_get_fields(
                "SELECT order_id FROM ?:orders WHERE parent_order_id = ?i", $_REQUEST['order_id']
            );
            $order_info['child_ids'] = implode(',', $child_ids);
        }
        $result = fn_update_one_product($order_info);
    }
 }