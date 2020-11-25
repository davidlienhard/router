<?php
/**
 * contains \DavidLienhard\Router\Router class
 *
 * @package         tourBase
 * @author          David Lienhard <david.lienhard@tourasia.ch>
 * @version         1.0.0, 26.11.2020
 * @since           1.0.0, 26.11.2020, created
 * @copyright       tourasia
 */

declare(strict_types=1);

namespace DavidLienhard\Router;

use \DavidLienhard\Router\RouterInterface;

/**
 * provides routing functionality
 *
 * @author          David Lienhard <david.lienhard@tourasia.ch>
 * @version         1.0.0, 26.11.2020
 * @since           1.0.0, 26.11.2020, created
 * @copyright       tourasia
 */
class Router implements RouterInterface
{
    /**
     * list of dependencies to load
     * @var     array           $dependencies
     */
    private $dependencies = [ ];

    /**
     * list of all supported http request methods
     * @var     array           $supportedMethods
     */
    private $supportedMethods = [
        "GET",
        "POST",
        "PUT",
        "DELETE",
        "OPTIONS",
        "PATCH",
        "HEAD"
    ];

    /**
     * the route patterns and their handling functions
     * @var     array           $routes
     */
    private $routes = [];

    /**
     * the before middleware route patterns and their handling functions
     * @var     array           $beforeRoutes
     */
    private $beforeRoutes = [];

    /**
     * the function to be executed when no route has been matched
     * @var     object|callable $notFoundCallback
     */
    protected $notFoundCallback;

    /**
     * current base route, used for (sub)route mounting
     * @var     string          $baseRoute
     */
    private $baseRoute = '';

    /**
     * the request method that needs to be handled
     * @var     string          $requestedMethod
     */
    private $requestedMethod = '';

    /**
     * the server base path for router execution
     * @var     string          $serverBasePath
     */
    private $serverBasePath;

    /**
     * default controllers namespace
     * @var     string          $namespace
     */
    private $namespace = '';


    /**
     * sets the given dependencies
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           array           $dependencies   associative array of dependencies. key will be the new name of the var
     * @return          void
     * @uses            self::$dependencies
     */
    public function __construct(array $dependencies = [ ])
    {
        $this->dependencies = $dependencies;
    }

    /**
     * loads the dependencies and requires the given file
     * if the files does not exist the notFound handler wil be used
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           string          $file           file to require
     * @return          void
     * @uses            self::$dependencies
     * @uses            self::handleNotFound()
     */
    public function require(string $file) : void
    {
        foreach ($this->dependencies as $varName => $varValue) {
            $$varName = $varValue;
        }

        if (file_exists($file)) {
            require_once $file;
        } else {
            $this->handleNotFound();
        }
        exit;
    }

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
     * @uses            self::before()
     * @uses            self::$supportedMethods
     */
    public function beforeAll(string $pattern, $fn)
    {
        $this->before($this->supportedMethods, $pattern, $fn);
    }

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
     * @uses            self::$baseRoute
     * @uses            self::$beforeRoutes
     */
    public function before($methods, string $pattern, $fn)
    {
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (self::formatMethods($methods) as $method) {
            $this->beforeRoutes[$method][] = [
                'pattern' => $pattern,
                'fn' => $fn,
            ];
        }
    }

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
     * @uses            self::$baseRoute
     * @uses            self::$routes
     */
    public function add($methods, string $pattern, $fn)
    {
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (self::formatMethods($methods) as $method) {
            $this->routes[$method][] = [
                'pattern' => $pattern,
                'fn' => $fn,
            ];
        }
    }

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
     * @uses            self::add()
     * @uses            self::$supportedMethods
     */
    public function all(string $pattern, $fn)
    {
        $this->add($this->supportedMethods, $pattern, $fn);
    }

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
     * @uses            self::add()
     */
    public function get(string $pattern, $fn)
    {
        $this->add('GET', $pattern, $fn);
    }

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
     * @uses            self::add()
     */
    public function post(string $pattern, $fn)
    {
        $this->add('POST', $pattern, $fn);
    }

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
     * @uses            self::add()
     */
    public function patch(string $pattern, $fn)
    {
        $this->add('PATCH', $pattern, $fn);
    }

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
     * @uses            self::add()
     */
    public function delete(string $pattern, $fn)
    {
        $this->add('DELETE', $pattern, $fn);
    }

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
     * @uses            self::add()
     */
    public function put(string $pattern, $fn)
    {
        $this->add('PUT', $pattern, $fn);
    }

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
     * @uses            self::add()
     */
    public function options(string $pattern, $fn)
    {
        $this->add('OPTIONS', $pattern, $fn);
    }

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
     * @uses            self::$baseRoute
     */
    public function mount(string $baseRoute, $fn)
    {
        // Track current base route
        $curBaseRoute = $this->baseRoute;

        // Build new base route string
        $this->baseRoute .= $baseRoute;

        // Call the callable
        call_user_func($fn);

        // Restore original base route
        $this->baseRoute = $curBaseRoute;
    }

