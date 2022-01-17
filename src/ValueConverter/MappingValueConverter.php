<?php

namespace Import\ValueConverter;

use Import\Exception\UnexpectedValueException;

/**
 * @author Grégoire Paris
 */
class MappingValueConverter
{
    private array $mapping = [];

    /**
     * @param array $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function __invoke($input)
    {
        if (isset($this->mapping[$input]) || array_key_exists($input, $this->mapping)) {
            return $this->mapping[$input];
        }

        throw new UnexpectedValueException(sprintf(
            'Cannot find mapping for value "%s"',
            $input
        ));
    }
}
