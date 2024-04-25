<?php

namespace Import\Writer;

use Import\Writer;

/**
 * This class writes an item into an array that was passed by reference
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class ArrayWriter implements Writer
{
    use WriterTemplate;

    /**
     * @var array
     */
    protected array $data;

    /**
     * @param array $data
     */
    public function __construct(array &$data)
    {
        $this->data = &$data;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(): void
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item): void
    {
        $this->data[] = $item;
    }
}
