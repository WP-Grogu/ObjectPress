<?php

namespace OP\Framework\Contracts;

interface WpCliCommand
{
    public function execute(array $args);
}
