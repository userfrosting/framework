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
