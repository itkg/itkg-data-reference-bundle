ReferenceBundle
===============

Installation
------------

To install the project, you need to require it in your project `composer.json` file.

In the `require` section :

```json

    "itkg/itkg-data-reference-bundle": "dev-master"

```

As the project is not published on `packagist` you need to complete the `repositories` section :

```json

    {
        "type": "vcs",
        "url": "https://github.com/itkg/itkg-data-reference-bundle.git"
    }
```

Once the bundle is installed, enable it in the `AppKernel.php` file :

```php

    new Itkg\ReferenceModelBundle\ItkgReferenceModelBundle(),
    new Itkg\ReferenceApiBundle\ItkgReferenceApiBundle(),
    new Itkg\ReferenceBundle\ItkgReferenceBundle(),
```

Then configure grunt to compile the bundles assets.

In the `grunt/app_config.js` file, add the bundle's Grunt targets:

```js

module.exports = {
  targetsDir: [
    './vendor/itkg/itkg-data-reference-bundle/GruntTasks/Targets'
  ]
}
```

Add the concatenation task in the `grunt/tasks/javascript_task.js` :

```js
module.exports = function(grunt) {
  grunt.registerTask(
    'javascript',
    'Main project task to generate javascripts',
    [
        'concat:referencebundlejs'
    ]
  );
};    
```

And in the main concatenation file `grunt/targets/concat.all_js.js` :

```js
module.exports = {
  src: [
    'web/built/referencebundle.js'
  ]
}
```

Configuration
-------------

Pour avoir le bon mapping des attributs "$attribute" (ReferenceModelBundle\Document\Reference.php),
"$name" et "$fields" (ReferenceModelBundle\Document\ReferenceType.php),
pensez à mettre à jour le fichier app/config/config.yml avec les paramètres suivants :

```
doctrine_mongodb:
    resolve_target_documents:
        OpenOrchestra\ModelInterface\Model\ContentAttributeInterface: OpenOrchestra\ModelBundle\Document\ContentAttribute
        OpenOrchestra\ModelInterface\Model\TranslatedValueInterface: OpenOrchestra\ModelBundle\Document\TranslatedValue
        OpenOrchestra\ModelInterface\Model\FieldTypeInterface: OpenOrchestra\ModelBundle\Document\FieldType
```
