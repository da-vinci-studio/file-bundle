<?php

declare (strict_types = 1);

namespace Dvs\FileBundle\Storage;

use Dvs\FileBundle\Storage\Exception\FileStoreException;
use Dvs\FileBundle\Storage\Resource\Storageable;
use Dvs\FileBundle\Storage\PathGenerating\FilePathStrategy;
use Dvs\FileBundle\Storage\Naming\NamingStrategy;
use Dvs\FileBundle\Interfaces\FileSystem as FileSystemInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileReceiver
{
    /**
     * @var NamingStrategy
     */
    private $namingStrategy;

    /**
     * @var FilePathStrategy
     */
    private $filePathStrategy;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @param NamingStrategy      $namingStrategy
     * @param FilePathStrategy    $filePathStrategy
     * @param FilesystemInterface $filesystem
     */
    public function __construct(
        NamingStrategy $namingStrategy,
        FilePathStrategy $filePathStrategy,
        FilesystemInterface $filesystem
    ) {
        $this->namingStrategy = $namingStrategy;
        $this->filePathStrategy = $filePathStrategy;
        $this->filesystem = $filesystem;
    }

    /**
     * @param Storageable $storageable
     * @param \SplFileInfo $fileInfo
     *
     * @return bool
     */
    public function store(Storageable $storageable, \SplFileInfo $fileInfo): bool
    {
        $fileName = $this->namingStrategy->generate($storageable);
        $filePath = $this->filePathStrategy->generate($fileName);
        $fileExtension = $this->getFileExtension($fileInfo);

        if($fileExtension) {
            $fileName .= '.'.$fileExtension;
        }

        $storageable->setFileName($fileName);
        $filePathWithName = $filePath.DIRECTORY_SEPARATOR.$fileName;

        if (!$this->filesystem->write($filePathWithName, file_get_contents($fileInfo->getPathname()))) {
            throw FileStoreException::notStored();
        }

        return true;
    }

    /**
     * @param \SplFileInfo $fileInfo
     * @return mixed|string
     */
    private function getFileExtension(\SplFileInfo $fileInfo)
    {
        if($fileInfo instanceof UploadedFile) {
            return pathinfo($fileInfo->getClientOriginalName(), PATHINFO_EXTENSION);
        }

        return $fileInfo->getExtension();
    }
}
