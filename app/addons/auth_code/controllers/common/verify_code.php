<?php

use Tygh\Development;
use Tygh\Registry;
use Tygh\Helpdesk;
use Tygh\Tools\Url;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //
    // verify code
    //
    if ($mode == 'get_validation') {
        
        $data =fn_get_session_data();
        $code = isset($data[AREA.'_code']) ? $data[AREA.'_code'] : false;
        if (empty($code)) {
            fn_set_notification('W', __('warning'), __('verification_code_expired'));
            $_REQUEST['redirect_url'] = 'index.php?dispatch=verify_code.get_validation&expired=true';
            return [CONTROLLER_STATUS_REDIRECT, $_REQUEST['redirect_url']];
        }
        $count = isset($data[AREA.'count']) ? $data[AREA.'count'] +1 : 1;
        fn_set_session_data(AREA . 'count', $count, COOKIE_ALIVE_TIME);

        $user_code = isset($_REQUEST['code']) && !empty($_REQUEST['code']) ? $_REQUEST['code'] : 0;
        $result = fn_validate_code($code, $user_code);
        if (!empty($result)) {
            // Regenerate session ID for security reasons
            Tygh::$app['session']->regenerateID();
            
            $user_data = fn_get_session_data(AREA . '_user_id');
            fn_login_user($user_data, true);

            Helpdesk::auth();

            if (!empty($_REQUEST['return_url'])) {
                $redirect_url = $_REQUEST['return_url'];
            }
            unset($_REQUEST['redirect_url']);
            unset(Tygh::$app['session']['cart']['edit_step']);
            if (AREA == 'C') {
                if (empty($_REQUEST['quick_login'])) {
                    fn_set_notification('N', __('notice'), __('successful_login'));
                    return array(CONTROLLER_STATUS_OK, 'index.php');
                } else {
                    Tygh::$app['ajax']->assign('force_redirection', fn_url());
                    exit;
                }
            }

        } else {
            if ($count >= 3) {
                fn_set_notification('E', __('error'), __('verificate_email'));
                $_REQUEST['redirect_url'] = 'index.php?dispatch=auth.login_form';
                return [CONTROLLER_STATUS_OK, $_REQUEST['redirect_url']];
            }
            fn_set_notification('E', __('error'), __('wrong_verification_code'));
            $_REQUEST['redirect_url'] = 'index.php?dispatch=verify_code.get_validation&wrong_code=true';
            return [CONTROLLER_STATUS_REDIRECT, $_REQUEST['redirect_url']];
        }
    }
    //
    // Login, redirect on validation page
    //
    if ($mode== 'validation') {
        
        Tygh::$app['view']->assign([
            'email'        => isset($_REQUEST['email']) ? trim($_REQUEST['email']) : null,
        $redirect_url = '']);
        
        fn_restore_processed_user_password($_REQUEST, $_POST);
        list($status, $user_data, $user_login, $password, $salt) = fn_auth_routines($_REQUEST, $auth);
        
        if (!empty($_REQUEST['redirect_url'])) {
            $redirect_url = $_REQUEST['redirect_url'];
        } else {
            $redirect_url = fn_url('verify_code.get_validation' . !empty($_REQUEST['return_url']) ? '?return_url=' . $_REQUEST['return_url'] : '');
        }

        if ($status === false) {
            fn_save_post_data('user_login');

            return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
        }

        if (
            !empty($user_data)
            && !empty($password)
            && fn_user_password_verify((int) $user_data['user_id'], $password, (string) $user_data['password'], (string) $salt)
        ) {
            $_REQUEST['return_url'] = 'index.php?dispatch=verify_code.get_validation';
            $_REQUEST['redirect_url'] = 'index.php?dispatch=verify_code.get_validation';
            
            $email = isset($_REQUEST['user_login']) ? trim($_REQUEST['user_login']) : null;

            fn_set_session_data(AREA . 'user_login', $email, COOKIE_ALIVE_TIME);
            fn_set_session_data(AREA . '_user_id', $user_data['user_id'], COOKIE_ALIVE_TIME);
            fn_set_session_data(AREA . 'new_count', -1, COOKIE_ALIVE_TIME);
            fn_set_session_data(AREA . 'count', 0, COOKIE_ALIVE_TIME);

            if (!empty($_REQUEST['return_url'])) {
                $redirect_url = $_REQUEST['return_url'];
            }

            unset($_REQUEST['redirect_url']);

            if (AREA == 'C') {
                if (empty($_REQUEST['quick_login'])) {
                    return array(CONTROLLER_STATUS_OK, 'verify_code.get_validation');
                } else {
                    Tygh::$app['ajax']->assign('force_redirection', fn_url('verify_code.get_validation'));
                    exit;
                }
            }

            return array(CONTROLLER_STATUS_OK, 'verify_code.get_validation');

        } else {
            // Log user failed login
            fn_log_event('users', 'failed_login', [
                'user' => $user_login
            ]);

            $auth = fn_fill_auth();

            if (empty($_REQUEST['quick_login'])) {
                fn_set_notification('E', __('error'), __('error_incorrect_login'));
            }
            fn_save_post_data('user_login');

            if (AREA === 'C' && defined('AJAX_REQUEST') && isset($_REQUEST['login_block_id'])) {
                /** @var \Tygh\SmartyEngine\Core $view */
                $view = Tygh::$app['view'];
                /** @var \Tygh\Ajax $ajax */
                $ajax = Tygh::$app['ajax'];

                $view->assign([
                    'stored_user_login' => $user_login,
                    'style'             => 'popup',
                    'login_error'       => true,
                    'id'                => $_REQUEST['login_block_id']
                ]);

                if ($view->templateExists('views/auth/login_form.tpl')) {
                    $view->display('views/auth/login_form.tpl');
                    $view->clearAssign(['login_error', 'id', 'style', 'stored_user_login']);

                    return [CONTROLLER_STATUS_NO_CONTENT];
                }
            }

            return [CONTROLLER_STATUS_REDIRECT, $redirect_url];
        }

        unset(Tygh::$app['session']['edit_step']);
    }

    return [CONTROLLER_STATUS_OK, !empty($redirect_url) ? $redirect_url : fn_url()];
}


