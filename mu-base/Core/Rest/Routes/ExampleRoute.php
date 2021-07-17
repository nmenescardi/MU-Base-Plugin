<?php

namespace MUBase\Core\Rest\Routes;

class ExampleRoute extends AbstractRoute
{     

    public function path(): string
    {
        return 'example';
    }

    public function respond():void
    {

        $this->response->ok();

        //$this->response->ok('Custom success message');
        
        // $this->response->ok('Custom success message');
        // $this->response->addHeaders([
        //     'another_custom_key' => 'if needed'
        // ]);

        // $this->response->notFound();

        //$this->response->notFound('Custom success message', 403);
        
    }
}