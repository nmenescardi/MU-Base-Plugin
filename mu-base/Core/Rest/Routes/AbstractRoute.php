<?php

namespace MUBase\Core\Rest\Routes;

use MUBase\Core\Rest\Responses\Response;

use function MUBase\Core\Helpers\app;

abstract class AbstractRoute {

    protected $request;
    protected $response;

    protected function namespace(): string
    {
        return app('routes.namespace');
    }

    /**
     * Specifies the request method. Default: 'GET'. See WP_REST_Server constants for more options
     */   
    protected function method(): string
    {
        return \WP_REST_Server::READABLE;
    }

    /**
     * Callback used as middleware to filter access to the route.
     */
    public function shouldAccess(): bool
    {
        return true;
    }

    /**
     * Register an endpoint with the WordPress REST API.
     *
     *  Some common methods to be used from WP_REST_Request:
     *      ->get_params(); ->get_param( $key ); ->get_headers(); ->get_header($key)
     *
     * @param WP_REST_Request $request
     */
    public function responseWrapper(\WP_REST_Request $request): \WP_REST_Response
    {
        $this->request = $request;
        $this->response = app('MUBase.routes.response');
        
        $this->respond();

        return $this->response->get();
    }
    
    // ? model args (default, required, sanitize_callback and validate_callback)
    public function init(): void
    {
        add_action('rest_api_init', function () {
            register_rest_route( 
                $this->namespace(), 
                $this->path(), 
                array(
                    array(
                        'methods' => $this->method(),
                        'callback' => array($this, 'responseWrapper'),
                        'permission_callback' => array($this, 'shouldAccess'),
                    )
                )
            );
        });
    }

    /**
     * Specifies the 'last part' of the URI. 
     * 
     * It also supports regular expressions like: /(?P<id>[\d]+)
     * More: https://developer.wordpress.org/rest-api/extending-the-rest-api/routes-and-endpoints/#path-variables
     */   
    protected abstract function path(): string;

    public abstract function respond(): void;
}