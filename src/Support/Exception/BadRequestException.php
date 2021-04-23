<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
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
