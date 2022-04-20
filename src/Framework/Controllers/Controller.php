<?php

namespace OP\Framework\Controllers;

use InvalidArgumentException;
use OP\Support\Facades\Blade;
use OP\Support\Facades\ObjectPress;
use OP\Framework\Contracts\Renderable;

abstract class Controller implements Renderable
{
    /**
     * Loads and render the controller.
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        if (!$this->view) {
            throw new InvalidArgumentException('The controller must have a view.');
        }

        return $this->render($this->view, $this->with());
    }


    /**
     * Renders the page using blade.
     *
     * @param string $view  The Vue.js vue to load.
     * @param array  $with  The data to send to the view.
     *
     * @return void
     */
    protected function render(string $view, array $with = [])
    {
        $with = $with + $this->getAdditionnalParams();
        
        echo ObjectPress::view()->make($view, $with)->render();
        
        return;
    }


    /**
     * Get additionnal data to be sent with the view.
     *
     * @return array
     */
    private function getAdditionnalParams()
    {
        return [
            //
        ];
    }
}
