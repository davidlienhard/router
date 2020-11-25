<?php
/**
 * contains \DavidLienhard\Router\RouterInterface
 *
 * @package         tourBase
 * @author          David Lienhard <david.lienhard@tourasia.ch>
 * @version         1.0.0, 26.11.2020
 * @since           1.0.0, 26.11.2020, created
 * @copyright       tourasia
 */

declare(strict_types=1);

namespace DavidLienhard\Router;

/**
 * interface for router class
 *
 * @author          David Lienhard <david.lienhard@tourasia.ch>
 * @version         1.0.0, 26.11.2020
 * @since           1.0.0, 26.11.2020, created
 * @copyright       tourasia
 */
interface RouterInterface
{
    /**
     * registers a before middleware route for all available methods
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     * @return          void
     */
    public function beforeAll(string $pattern, $fn);

    /**
     * registers a before middleware route and a handling function
     * to be executed when accessed using one of the specified methods.
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           array|string    $methods        allowed methods. single method as string, multiple as array
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     * @return          void
     */
    public function before($methods, string $pattern, $fn);

    /**
     * store a route and a handling function to be executed
     * when accessed using one of the specified methods.
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           array|string    $methods        allowed methods. single method as string, multiple as array
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     * @return          void
     */
    public function add($methods, string $pattern, $fn);

    /**
     * shorthand for a route accessed using any method
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     * @return          void
     */
    public function all(string $pattern, $fn);

    /**
     * shorthand for a route accessed using GET
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     * @return          void
     */
    public function get(string $pattern, $fn);

    /**
     * shorthand for a route accessed using POST
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     * @return          void
     */
    public function post(string $pattern, $fn);

    /**
     * Shorthand for a route accessed using PATCH
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     * @return          void
     */
    public function patch(string $pattern, $fn);

    /**
     * Shorthand for a route accessed using DELETE
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     * @return          void
     */
    public function delete(string $pattern, $fn);

    /**
     * Shorthand for a route accessed using PUT
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     * @return          void
     */
    public function put(string $pattern, $fn);

    /**
     * Shorthand for a route accessed using OPTIONS
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     * @return          void
     */
    public function options(string $pattern, $fn);

    /**
     * mounts a collection of callbacks onto a base route
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           string          $baseRoute      the route sub pattern to mount the callbacks on
     * @param           callable        $fn             the callback method
     * @return          void
     */
    public function mount(string $baseRoute, $fn);

    /**
     * get all request headers
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @return          array           the request headers
     */
    public function getRequestHeaders();

    /**
     * get the request method used, taking overrides into account
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @return          string          the Request method to handle
     */
    public function getRequestMethod();

    /**
     * set a default lookup namespace for callable methods
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           string          $namespace      a given namespace
     * @return          void
     */
    public function setNamespace($namespace);

    /**
     * get the given namespace before
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @return          string          the given namespace if exists
     */
    public function getNamespace();

    /**
     * execute the router:
     * loop all defined before middleware's and routes, and execute the handling function if a match was found
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           object|callable $callback       function to be executed after a matching route was handled (= after router middleware)
     * @return          bool
     */
    public function run($callback = null);

    /**
     * set the 404 handling function
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           object|callable $fn             the function to be executed$
     * @return          void
     */
    public function set404($fn);

    /**
     * define the current relative URI.
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @return          string
     */
    public function getCurrentUri();

    /**
     * return server base path, and define it if isn't defined.
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @return          string
     */
    public function getBasePath();

    /**
     * explicilty sets the server base path.
     * to be used when your entry script path differs from your entry URLs.
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           string          $serverBasePath base path to set
     * @return          void
     */
    public function setBasePath($serverBasePath);
}
