<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\TestSprinkle;

use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;

class TestController
{
    public function index(Response $response, MessageGenerator $messageGenerator, Container $container)
    {
        // Prep data container
        $msg = [];
        
        /** @var MessageGenerator */
        $service = $container->get('testMessageGenerator');

        // Message from service
        $msg[] = $service->getMessage();

        // Message from DI Injection
        $msg[] = $messageGenerator->getMessage();

        // Add both messages to body
        $payload = json_encode($msg);
        $response->getBody()->write($payload);

        return $response;
    }
}
