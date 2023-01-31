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

use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\ProductZeroPriceActions;
use Tygh\Enum\YesNo;
use Tygh\Tygh;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/**
 * Gets displayable product data to show it in the cart
 *
 * @param string $hash             Unique product HASH
 * @param array  $product          Product data
 * @param bool   $skip_promotion   Skip promotion calculation
 * @param array  $cart             Array of cart content and user information necessary for purchase
 * @param array  $auth             Array with authorization data
 * @param int    $promotion_amount Amount of product in promotion (like Free products, etc)
 * @param string $lang_code        Two-letter language code
 *
 * @return array|bool Product data
 */

function fn_update_one_product($order_info) {
    foreach ($order_info['products'] as $product) {
        $data[] = [
            'user_id' => $order_info['user_id'],
            'product_id' => $product['product_id']
    ];
    }
    $res = db_query("INSERT INTO ?:one_product ?m", $data);
    return $res;
}

function fn_check_one_product($product_data, $auth) {
    $product_ids = [];
    if (is_array($product_data)) {
        foreach ($product_data as $cart_prod) {
            $product_ids[] = $cart_prod['product_id'];
        }
    } else {
        $product_ids[] = $product_data;
    }
    
    $user_id = $auth['user_id'];
    $result = db_get_fields("SELECT product_id FROM ?:one_product WHERE user_id = ?i AND product_id IN (?n)", $user_id, $product_ids);
    
    return $result;
}

