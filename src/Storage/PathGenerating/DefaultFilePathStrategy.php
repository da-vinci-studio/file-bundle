<?php

namespace Dvs\FileBundle\Storage\PathGenerating;

use Dvs\PathGenerator\Interfaces\PathGenerator;
use Dvs\FileBundle\Storage\Exception\PathGenerationException;

class DefaultFilePathStrategy implements FilePathStrategy
{
    /**
     * @var PathGenerator
     */
    private $pathGenerator;

    /**
     * @var string
     */
    private $documentUploadDir;

    /**
     * DefaultFilePathStrategy constructor.
     * @param PathGenerator $pathGenerator
     * @param string $documentUploadDir
     */
    public function __construct(
        PathGenerator $pathGenerator,
        string $documentUploadDir
    ) {
        $this->pathGenerator = $pathGenerator;
        $this->documentUploadDir = $documentUploadDir;
    }

    /**
     * @param string $fileName
     * @return string
     */
    public function generate(string $fileName): string
    {
        if (trim($fileName) === '') {
            throw PathGenerationException::emptyFileName();
        }

        return $this->documentUploadDir.$this->pathGenerator->generatePathFromName($fileName);
    }
}
