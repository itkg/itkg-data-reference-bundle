<?php

namespace Itkg\ReferenceBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadGroupReferenceData
 */
class LoadGroupReferenceData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $group2 = $this->getReference('group2');
        $group2->addRole('ROLE_ACCESS_REFERENCE_TYPE');
        $group2->addRole('ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE');

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1005;
    }
}
