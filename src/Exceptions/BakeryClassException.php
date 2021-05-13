<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Exceptions;

use LogicException;

/**
 * BakeryClassException.
 *
 * This exception should be thrown when a sprinkle class doesn't extend the right base class.
 */
class BakeryClassException extends LogicException
{
}
