<?php

namespace spec\Dvs\FileBundle\Storage\PathGenerating;

use PhpSpec\ObjectBehavior;
use Dvs\FileBundle\Storage\Exception\PathGenerationException;

/**
 * @mixin \Dvs\FileBundle\Storage\PathGenerating\DefaultFilePathStrategy
 */
class DefaultFilePathStrategySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Dvs\FileBundle\Storage\PathGenerating\DefaultFilePathStrategy');
    }

    /**
     * @param \Dvs\PathGenerator\Interfaces\PathGenerator $pathGenerator
     */
    public function let($pathGenerator)
    {
        $uploadDir = 'upload/';
        $this->beConstructedWith($pathGenerator, $uploadDir);
    }

    public function it_should_throw_an_exception_if_filename_is_an_empty_string()
    {
        //given
        $fileName = '';

        //then
        $this->shouldThrow(PathGenerationException::emptyFileName())
            ->during('generate', [$fileName]);
    }

    /**
     * @param \Dvs\PathGenerator\Interfaces\PathGenerator $pathGenerator
     */
    public function it_should_return_generated_path($pathGenerator)
    {
        //given
        $uploadDir = 'upload/';
        $fileName = 'test';
        $pathGenerator->generatePathFromName($fileName)->willReturn($filePath = 't/e/s/t');

        //when
        $this->generate($fileName)
        //then
            ->shouldReturn($uploadDir.$filePath);
    }
}