    /**
     * get all request headers
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @return          array           the request headers
     */
    public function getRequestHeaders()
    {
        $headers = [];

        // If getallheaders() is available, use that
        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            // getallheaders() can return false if something went wrong
            if ($headers !== false) {
                return $headers;
            }
        }

        // Method getallheaders() not available or went wrong: manually extract 'm
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_' || $name == 'CONTENT_TYPE' || $name == 'CONTENT_LENGTH') {
                $headers[str_replace([' ', 'Http'], ['-', 'HTTP'], ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    /**
     * get the request method used, taking overrides into account
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @return          string          the Request method to handle
     */
    public function getRequestMethod()
    {
        // Take the method as found in $_SERVER
        $method = $_SERVER['REQUEST_METHOD'] ?? "";

        // If it's a HEAD request override it to being GET and prevent any output, as per HTTP Specification
        // @url http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') { // If it's a POST request, check for a method override header
            $headers = $this->getRequestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], ['PUT', 'DELETE', 'PATCH'])) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }

        return $method;
    }

    /**
     * set a default lookup namespace for callable methods
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           string          $namespace      a given namespace
     * @return          void
     * @uses            self::$namespace
     */
    public function setNamespace($namespace)
    {
        if (is_string($namespace)) {
            $this->namespace = $namespace;
        }
    }

    /**
     * get the given namespace before
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @return          string          the given Namespace if exists
     * @uses            self::$namespace
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

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
     * @uses            self::$requestedMethod
     * @uses            self::getRequestMethod()
     * @uses            self::$beforeRoutes
     * @uses            self::handle()
     * @uses            self::$routes
     * @uses            self::$notFoundCallback
     */
    public function run($callback = null)
    {
        // Define which method we need to handle
        $this->requestedMethod = $this->getRequestMethod();

        // Handle all before middlewares
        if (isset($this->beforeRoutes[$this->requestedMethod])) {
            $this->handle($this->beforeRoutes[$this->requestedMethod]);
        }

        // Handle all routes
        $numHandled = 0;
        if (isset($this->routes[$this->requestedMethod])) {
            $numHandled = $this->handle($this->routes[$this->requestedMethod], true);
        }

        // If no route was handled, trigger the 404 (if any)
        if ($numHandled === 0) {
            $this->handleNotFound();
        } else { // If a route was handled, perform the finish callback (if any)
            if ($callback && is_callable($callback)) {
                $callback();
            }
        }

        // If it originally was a HEAD request, clean up after ourselves by emptying the output buffer
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_end_clean();
        }

        // Return true if a route was handled, false otherwise
        return $numHandled !== 0;
    }

    /**
     * set the 404 handling function
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           object|callable $fn             the function to be executed$
     * @return          void
     * @uses            self::$notFoundCallback
     */
    public function set404($fn)
    {
        $this->notFoundCallback = $fn;
    }

    /**
     * handle a a set of routes:
     * if a match is found, execute the relating handling function
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           array           $routes         collection of route patterns and their handling functions
     * @param           bool            $quitAfterRun   does the handle function need to quit after one route was matched?
     * @return          int             the number of routes handled
     * @uses            self::getCurrentUri()
     * @uses            self::invoke()
     */
    private function handle(array $routes, bool $quitAfterRun = false)
    {
        // Counter to keep track of the number of routes we've handled
        $numHandled = 0;

        // The current page URL
        $uri = $this->getCurrentUri();

        // Loop all routes
        foreach ($routes as $route) {
            // Replace all curly braces matches {} into word patterns (like Laravel)
            $route['pattern'] = preg_replace('/\/{(.*?)}/', '/(.*?)', $route['pattern']);

            // we have a match!
            if (preg_match_all('#^' . $route['pattern'] . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {
                // Rework matches to only contain the matches, not the orig string
                $matches = array_slice($matches, 1);

                // Extract the matched URL parameters (and only the parameters)
                $params = array_map(function ($match, $index) use ($matches) {

                    // We have a following parameter: take the substring from the current param position until the next one's position
                    // (thank you PREG_OFFSET_CAPTURE)
                    if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                        return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                    } // We have no following parameters: return the whole lot

                    return isset($match[0][0]) ? trim($match[0][0], '/') : null;
                }, $matches, array_keys($matches));

                // Call the handling function with the URL parameters if the desired input is callable
                $this->invoke($route['fn'], $params);

                ++$numHandled;

                // If we need to quit, then quit
                if ($quitAfterRun) {
                    break;
                }
            }
        }

        // Return the number of routes handled
        return $numHandled;
    }

    /**
     * invokes a given function / method
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           callable|object $fn             function / method to invoke
     * @param           array           $params         list of parameters
     * @return          void
     * @uses            self::getNamespace()
     */
    private function invoke($fn, array $params = [])
    {
        if (is_callable($fn)) {
            call_user_func_array($fn, $params);
        } elseif (stripos($fn, '@') !== false) {  // If not, check the existence of special parameters
            // Explode segments of given route
            list($controller, $method) = explode('@', $fn);
            // Adjust controller class if namespace has been set
            if ($this->getNamespace() !== '') {
                $controller = $this->getNamespace() . '\\' . $controller;
            }
            // Check if class exists, if not just ignore and check if the class exists on the default namespace
            if (class_exists($controller)) {
                // First check if is a static method, directly trying to invoke it.
                // If isn't a valid static method, we will try as a normal method invocation.
                if (call_user_func_array([new $controller(), $method], $params) === false) {
                    // Try to call the method as an non-static method. (the if does nothing, only avoids the notice)
                    if (forward_static_call_array([$controller, $method], $params) === false) {
                        //
                    }
                }
            }
        }
    }

    /**
     * define the current relative URI.
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @return          string
     * @uses            self::getBasePath()
     */
    public function getCurrentUri()
    {
        // Get the current Request URI and remove rewrite base path from it (= allows one to run the router in a sub folder)
        $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen($this->getBasePath()));

        // Don't take query params into account on the URL
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        // Remove trailing slash + enforce a slash at the start
        return '/' . trim($uri, '/');
    }

    /**
     * return server base path, and define it if isn't defined.
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @return          string
     * @uses            self::$serverBasePath
     */
    public function getBasePath()
    {
        // Check if server base path is defined, if not define it.
        if ($this->serverBasePath === null) {
            $this->serverBasePath = implode(
                '/',
                array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)
            ) . '/';
        }

        return $this->serverBasePath;
    }

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
     * @uses            self::$serverBasePath
     */
    public function setBasePath($serverBasePath)
    {
        $this->serverBasePath = $serverBasePath;
    }

    /**
     * returns the given methods in correct format
     * always as array and upperstring
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @param           array|string    $methods        allowed methods. single method as string, multiple as array
     * @return          array
     */
    private static function formatMethods($methods)
    {
        return array_map(function ($method) {
            return \mb_strtoupper($method);
        }, !is_array($methods) ? [ $methods ] : $methods);
    }

    /**
     * handles the event if a page is not found
     * this can either be an inexistent route or a not existig file
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @version         1.0.0, 26.11.2020
     * @since           1.0.0, 26.11.2020, created
     * @copyright       tourasia
     * @return          void
     * @uses            self::$notFoundCallback
     * @uses            self::invoke()
     */
    private function handleNotFound()
    {
        if ($this->notFoundCallback) {
            $this->invoke($this->notFoundCallback);
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        }
        exit;
    }
}
