<?php

declare(strict_types=1);

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Alert;

use RuntimeException;
use UserFrosting\Fortress\ServerSideValidator;
use UserFrosting\I18n\Translator;

/**
 * Implements an alert stream for use between HTTP requests, with i18n support via the Translator class.
 */
abstract class AlertStream
{
    /**
     * Create a new message stream.
     *
     * @param string          $messagesKey
     * @param Translator|null $translator
     */
    public function __construct(
        protected string $messagesKey,
        protected ?Translator $translator = null
    ) {
    }

    /**
     * Set the translator to be used for all message streams.  Must be done
     * before `addMessageTranslated` can be used.
     *
     * @param Translator|null $translator
     *
     * @return static
     */
    public function setTranslator(?Translator $translator = null): static
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Adds a raw text message to the cache message stream.
     *
     * @param string $type    The type of message, indicating how it will be styled when outputted. Should be set to "success", "danger", "warning", or "info".
     * @param string $message The message to be added to the message stream.
     *
     * @return static
     */
    public function addMessage(string $type, string $message): static
    {
        $messages = $this->messages();
        $messages[] = [
            'type'    => $type,
            'message' => $message,
        ];
        $this->saveMessages($messages);

        return $this;
    }

    /**
     * Adds a text message to the cache message stream, translated into the currently selected language.
     *
     * @param string      $type         The type of message, indicating how it will be styled when outputted.  Should be set to "success", "danger", "warning", or "info".
     * @param string      $messageId    The message id for the message to be added to the message stream.
     * @param mixed[]|int $placeholders An optional hash of placeholder names => placeholder values to substitute into the translated message.
     *
     * @throws RuntimeException
     *
     * @return static
     */
    public function addMessageTranslated(string $type, string $messageId, array|int $placeholders = []): static
    {
        if ($this->translator === null) {
            throw new RuntimeException('No translator has been set!  Please call MessageStream::setTranslator first.');
        }

        $message = $this->translator->translate($messageId, $placeholders);

        return $this->addMessage($type, $message);
    }

    /**
     * Get the messages and then clear the message stream.
     *
     * This function does the same thing as `messages()`, except that it also clears all messages afterwards.
     * This is useful, because typically we don't want to view the same messages more than once.
     * Returns an array of messages, each of which is itself an array containing "type" and "message" fields.
     *
     * @return array<int, array{type: string, message: string}>
     */
    public function getAndClearMessages(): array
    {
        $messages = $this->messages();
        $this->resetMessageStream();

        return $messages;
    }

    /**
     * Add error messages from a ServerSideValidator object to the message stream.
     *
     * @param ServerSideValidator $validator
     */
    public function addValidationErrors(ServerSideValidator $validator): void
    {
        // @phpstan-ignore-next-line errors() will be array since no argument is used
        foreach ($validator->errors() as $idx => $field) {
            foreach ($field as $eidx => $error) {
                $this->addMessage('danger', $error);
            }
        }
    }

    /**
     * Return the translator for this message stream.
     *
     * @return Translator|null The translator for this message stream.
     */
    public function translator(): ?Translator
    {
        return $this->translator;
    }

    /**
     * Get the messages from this message stream.
     *
     * @return array<int, array{type: string, message: string}> An array of messages, each of which is itself an array containing "type" and "message" fields.
     */
    abstract public function messages(): array;

    /**
     * Clear all messages from this message stream.
     */
    abstract public function resetMessageStream(): void;

    /**
     * Save messages to the stream.
     *
     * @param array<int, array{type: string, message: string}> $messages
     */
    abstract protected function saveMessages(array $messages): void;
}
