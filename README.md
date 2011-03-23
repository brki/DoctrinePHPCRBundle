DoctrinePHPCRBundle
===================

Provides basic integration of the Doctrine PHPCR ODM into Symfony projects

Installation
============

 1. Add the DoctrinePHPCRBundle and the doctrine-phpcr-odm library to your project as git submodules:

        $ git submodule add git://github.com/symfony-cmf/DoctrinePHPCRBundle.git vendor/bundles/Symfony/Cmf/DoctrinePHPCRBundle
        $ git submodule add git://github.com/doctrine/phpcr-odm.git vendor/doctrine-phpcr-odm
        $ cd vendor/doctrine-phpcr-odm
        $ git submodule update --recursive --init

 2. Add the bundle to your application kernel:

        // app/AppKernel.php
        public function registerBundles()
        {
            return array(
                // ...
                new Symfony\Bundle\DoctrinePHPCRBundle\DoctrinePHPCRBundle(),
                // ...
            );
        }

 3. Add the autoloader namespace paths:

        // src/autoload.php
        $loader->registerNamespaces(array(
            // ...
            'Jackalope'                             => $vendorDir.'/doctrine-phpcr-odm/lib/vendor/jackalope/src',
            'PHPCR'                                 => $vendorDir.'/doctrine-phpcr-odm/lib/vendor/jackalope/lib/phpcr/src',
            'Doctrine\\ODM\\PHPCR'                  => $vendorDir.'/doctrine-phpcr-odm/lib',
            'Symfony\\Bundle\\DoctrinePHPCRBundle'  => $vendorDir.'/bundles/',
            // ...
        ));

 4. Add the bundle to your application config:

        # app/config/config.yml
        doctrine_phpcr:
            backend:
                url: http://localhost:8080/server/
                workspace: foo
                user:
                pass:
                transport:

        # app/config/config.xml
        <doctrine_phpcr:config>
            <backend
                url="http://localhost:8080/server/"
                workspace="foo"
                user=""
                pass=""
                transport=""
            />
        </doctrine_phpcr:config>

Usage
=====

* Use the `doctrine.phpcr_odm.document_manager` service to get the DocumentManager instance
* To get a PHPCR\SessionInterface instance, call getPhpcrSession() on the DocumentManager
* Store your documents with a path:

      $dm = $this->container->get('doctrine.phpcr_odm.document_manager');
      $dm->persist($document, '/document_path');  //TODO: this is subject to change. we probably will use the @Path property to know where to store a document
      $dm->flush();

* Load a document by path:

      $dm = $this->container->get('doctrine.phpcr_odm.document_manager');
      $user = $dm->getRepository('Application\YourBundle\Document\User')->find('/bob');

For more on how to define documents and other non-symfony-specific tasks, see the doc in the phpcr-odm project: https://github.com/doctrine/phpcr-odm/

Contributors
============

- Jordi Boggiano <j.boggiano@seld.be>
- Lukas Kahwe Smith <smith@pooteeweet.org>
- David Buchmann <mail@davidbu.ch>
