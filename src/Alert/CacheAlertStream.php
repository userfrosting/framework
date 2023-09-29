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

use Illuminate\Cache\Repository as Cache;
use Illuminate\Cache\TaggedCache;
use UserFrosting\I18n\Translator;

/**
 * Implements a message stream for use between HTTP requests, with i18n
 * support via the Translator class using the cache system to store
 * the alerts. Note that the tags are added each time instead of the
 * constructor since the session_id can change when the user logs in or out.
 */
class CacheAlertStream extends AlertStream
{
    /**
     * @var Cache Object We use the cache object so that added messages will automatically appear in the cache.
     */
    protected Cache $cache;

    /**
     * @var string Session id tied to the alert stream.
     */
    protected string $session_id;

    /**
     * Create a new message stream.
     *
     * @param string          $messagesKey Store the messages under this key
     * @param Translator|null $translator
     * @param Cache           $cache
     * @param string          $sessionId
     */
    public function __construct(string $messagesKey, ?Translator $translator, Cache $cache, string $sessionId)
    {
        $this->cache = $cache;
        $this->session_id = $sessionId;
        parent::__construct($messagesKey, $translator);
    }

    /**
     * {@inheritDoc}
     */
    public function messages(): array
    {
        if ($this->getCache()->has($this->messagesKey)) {
            $data = $this->getCache()->get($this->messagesKey);

            return (is_array($data)) ? $data : [];
        } else {
            return [];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function resetMessageStream(): void
    {
        $this->getCache()->forget($this->messagesKey);
    }

    /**
     * {@inheritDoc}
     */
    protected function saveMessages(array $messages): void
    {
        $this->getCache()->forever($this->messagesKey, $messages);
    }

    /**
     * @return TaggedCache
     */
    protected function getCache(): TaggedCache
    {
        return $this->cache->tags('_s' . $this->session_id);
    }
}
