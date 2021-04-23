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
 * NotFoundException.
 *
 * This exception should be thrown when a resource could not be found.
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
class NotFoundException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    protected $httpErrorCode = 404;

    /**
     * {@inheritdoc}
     */
    protected $defaultMessage = 'ERROR.404.TITLE';
}
