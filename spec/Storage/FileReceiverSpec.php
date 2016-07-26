<?php

namespace spec\Dvs\FileBundle\Storage;

use Dvs\FileBundle\Storage\Exception\FileStoreException;
use PhpSpec\ObjectBehavior;

/**
 * @mixin \Dvs\FileBundle\Storage\FileReceiver
 */
class FileReceiverSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Dvs\FileBundle\Storage\FileReceiver');
    }

    /**
     * @param \Dvs\FileBundle\Storage\Naming\NamingStrategy           $namingStrategy
     * @param \Dvs\FileBundle\Storage\PathGenerating\FilePathStrategy $filePathStrategy
     * @param \Dvs\FileBundle\Interfaces\FileSystem      $filesystem
     */
    public function let($namingStrategy, $filePathStrategy, $filesystem)
    {
        $this->beConstructedWith($namingStrategy, $filePathStrategy, $filesystem);
    }

    /**
     * @param \Dvs\FileBundle\Storage\Naming\NamingStrategy           $namingStrategy
     * @param \Dvs\FileBundle\Storage\PathGenerating\FilePathStrategy $filePathStrategy
     * @param \Dvs\FileBundle\Interfaces\FileSystem      $filesystem
     * @param \Dvs\FileBundle\Storage\Resource\Storageable            $storageable
     */
    public function it_should_throw_an_exception_if_resource_can_not_be_stored(
        $namingStrategy,
        $filePathStrategy,
        $filesystem,
        $storageable
    ) {
        // Given
        $fileInfo = new \SplFileInfo(__FILE__);

        $namingStrategy->generate($storageable)->willReturn($filename = 'filename');
        $filePathStrategy->generate($filename)->willReturn($path = 'path/to/file/filename');
        $fileExtension = $fileInfo->getExtension();

        $filename .= '.'.$fileExtension;

        $filePathWithName = $path.DIRECTORY_SEPARATOR.$filename;

        $filesystem->write($filePathWithName, file_get_contents($fileInfo->getPathname()))->willReturn(false);

        // When
        $this->shouldThrow(FileStoreException::notStored())
            ->duringStore($storageable, $fileInfo);

        // Then
    }

    /**
     * @param \Dvs\FileBundle\Storage\Naming\NamingStrategy           $namingStrategy
     * @param \Dvs\FileBundle\Storage\PathGenerating\FilePathStrategy $filePathStrategy
     * @param \Dvs\FileBundle\Interfaces\FileSystem      $filesystem
     * @param \Dvs\FileBundle\Storage\Resource\Storageable            $storageable
     */
    public function it_should_store_resource_if_possible(
        $namingStrategy,
        $filePathStrategy,
        $filesystem,
        $storageable
    ) {
        // Given
        $fileInfo = new \SplFileInfo(__FILE__);

        $namingStrategy->generate($storageable)->willReturn($filename = 'filename');
        $filePathStrategy->generate($filename)->willReturn($path = 'path/to/file/filename');

        $filename .= '.'.$fileInfo->getExtension();
        $filePathWithName = $path.DIRECTORY_SEPARATOR.$filename;

        $filesystem->write($filePathWithName, file_get_contents(__FILE__))->willReturn(true);

        // When
        $this->store($storageable, $fileInfo)
        // Then
            ->shouldReturn(true);

        $storageable->setFileName($filename)->shouldHaveBeenCalled();
    }
}
