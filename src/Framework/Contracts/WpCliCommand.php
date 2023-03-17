<?php

namespace OP\Framework\Contracts;

interface WpCliCommand
{
    /**
     * The action performed by the command.
     *
     * @param array $args The command arguments as returned by WpCLI.
     * @return void
     */
    public function execute(array $args);
}
