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
        'auth' => false,
    ],
    'site' => [
        'login' => [
            'enable_email' => false,
        ],
        'registration' => [
            'enabled' => false,
            'captcha' => false,
        ],
    ],
];
