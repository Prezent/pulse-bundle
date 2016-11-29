prezent/pulse-bundle
====================

Basic analytics event tracking in Symfony

The full documentation can be found in [Resources/doc](src/Resources/doc/index.md)

Index
-----

1. Installation (see below)
2. [Getting started](getting-started.md)


Installation
------------

This bundle can be installed using Composer. Tell composer to install the bundle:

```bash
$ php composer.phar require prezent/pulse-bundle
```

Then, activate the bundle in your kernel:

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Prezent\PulseBundle\PrezentPulseBundle(),
    );
}
```


Configuration
-------------

Configuration is entirely optional. The only option is defining which dbal connection
to use for storing events and aggregate statistics. If you do not configure anything, the default
connection will be used.

```yml
# app/config/config.yml

prezent_pulse:
    connection: default
```


Update your database schema
---------------------------

Finally, update your database schema:

```sh
$ bin/console doctrine:schema:update --force
```

Or if you are using Doctrine migrations:

```sh
$ bin/console doctrine:migrations:diff
$ bin/console doctrine:migrations:migrate
```
