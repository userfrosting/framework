<?php

/*
 * UserFrosting Config (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/config
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/config/blob/master/LICENSE.md (MIT License)
 */

/*
 * Test configuration file for UserFrosting.
 */
return [
    'debug' => [
        'auth' => true,
    ],
    'site' => [
        'login' => [
            'enable_email' => true,
        ],
        'registration' => [
            'enabled'                    => true,
            'captcha'                    => true,
            'require_email_verification' => true,
            'user_defaults'              => [
                'locale' => 'en_US',
                'group'  => 'terran',
                // Default roles for newly registered users
                'roles' => [
                    'user' => true,
                ],
            ],
        ],
    ],
];
