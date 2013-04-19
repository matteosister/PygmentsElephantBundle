<?php
/**
 * User: matteo
 * Date: 04/12/12
 * Time: 0.39
 * 
 * Just for fun...
 */

namespace Cypress\PygmentsElephantBundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class CssRoutingLoader implements LoaderInterface
{
    /**
     * Loads a resource
     *
     * @param mixed  $resource The resource
     * @param string $type     The resource type
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        $pattern = '/_pygments_bundle/style.css';
        $defaults = array(
            '_controller' => 'CypressPygmentsElephantBundle:Main:css',
        );

        $route = new Route($pattern, $defaults);
        $routes->add('pygments_bundle_style', $route);

        return $routes;
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed  $resource A resource
     * @param string $type     The resource type
     *
     * @return Boolean true if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return 'pygments_bundle' === $type;
    }

    /**
     * Gets the loader resolver.
     *
     * @return LoaderResolverInterface A LoaderResolverInterface instance
     */
    public function getResolver()
    {
    }

    /**
     * Sets the loader resolver.
     *
     * @param LoaderResolverInterface $resolver A LoaderResolverInterface instance
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
