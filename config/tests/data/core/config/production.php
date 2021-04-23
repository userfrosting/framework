<?php

/*
 * UserFrosting Config (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/config
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/config/blob/master/LICENSE.md (MIT License)
 */

/*
 * Environment configuration file.  Recursively merged in over the base default.php configuration file.
 */
return [
    'site' => [
        'analytics' => [
            'google' => [
                'enabled' => true,
            ],
        ],
        'debug' => [
            'ajax' => false,
            'info' => false,
        ],
    ],
];
