<?php
namespace App\Middleware;

/**
 * Check the user's permissions
 */
class Permissions
{
    private $container;
    private $keys = [];

    /**
     * @param $container Slim application container
     * @param array $keys
     */
    public function __construct($container, $keys)
    {
        $this->container = $container;
        $this->keys = $keys;
    }

    /**
     * Permissions middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        if ($this->container->has('jwt')) {
            if (empty(array_intersect($this->keys, $this->container->jwt->user->permissions))) {
                return \App\Api\code_403($response);
            }
        }

        $response = $next($request, $response);
        return $response;
    }
}
