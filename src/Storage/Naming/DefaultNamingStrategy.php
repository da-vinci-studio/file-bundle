<?php

namespace Dvs\FileBundle\Storage\Naming;

use Dvs\FileBundle\Storage\Resource\Storageable;
use Dvs\UUIDGenerator\Interfaces\UUIDGenerator;
use Dvs\UUIDGenerator\Exception\UUIDGeneratorException;
use Dvs\FileBundle\Storage\Exception\NameGenerationException;

class DefaultNamingStrategy implements NamingStrategy
{
    /**
     * @var UUIDGenerator
     */
    private $uuidGenerator;

    /**
     * DefaultNamingStrategy constructor.
     * @param UUIDGenerator $uuidGenerator
     */
    public function __construct(UUIDGenerator $uuidGenerator)
    {
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * @param Storageable $storageable
     * @return string
     */
    public function generate(Storageable $storageable): string
    {
        try {
            return $this->uuidGenerator->generateUnique();
        } catch (UUIDGeneratorException $e) {
            throw NameGenerationException::notGenerated();
        }
    }
}