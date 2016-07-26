<?php

namespace spec\Dvs\FileBundle\Factory;

use Dvs\FileBundle\File;
use PhpSpec\ObjectBehavior;

class FileFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Dvs\FileBundle\Factory\FileFactory');
    }

    /**
     * @param \Dvs\FileBundle\Adapter\FlySystem\Local   $filesystem
     * @param \Dvs\UUIDGenerator\Interfaces\UUIDGenerator $UUIDGenerator
     * @param \Dvs\PathGenerator\Interfaces\PathGenerator $pathGenerator
     */
    public function let($filesystem, $UUIDGenerator, $pathGenerator)
    {
        $this->beConstructedWith($filesystem, $UUIDGenerator, $pathGenerator, 'dir');
    }

    /**
     * @param \Dvs\FileBundle\Adapter\FlySystem\Local   $filesystem
     * @param \Dvs\PathGenerator\Interfaces\PathGenerator $pathGenerator
     */
    public function it_should_create_file_from_resource($filesystem, $pathGenerator)
    {
        $subDirectories = $this->getSubDirectories();
        $directory = 'directory';

        $file = File::create(
            $filename = 'filename',
            $directory.DIRECTORY_SEPARATOR.$subDirectories,
            $resource = 'resource'
        );

        $pathGenerator->generatePathFromName($filename)->willReturn($subDirectories);

        $filesystem->getPathPrefix()->willReturn('/root');
        $filesystem->writeFile($file)->shouldBeCalled();

        $this->create($filename, $resource, $directory)->shouldBeLike($file);
    }

    /**
     * @return string
     */
    private function getSubDirectories()
    {
        return str_replace('%s', DIRECTORY_SEPARATOR, 'f%si%sl%se%sn');
    }
}
