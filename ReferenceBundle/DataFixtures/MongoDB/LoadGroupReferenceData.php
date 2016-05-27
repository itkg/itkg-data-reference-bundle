<?php

namespace Itkg\ReferenceBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Itkg\ReferenceBundle\NavigationPanel\Strategies\ReferencePanelStrategy;
use Itkg\ReferenceBundle\NavigationPanel\Strategies\ReferenceTypePanelStrategy;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;

/**
 * Class LoadGroupReferenceData
 */
class LoadGroupReferenceData extends AbstractFixture implements OrderedFixtureInterface, OrchestraProductionFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $group2 = $this->getReference('group2');
        $group2->addRole(ReferenceTypePanelStrategy::ROLE_ACCESS_REFERENCE_TYPE);
        $group2->addRole(ReferencePanelStrategy::ROLE_ACCESS_REFERENCE);

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