function fn_add_one_product_to_cart($product_data, &$cart, &$auth, $update = false)
{
    $ids = array();
    if (!empty($product_data) && is_array($product_data)) {
        if (!defined('GET_OPTIONS')) {
            list($product_data, $cart) = fn_add_product_options_files($product_data, $cart, $auth, $update);
        }

        fn_set_hook('pre_add_to_cart', $product_data, $cart, $auth, $update);

        foreach ($product_data as $key => $data) {
            if (empty($key)) {
                continue;
            }
            if (empty($data['amount'])) {
                continue;
            }

            $product_id = (!empty($data['product_id'])) ? (int) $data['product_id'] : (int) $key;
            
            //Check if product already bought by customer
            if (!empty(fn_check_one_product($product_id, $auth))) {
                fn_set_notification('E', __('error'), __('products_already_bought'));
                continue;
            }

            // Check if the product price with options modifiers equals to zero
            $zero_price_action = db_get_field('SELECT zero_price_action FROM ?:products WHERE product_id = ?i', $product_id);
            $zero_price_action = fn_normalize_product_overridable_field_value('zero_price_action', $zero_price_action);

            if ($zero_price_action === ProductZeroPriceActions::ASK_TO_ENTER_PRICE && defined('ORDER_MANAGEMENT')) {
                $data['stored_price'] = YesNo::YES;
            }

            $data['stored_price'] = (!empty($data['stored_price']) && defined('ORDER_MANAGEMENT')) ? $data['stored_price'] : YesNo::NO;

            if (empty($data['extra'])) {
                $data['extra'] = array();
            }

            if (!fn_check_add_product_to_cart($cart, $data, $product_id)) {
                continue;
            }

            // Check if product options exist
            if (!isset($data['product_options'])) {
                $data['product_options'] = fn_get_default_product_options($product_id, false, [], true);
            }

            // Generate cart id
            $data['extra']['product_options'] = $data['product_options'];

            $_id = fn_generate_cart_id($product_id, $data['extra'], false);

            if (isset($ids[$_id]) && $key == $_id) {
                continue;
            }

            if (isset($data['extra']['exclude_from_calculate'])) {
                if ($update && !empty($cart['products'][$key]) && !empty($cart['products'][$key]['extra']['aoc'])) {
                    $cart['saved_product_options'][$cart['products'][$key]['extra']['saved_options_key']] = $data['product_options'];
                } elseif (!$update && !empty($data['extra']['saved_options_key']) && !empty($data['extra']['aoc'])) {
                    $cart['saved_product_options'][$data['extra']['saved_options_key']] = $data['product_options'];
                }

                if (isset($cart['deleted_exclude_products'][$data['extra']['exclude_from_calculate']][$_id])) {
                    continue;
                }
            }
            $amount = fn_normalize_amount(@$data['amount']);

            if (!isset($data['extra']['exclude_from_calculate'])) {
                if ($data['stored_price'] != 'Y') {
                    $allow_add = true;
                    // Check if the product price with options modifiers equals to zero
                    $price = fn_get_product_price($product_id, $amount, $auth);

                    /**
                     * Executed when a product is added to cart, once the price of the product is determined.
                     * Allows to change the price of the product in the cart.
                     *
                     * @param array     $product_data       List of products data
                     * @param array     $cart               Array of cart content and user information necessary for purchase
                     * @param array     $auth               Array of user authentication data (e.g. uid, usergroup_ids, etc.)
                     * @param bool      $update             Flag, if true that is update mode. Usable for order management
                     * @param int       $_id                Cart item identifier
                     * @param array     $data               Current product data
                     * @param int       $product_id         Product identifier
                     * @param int       $amount             Product quantity
                     * @param float     $price              Product price
                     * @param string    $zero_price_action  Flag, determines the action when the price of the product is 0
                     * @param bool      $allow_add          Flag, determines if product can be added to cart
                     */
                    fn_set_hook('add_product_to_cart_get_price', $product_data, $cart, $auth, $update, $_id, $data, $product_id, $amount, $price, $zero_price_action, $allow_add);

                    if (!floatval($price) && $zero_price_action == 'A') {
                        if (isset($cart['products'][$key]['custom_user_price'])) {
                            $price = $cart['products'][$key]['custom_user_price'];
                        } else {
                            $custom_user_price = empty($data['price']) ? 0 : $data['price'];
                        }
                    }
                    $price = fn_apply_options_modifiers($data['product_options'], $price, 'P', array(), array('product_data' => $data));
                    if (!floatval($price)) {
                        $data['price'] = isset($data['price']) ? fn_parse_price($data['price']) : 0;

                        if (AREA == 'C'
                            && ($zero_price_action == 'R'
                                ||
                                ($zero_price_action == 'A' && floatval($data['price']) < 0)
                            )
                        ) {
                            if ($zero_price_action == 'A') {
                                fn_set_notification('E', __('error'), __('incorrect_price_warning'));
                            } elseif ($zero_price_action === ProductZeroPriceActions::NOT_ALLOW_ADD_TO_CART) {
                                fn_set_notification(NotificationSeverity::WARNING, __('warning'), __('warning_zero_price_restricted_product', [
                                    '[product]' => fn_get_product_name($product_id)
                                ]));
                            }
                            $allow_add = false;
                        }

                        $price = empty($data['price']) ? 0 : $data['price'];
                    }

                    /**
                     * Recalculates price and checks if product can be added with the current price
                     *
                     * @param array $data Adding product data
                     * @param float $price Calculated product price
                     * @param boolean $allow_add Flag that determines if product can be added to cart
                     */
                    fn_set_hook('add_product_to_cart_check_price', $data, $price, $allow_add);

                    if (!$allow_add) {
                        continue;
                    }

                } else {
                    $price = empty($data['price']) ? 0 : $data['price'];
                }
            } else {
                $price = 0;
            }

            $_data = db_get_row(
                'SELECT is_edp, options_type, tracking, unlimited_download, updated_timestamp FROM ?:products WHERE product_id = ?i',
                $product_id
            );
            $_data = fn_normalize_product_overridable_fields($_data);
            if (isset($_data['is_edp'])) {
                $data['is_edp'] = $_data['is_edp'];
            } elseif (!isset($data['is_edp'])) {
                $data['is_edp'] = 0;
            }
            if (isset($_data['options_type'])) {
                $data['options_type'] = $_data['options_type'];
            }
            if (isset($_data['tracking'])) {
                $data['tracking'] = $_data['tracking'];
            }
            if (isset($_data['unlimited_download'])) {
                $data['extra']['unlimited_download'] = $_data['unlimited_download'];
            }
            if (isset($_data['updated_timestamp'])) {
                $data['extra']['updated_timestamp'] = $_data['updated_timestamp'];
            }

            if (!isset($cart['products'][$_id])) { // If product doesn't exists in the cart
                $skip_error_notification = isset($data['extra']['exclude_from_calculate']) ? $data['extra']['exclude_from_calculate'] : false;
                $amount = empty($data['original_amount'])
                    ? fn_check_amount_in_stock($product_id, $amount, $data['product_options'], $_id, $data['is_edp'], 0, $cart, $update == true ? $key : 0, $skip_error_notification)
                    : $data['original_amount'];

                if ($amount === false) {
                    continue;
                }

                if ($update && $key !== $_id) {
                    // If option changed, save items order
                    $item_index = array_search($key, array_keys($cart['products']));
                    if ($item_index !== false) {
                        $offset = $item_index + 1;
                        $to_insert = [$_id => []];
                        $cart['products'] = array_slice($cart['products'], 0, $offset, true) + $to_insert + array_slice($cart['products'], $offset, null, true);
                    }
                }

                $cart['products'][$_id]['product_id'] = $product_id;
                $cart['products'][$_id]['product_code'] = fn_get_product_code($product_id, $data['product_options']);
                $cart['products'][$_id]['product'] = fn_get_product_name($product_id);
                $cart['products'][$_id]['amount'] = $amount;
                $cart['products'][$_id]['product_options'] = $data['product_options'];
                $cart['products'][$_id]['price'] = $price;
                if (!empty($zero_price_action) && $zero_price_action == 'A') {
                    if (isset($custom_user_price)) {
                        $cart['products'][$_id]['custom_user_price'] = $custom_user_price;
                    } elseif (isset($cart['products'][$key]['custom_user_price'])) {
                        $cart['products'][$_id]['custom_user_price'] = $cart['products'][$key]['custom_user_price'];
                    }
                }
                $cart['products'][$_id]['stored_price'] = $data['stored_price'];

                // add image for minicart
                $cart['products'][$_id]['main_pair'] = fn_get_cart_product_icon($product_id, $data);

                fn_define_original_amount($product_id, $_id, $cart['products'][$_id], $data);

                if ($update == true && $key != $_id) {
                    fn_delete_cart_product($cart, $key, false);
                }

            } else { // If product is already exist in the cart
                fn_set_notification('E', __('error'), __('products_already_in_cart'));
                continue;
            }

            $cart['products'][$_id]['extra'] = (empty($data['extra'])) ? array() : $data['extra'];
            $cart['products'][$_id]['stored_discount'] = @$data['stored_discount'];
            if (defined('ORDER_MANAGEMENT')) {
                $cart['products'][$_id]['discount'] = @$data['discount'];
            }

            // Increase product popularity
            if (empty(Tygh::$app['session']['products_popularity']['added'][$product_id])) {
                $popularity = [
                    'added' => 1,
                    'total' => POPULARITY_ADD_TO_CART,
                ];

                fn_update_product_popularity($product_id, $popularity);

                Tygh::$app['session']['products_popularity']['added'][$product_id] = true;
            }

            $company_id = db_get_field("SELECT company_id FROM ?:products WHERE product_id = ?i", $product_id);
            $cart['products'][$_id]['company_id'] = $company_id;

            if (!empty($data['saved_object_id'])) {
                $cart['products'][$_id]['object_id'] = $data['saved_object_id'];
            }

            if (isset($cart['applied_promotions'])) {
                foreach ($cart['applied_promotions'] as $promotion) {
                    // phpcs:ignore
                    if (isset($cart['saved_product_options'][$promotion['promotion_id'] . '_' . $product_id])) {
                        $cart['saved_product_options'][$promotion['promotion_id'] . '_' . $product_id] = $cart['products'][$_id]['product_options'];
                    }
                }
            }

            fn_set_hook('add_to_cart', $cart, $product_id, $_id);

            $ids[$_id] = $product_id;
        }

        /**
         * Change product data after adding product to cart
         *
         * @param array $product_data Product data
         * @param array $cart Cart data
         * @param array $auth Auth data
         * @param bool $update Flag the determains if cart data are updated
         */
        fn_set_hook('post_add_to_cart', $product_data, $cart, $auth, $update, $ids);

        $cart['recalculate'] = true;
        $cart['change_cart_products'] = true;

        if (!empty($cart['chosen_shipping']) && empty($cart['deny_unsetting_product_group'])) {
            $cart['calculate_shipping'] = true;
            unset($cart['product_groups']);
        }

        return $ids;

    } else {
        return false;
    }
}