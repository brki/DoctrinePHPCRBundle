<?php

namespace Bundle\DoctrinePHPCRBundle;

class JackalopeLoader
{
    protected $container;
    protected $session;

    protected $config;

    public function __construct($container, $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    public function getSession()
    {
        if (!$this->session) {
            $this->session = $this->container->get('jackalope.session');
        }
        return $this->session;
    }

    /**
     * Setup the base structure for this importer if necessary.
     *
     * @return \PHPCR\NodeInterface
     * @throws todo.. findout which exceptions might be thrown.
     */
    public function initPath($path)
    {
        $node = $this->getSession()->getRootNode();
        $nodes = explode('/', trim($path, '/'));
        foreach ($nodes as $subpath) {
            if ($node->hasNode($subpath)) {
                $node = $node->getNode($subpath);
            } else {
                $node = $node->addNode($subpath);
            }
        }
        return $node;
    }

}
