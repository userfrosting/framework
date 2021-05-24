<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

use Slim\App;
use UserFrosting\Tests\TestSprinkle\TestController;

return function (App $app) {
    $app->get('/foo', [TestController::class, 'index']);
};
