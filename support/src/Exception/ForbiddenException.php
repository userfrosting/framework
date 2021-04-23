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
