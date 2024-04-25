<?php

namespace Import\Writer;

use Import\Writer;

/**
 * Writes using a callback or closure
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class CallbackWriter implements Writer
{
    use WriterTemplate;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item): void
    {
        call_user_func($this->callback, $item);
    }
}
