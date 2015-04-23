<?php

namespace Itkg\ReferenceBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Role;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;

/**
 * Class LoadRoleReferenceData
 */
class LoadRoleReferenceData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $roleReference = new Role();
        $roleReference->setName('ROLE_ACCESS_REFERENCE_TYPE');
        $roleReference->addDescription($this->generateTranslatedValue('en', 'Reference types acces'));
        $roleReference->addDescription($this->generateTranslatedValue('fr', 'Accès types de références '));
        $roleReference->addDescription($this->generateTranslatedValue('de', 'Der Zugriff auf Referenztypen'));
        $roleReference->addDescription($this->generateTranslatedValue('es', 'Acceso a los tipos de referencia'));
        $manager->persist($roleReference);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 800;
    }

    /**
     * Generate a translatedValue
     *
     * @param string $language
     * @param string $value
     *
     * @return TranslatedValue
     */
    protected function generateTranslatedValue($language, $value)
    {
        $label = new TranslatedValue();
        $label->setLanguage($language);
        $label->setValue($value);

        return $label;
    }
}
