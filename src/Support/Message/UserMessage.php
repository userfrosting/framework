<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Support\Message;

/**
 * UserMessage.
 *
 * A user-viewable message, consisting of a message string or message token, and zero or more parameters for the message.
 * Parameters can be used, for example, to fill in placeholders in dynamically generated messages.
 */
// TODO : Rename to translatable message
// TODO : Add __toString(), which will return a translated message (depend on Translator... or have Translator accept a TranslatableMessage)
class UserMessage
{
    /**
     * @var string The user-viewable error message.
     */
    public string $message;

    /**
     * @var string[] The parameters to be filled in for any placeholders in the message.
     */
    public array $parameters = [];

    /**
     * Public constructor.
     *
     * @param string   $message
     * @param string[] $parameters The parameters to be filled in for any placeholders in the message.
     */
    public function __construct(string $message, array $parameters = [])
    {
        $this->message = $message;
        $this->parameters = $parameters;
    }
}
