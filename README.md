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

    new Itkg\ReferenceApiBundle\ItkgReferenceApiBundle(),
    new Itkg\ReferenceBundle\ItkgReferenceBundle(),
```

Then configure grunt to compile the bundles assets.

In the `Gruntfile.js` file :

```js

    grunt.loadTasks('./vendor/itkg/itkg-data-reference-bundle/GruntTasks');

    // config declaration
    config = merge.recursive(true, config, loadDirConfig('./vendor/itkg/itkg-data-reference-bundle/GruntTasks/Options/'));
```

Check the paths, they are linked to the autoloading (psr-0 or psr-4).

Add the concatenation task in the `javascript_tasks.js` :

```js

    'concat:referencebundlejs',
```

And in the main concatenation file `concat.js.js` :

```js

    'web/built/referencebundle.js'
```

Configuration
-------------

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
