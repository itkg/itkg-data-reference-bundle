ReferenceBundle
===============

## Configuration 

Pour avoir le bon mapping des attributs "$attribute" (ReferenceBundle\Document\Reference.php),
"$name" et "$fields" (ReferenceBundle\Document\ReferenceType.php),
pensez à mettre à jour le fichier app/config/config.yml avec les paramètres suivants :

```
doctrine_mongodb:
    resolve_target_documents:
        OpenOrchestra\ModelInterface\Model\ContentAttributeInterface: OpenOrchestra\ModelBundle\Document\ContentAttribute
        OpenOrchestra\ModelInterface\Model\TranslatedValueInterface: OpenOrchestra\ModelBundle\Document\TranslatedValue
        OpenOrchestra\ModelInterface\Model\FieldTypeInterface: OpenOrchestra\ModelBundle\Document\FieldType
```
