DoctrinePHPCRBundle
===================

Provides basic integration of the Doctrine PHPCR ODM into Symfony projects

Installation
============

 1. Add the DoctrinePHPCRBundle and the jackalope library to your project as git submodules:

        $ git submodule add git://github.com/Seldaek/DoctrinePHPCRBundle.git src/Bundle/JackalopeBundle
        $ git submodule add git://github.com/Seldaek/phpcr-odm.git src/vendor/doctrine-phpcr
        $ cd src/vendor/doctrine-phpcr
        $ git submodule update --recursive --init

 2. Add the bundle to your application kernel:

        // app/AppKernel.php
        public function registerBundles()
        {
            return array(
                // ...
                new Bundle\DoctrinePHPCRBundle\DoctrinePHPCRBundle(),
                // ...
            );
        }

 3. Add the autoloader namespace paths:

        // src/autoload.php
        $loader->registerNamespaces(array(
            // ...
            'Jackalope'                      => $vendorDir.'/doctrine-phpcr/lib/vendor/jackalope/src',
            'PHPCR'                          => $vendorDir.'/doctrine-phpcr/lib/vendor/jackalope/lib/phpcr/src',
            'Doctrine\\ODM\\PHPCR'           => $vendorDir.'/doctrine-phpcr/lib',
            // ...
        ));

 4. Add the bundle to your application config:

        # app/config/config.yml
        phpcr.config:
            backend:
                url: http://localhost:8080/server/
                workspace: foo
                user:
                pass:
                transport:

        # app/config/config.xml
        <phpcr:config>
            <backend
                url="http://localhost:8080/server/"
                workspace="foo"
                user=""
                pass=""
                transport=""
            />
        </phpcr:config>

Usage
=====

* Use the `doctrine.phpcr_odm.document_manager` service to get the DocumentManager instance
* To get a Jackalope\Session instance, call getPhpcrSession() on the DocumentManager
* Store your documents with a path:

      $dm = $this->container->get('doctrine.phpcr_odm.document_manager');
      $dm->persist($document, '/document_path');
      $dm->flush();

* Load a document by path:

      $dm = $this->container->get('doctrine.phpcr_odm.document_manager');
      $user = $dm->getRepository('Application\YourBundle\Document\User')->find('/bob');

Contributors
============

- Jordi Boggiano <j.boggiano@seld.be>
