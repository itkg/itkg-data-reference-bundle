ReferenceBundle
===============

[![Build Status](https://travis-ci.com/itkg/itkg-data-reference-bundle.svg?token=ASZJDqSq1aY7qys6tNP3&branch=master)](https://travis-ci.com/itkg/itkg-data-reference-bundle.svg?token=ASZJDqSq1aY7qys6tNP3&branch=master) 
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/itkg/itkg-data-reference-bundle/badges/quality-score.png?b=master&s=fa16b0e1800535b1dd94b878802f36e8fdb1944c)](https://scrutinizer-ci.com/g/itkg/itkg-data-reference-bundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/itkg/itkg-data-reference-bundle/badges/coverage.png?b=master&s=88c379e412b1070b666c0f964be28c593c9c6309)](https://scrutinizer-ci.com/g/itkg/itkg-data-reference-bundle/?branch=master)

Installation
------------

To install the project, you need to require it in your project `composer.json` file (adapt with version you need on your project).

```json
{
    "require": {
        "itkg/itkg-data-reference-bundle": "~1.1"
    }
}

```

As the project is not published on `packagist` you need to complete the `repositories` section :

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/itkg/itkg-data-reference-bundle.git"
        }
    ]
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
