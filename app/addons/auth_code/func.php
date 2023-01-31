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
use Tygh\Navigation\LastView;
use Tygh\Enum\ObjectStatuses;
use Tygh\Languages\Languages;

if (!defined('BOOTSTRAP')) { die('Access denied'); }



function fn_validate_code($code, $data) {
    if ($code == $data) {
        return true;
    }
    return false;
}

function fn_send_code($to, $from, $subj, $body, $attachments = array(), $lang_code = CART_LANGUAGE, $reply_to = '')
{
    $reply_to = !empty($reply_to) ? $reply_to : 'default_company_newsletter_email';
    $_from = array(
        'email' => !empty($from['from_email']) ? $from['from_email'] : 'default_company_newsletter_email',
        'name' => !empty($from['from_name']) ? $from['from_name'] : (empty($from['from_email']) ? 'default_company_name' : '')
    );
    /** @var \Tygh\Mailer\Mailer $mailer */
    $mailer = Tygh::$app['mailer'];

    return $mailer->send(array(
        'to' => $to,
        'from' => $_from,
        'reply_to' => $reply_to,
        'data' => array(
            'body' => $body,
            'subject' => $subj
        ),
        'attachments' => $attachments,
        'template_code' => 'newsletters_newsletter',
        'tpl' => 'addons/auth_code/verify_profile.tpl', // this parameter is obsolete and is used for back compatibility
    ), 'C', $lang_code, fn_get_auth_code_mailer_settings());
}

function fn_get_auth_code_mailer_settings()
{
    $mailer_settings = array();
    $settings = Registry::get('addons.auth_code');

    if ($settings['mailer_send_method'] !== 'default') {
        $mailer_settings = array(
            'mailer_send_method' => $settings['mailer_send_method'],
            'mailer_smtp_host' => $settings['mailer_smtp_host'],
            'mailer_smtp_username' => $settings['mailer_smtp_username'],
            'mailer_smtp_password' => $settings['mailer_smtp_password'],
            'mailer_smtp_ecrypted_connection' => $settings['mailer_smtp_ecrypted_connection'],
            'mailer_smtp_auth' => $settings['mailer_smtp_auth'],
        );
    }

    return $mailer_settings;
}
