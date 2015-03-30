<?php

namespace itkg\ReferenceBundle\DataFixtures\MongoDB;

use OpenOrchestra\ModelBundle\Document\ContentAttribute;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use itkg\ReferenceBundle\Document\Reference;
use itkg\ReferenceType\Document\ReferenceType;
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

        $manager->flush();
    }

    /**
     * @return Reference
     */
    protected function getReference1()
    {
        $reference1 = new Reference();
        $reference1->setName("1ere Reference");

        $attribute1 = new ContentAttribute();
        $attribute1->setName("Attr 1 Ref 1");
        $attribute2 = new ContentAttribute();
        $attribute2->setName("Attr 2 Ref 1");
        $attribute2->setValue("value 1 Ref 1");
        $reference1->addAttribute($attribute1);
        $reference1->addAttribute($attribute2);

        return $reference1;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }

}
