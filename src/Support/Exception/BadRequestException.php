<?php

/*
 * UserFrosting Support (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/support
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/support/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Support\Exception;

/**
 * BadRequestException.
 *
 * This exception should be thrown when a user has submitted an ill-formed request, or other incorrect data.
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
class BadRequestException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    protected $httpErrorCode = 400;

    /**
     * {@inheritdoc}
     */
    protected $defaultMessage = 'NO_DATA';
}
