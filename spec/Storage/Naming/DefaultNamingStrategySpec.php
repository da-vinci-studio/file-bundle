<?php

namespace spec\Dvs\FileBundle\Storage\Naming;

use PhpSpec\ObjectBehavior;
use Dvs\UUIDGenerator\Exception\UUIDGeneratorException;
use Dvs\FileBundle\Storage\Exception\NameGenerationException;

/**
 * @mixin \Dvs\FileBundle\Storage\Naming\DefaultNamingStrategy
 */
class DefaultNamingStrategySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Dvs\FileBundle\Storage\Naming\DefaultNamingStrategy');
    }

    /**
     * @param \Dvs\UUIDGenerator\Interfaces\UUIDGenerator $uuidGenerator
     */
    public function let($uuidGenerator)
    {
        $this->beConstructedWith($uuidGenerator);
    }

    /**
     * @param \Dvs\UUIDGenerator\Interfaces\UUIDGenerator $uuidGenerator
     * @param \Dvs\FileBundle\Storage\Resource\Storageable           $storageable
     */
    public function it_should_throw_an_exception_if_name_can_not_be_generated(
        $uuidGenerator,
        $storageable
    ) {
        //given
        $uuidGenerator->generateUnique()->willThrow(new UUIDGeneratorException('Message'));

        //then
        $this->shouldThrow(NameGenerationException::notGenerated())
            ->during('generate', [$storageable]);
    }

    /**
     * @param \Dvs\UUIDGenerator\Interfaces\UUIDGenerator $uuidGenerator
     * @param \Dvs\FileBundle\Storage\Resource\Storageable           $storageable
     */
    public function it_should_return_generated_name_if_it_possible(
        $uuidGenerator,
        $storageable
    ) {
        //given
        $uuidGenerator->generateUnique()->willReturn($generatedName = 'name');

        //then
        $this->generate($storageable)->shouldReturn($generatedName);
    }
}
