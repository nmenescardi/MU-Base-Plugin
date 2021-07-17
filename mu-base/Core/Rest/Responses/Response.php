<?php

namespace MUBase\Core\Rest\Responses;

class Response 
{
    protected $headers = [];
    protected $status;

    protected $methods = [
        'ok' => [
            'message'   => 'Success', 
            'status'    => 200
        ],
        'notFound' => [
            'message'   => 'Resource Not Found', 
            'status'    => 404
        ],
        'failed' => [
            'message'   => 'Internal Server Error', 
            'status'    => 500
        ],
        'unavailable' => [
            'message'   => 'Service Unavailable', 
            'status'    => 503
        ],
    ];

    public function addHeaders(array $customHeaders): void
    {
        $this->headers = array_merge(
            $this->headers,
            $customHeaders
        );
    }

    /**
     * Allows the available methods to be used like $response->ok()
     */
    public function __call(string $method, $args): void
    {
        if (isset($this->methods[$method]))
            $this->init(
                $args['message'] ?: $this->methods[$method]['message'], 
                $args['status'] ?: $this->methods[$method]['status']
            );
    }

    public function get(): \WP_REST_Response
    {
        return new \WP_REST_Response(
            $this->headers,
            $this->status
        );
    }

    protected function init(string $message, int $status){
        $this->addHeaders([
            'message' => $message,
            'status' => $status,
        ]);
        $this->status = $status; 
    }
}
