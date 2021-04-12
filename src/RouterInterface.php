<?php
/**
 * contains \DavidLienhard\Router\RouterInterface
 *
 * @package         tourBase
 * @author          David Lienhard <david.lienhard@tourasia.ch>
 * @copyright       tourasia
 */

declare(strict_types=1);

namespace DavidLienhard\Router;

/**
 * interface for router class
 *
 * @author          David Lienhard <david.lienhard@tourasia.ch>
 * @copyright       tourasia
 */
interface RouterInterface
{
    /**
     * registers a before middleware route for all available methods
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     */
    public function beforeAll(string $pattern, object|callable $fn): void;

    /**
     * registers a before middleware route and a handling function
     * to be executed when accessed using one of the specified methods.
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           array|string    $methods        allowed methods. single method as string, multiple as array
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     */
    public function before(array|string $methods, string $pattern, object|callable $fn): void;

    /**
     * store a route and a handling function to be executed
     * when accessed using one of the specified methods.
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           array|string    $methods        allowed methods. single method as string, multiple as array
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     */
    public function add(array|string $methods, string $pattern, object|callable $fn): void;

    /**
     * shorthand for a route accessed using any method
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     */
    public function all(string $pattern, object|callable $fn): void;

    /**
     * shorthand for a route accessed using GET
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     */
    public function get(string $pattern, object|callable $fn): void;

    /**
     * shorthand for a route accessed using POST
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     */
    public function post(string $pattern, object|callable $fn): void;

    /**
     * Shorthand for a route accessed using PATCH
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     */
    public function patch(string $pattern, object|callable $fn): void;

    /**
     * Shorthand for a route accessed using DELETE
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     */
    public function delete(string $pattern, object|callable $fn): void;

    /**
     * Shorthand for a route accessed using PUT
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     */
    public function put(string $pattern, object|callable $fn): void;

    /**
     * Shorthand for a route accessed using OPTIONS
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string          $pattern        a route pattern such as /about/system
     * @param           object|callable $fn             the handling function to be executed
     */
    public function options(string $pattern, object|callable $fn): void;

    /**
     * mounts a collection of callbacks onto a base route
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string          $baseRoute      the route sub pattern to mount the callbacks on
     * @param           callable        $fn             the callback method
     */
    public function mount(string $baseRoute, callable $fn): void;

    /**
     * get all request headers
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @return          array           the request headers
     */
    public function getRequestHeaders(): array;

    /**
     * get the request method used, taking overrides into account
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @return          string          the Request method to handle
     */
    public function getRequestMethod(): string;

    /**
     * set a default lookup namespace for callable methods
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string          $namespace      a given namespace
     */
    public function setNamespace(string $namespace): void;

    /**
     * get the given namespace before
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @return          string          the given namespace if exists
     */
    public function getNamespace(): string;

    /**
     * execute the router:
     * loop all defined before middleware's and routes, and execute the handling function if a match was found
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           object|callable $callback       function to be executed after a matching route was handled (= after router middleware)
     */
    public function run(object|callable $callback = null): bool;

    /**
     * set the 404 handling function
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           object|callable $fn             the function to be executed$
     */
    public function set404(object|callable $fn): void;

    /**
     * define the current relative URI.
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     */
    public function getCurrentUri(): string;

    /**
     * return server base path, and define it if isn't defined.
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     */
    public function getBasePath(): string;

    /**
     * explicilty sets the server base path.
     * to be used when your entry script path differs from your entry URLs.
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string          $serverBasePath base path to set
     */
    public function setBasePath(string $serverBasePath): void;
}
