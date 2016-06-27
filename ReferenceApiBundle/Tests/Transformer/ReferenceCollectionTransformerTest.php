<?php

namespace Itkg\ReferenceApiBundle\Tests\Transformer;

use Itkg\ReferenceApiBundle\Facade\ReferenceCollectionFacade;
use Itkg\ReferenceApiBundle\Transformer\ReferenceCollectionTransformer;
use Phake;

/**
 * Class ReferenceCollectionTransformerTest
 */
class ReferenceCollectionTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReferenceCollectionTransformer
     */
    private $transformer;

    private $transformerManager;

    private $urlGenerator;

    private $groupContext;

    protected function setUp()
    {
        parent::setUp();

        $this->urlGenerator = Phake::mock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->groupContext = Phake::mock('OpenOrchestra\BaseApi\Context\GroupContext');

        $this->transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->urlGenerator);
        Phake::when($this->transformerManager)->getGroupContext()->thenReturn($this->groupContext);

        $this->transformer = new ReferenceCollectionTransformer();
        $this->transformer->setContext($this->transformerManager);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('OpenOrchestra\BaseApi\Transformer\TransformerInterface', $this->transformer);
    }

    public function testGetName()
    {
        $this->assertEquals('reference_collection', $this->transformer->getName());
    }

    /**
     * @param mixed $references
     *
     * @dataProvider provideTransformForReferences
     */
    public function testTransformForReferences($references)
    {
        $facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');

        $transformer = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerInterface');
        Phake::when($transformer)->transform(Phake::ignoreRemaining())->thenReturn($facade);

        Phake::when($this->transformerManager)->get('reference')->thenReturn($transformer);

        /** @var ReferenceCollectionFacade $facade */
        $facade = $this->transformer->transform($references);
        $this->assertInstanceOf('Itkg\ReferenceApiBundle\Facade\ReferenceCollectionFacade', $facade);

        $this->assertCount(count($references), $facade->getReferences());
    }

    /**
     * @param mixed $referenceType
     * @param bool  $expectAddLink
     *
     * @dataProvider provideTransformForFacadeLinks
     */
    public function testTransformForFacadeLinks($referenceType, $expectAddLink)
    {
        /** @var ReferenceCollectionFacade $facade */
        $facade = $this->transformer->transform([], $referenceType);
        $this->assertInstanceOf('Itkg\ReferenceApiBundle\Facade\ReferenceCollectionFacade', $facade);

        if ($expectAddLink) {
            $this->assertArrayHasKey('_self_add', $facade->getLinks());
        } else {
            $this->assertArrayNotHasKey('_self_add', $facade->getLinks());
        }
    }

    /**
     * @return array
     */
    public function provideTransformForReferences()
    {
        return [
            [[]],
            [['foo', 'bar']],
            [['foo', 'bar', 'baz', new \stdClass()]],
        ];
    }

    /**
     * @return array
     */
    public function provideTransformForFacadeLinks()
    {
        return [
            [null, false],
            ['foo', true],
        ];
    }
}
