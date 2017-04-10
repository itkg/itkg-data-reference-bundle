<?php

namespace Itkg\ReferenceModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170404103811 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Migrate Reference Types & References from 1.2 to 2.0";
    }

    /**
     * {@inheritDoc}
     * @see \AntiMattr\MongoDB\Migrations\AbstractMigration::up()
     */
    public function up(Database $db)
    {
        $this->updateReferenceTypes($db);
        $this->updateReferences($db);
    }

    /**
     * Update Reference Types
     *
     * @param Database $db
     */
    protected function updateReferenceTypes(Database $db)
    {
        $this->write(' + Update Reference Types');

        $this->write(' -> Add DefaultListable');
        $db->execute('
            db.reference_type.find({}).forEach(function(item) {
                item.defaultListable = {"name": true, "created_at": true, "created_by": true, "updated_at": false, "updated_by": false};
                db.reference_type.update({_id: item._id}, item);
            });
        ');

        $this->write(' -> Update names');
        $db->execute('
            db.reference_type.find({"names":{$exists:1}}).forEach(function(item) {
                ' . $this->getTranslatedValueUpdateCode('item.names') . '
                db.reference_type.update({_id: item._id}, item);
            });
        ');

        $this->write(' -> Update fields labels');
        $db->execute('
            db.reference_type.find({"fields":{$exists:1}}).forEach(function(item) {
                for (var i in item.fields) {
                    var field = item.fields[i];
                    ' . $this->getTranslatedValueUpdateCode('field.labels') . '
                    item.fields[i] = field;
                }

                db.reference_type.update({_id: item._id}, item);
            });
        ');
    }

    /**
     * Update References
     *
     * @param Database $db
     */
    protected function updateReferences(Database $db)
    {
        $this->write(' + Update References');

        $this->write(' -> Rename referenceTypeId to referenceType');
        $db->execute('
            db.reference.find({}).forEach(function(item) {
                item.referenceType = item.referenceTypeId;
                delete item.referenceTypeId;
                db.reference.update({_id: item._id}, item);
            });
        ');
    }

    /**
     * Get the code to update a translation value from 1.1 to 1.2
     *
     * @param string $property
     * @return string
     */
    protected function getTranslatedValueUpdateCode($property)
    {
        return '
            var property = ' . $property . '
            var newProperty = {};
            for (var i in property) {
               var element = property[i];
               var language = element.language;
               var value = element.value;
               newProperty[language] = value;
            }
            ' . $property . ' = newProperty;
        ';
    }

    public function down(Database $db)
    {
    }
}
