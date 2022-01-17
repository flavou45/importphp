<?php

namespace Import\ValueConverter;

use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Converts a string to an object
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class StringToObjectConverter
{
    private ObjectRepository $repository;

    private string $property;

    /**
     * @param ObjectRepository $repository
     * @param string           $property
     */
    public function __construct(ObjectRepository $repository, $property)
    {
        $this->repository = $repository;
        $this->property = $property;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($input)
    {
        $method = 'findOneBy'.ucfirst($this->property);

        return $this->repository->$method($input);
    }
}
