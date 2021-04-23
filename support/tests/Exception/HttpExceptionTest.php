<?php

/*
 * UserFrosting Support (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/support
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/support/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Support\Tests\Exception;

use PHPUnit\Framework\TestCase;
use UserFrosting\Support\Exception\HttpException;
use UserFrosting\Support\Message\UserMessage;

class HttpExceptionTest extends TestCase
{
    public function testHttpException()
    {
        $exception = new HttpException('foo', 123);

        $this->assertEquals(123, $exception->getCode());
        $this->assertEquals('foo', $exception->getMessage());
        $this->assertEquals([new UserMessage('SERVER_ERROR')], $exception->getUserMessages());
        $this->assertSame(500, $exception->getHttpErrorCode());
    }

    public function testHttpExceptionWithAddUserMessage()
    {
        $exception = new HttpException();

        $exception->addUserMessage('foo');
        $this->assertEquals([new UserMessage('foo')], $exception->getUserMessages());
    }

    public function testHttpExceptionWithAddUserMessageInstance()
    {
        $exception = new HttpException();
        $message = new UserMessage('foo');

        $exception->addUserMessage($message);
        $this->assertEquals([$message], $exception->getUserMessages());
    }
}
