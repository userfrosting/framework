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
 * ForbiddenException.
 *
 * This exception should be thrown when a user has attempted to perform an unauthorized action.
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
class ForbiddenException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    protected $httpErrorCode = 403;

    /**
     * {@inheritdoc}
     */
    protected $defaultMessage = 'ACCESS_DENIED';
}
