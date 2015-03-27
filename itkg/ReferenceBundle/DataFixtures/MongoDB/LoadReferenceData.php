<?php

namespace itkg\ReferenceBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use itkg\ReferenceBundle\Document\Reference;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;

/**
 * Class LoadReferenceData
 */
class LoadReferenceData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $reference1 = $this->getReference1();
        $manager->persist($reference1);
        $this->addReference('reference1', $reference1);

        $manager->flush();
    }

    /**
     * @return Reference
     */
    protected function getReference1()
    {
        $reference1 = new Reference();
        $reference1->setName("1ere Reference");
        //$reference1->setReferenceType();

        return $reference1;
    }
    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 10000;
    }

}