if ($mode== 'get_validation') {
    //
    // validation page, verify or send new code
    //
    $old_pass = isset($_REQUEST['wrong_code']) ? true : false;
    $expired_code = isset($_REQUEST['expired']) ? true : false;
    $data =fn_get_session_data();
    if (empty($old_pass || !empty($expired_code))) {
        $new_count = $data[AREA.'new_count'] + 1;
    }
    if (isset($new_count) && $new_count > 2 ) {
        fn_set_notification('E', __('error'), __('verificate_email'));
        $_REQUEST['redirect_url'] = 'index.php?dispatch=auth.login_form';
        return [CONTROLLER_STATUS_OK, $_REQUEST['redirect_url']];
    }
    $email = $data[AREA.'user_login'];
    if (fn_validate_email($email) && empty($old_pass) && empty($expired_code)) {
        $code = rand(1000, 10000);
        $html_body = '<strong>Your code for authorization: </strong>' . $code;

        if (!empty($html_body)) {
            $result = fn_send_code($email, array(), 'Verification code', $html_body, array(), DESCR_SL, '', true);
        }

        if ((!empty($html_body) && $result)) {
            fn_set_notification('N', __('notice'), __('verification_code_sent'));
        }
    }

    !empty($expired_code) ? fn_set_session_data(AREA . 'count', 0, COOKIE_ALIVE_TIME) : false;
    isset($new_count) ? fn_set_session_data(AREA . 'count', 0, COOKIE_ALIVE_TIME) : false;
    isset($new_count) ? fn_set_session_data(AREA . 'new_count', $new_count, COOKIE_ALIVE_TIME) : false;
    isset($code) ? fn_set_session_data(AREA . '_code', $code, 300) : false;

}

