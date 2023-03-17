<?php

namespace OP\Framework\Contracts;

interface Renderable
{
    /**
     * The data sent to the view.
     *
     * @return array
     */
    public function with(): array;
}
