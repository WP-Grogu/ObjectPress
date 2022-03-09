<?php

namespace OP\Framework\Contracts;

interface WpCliCommand
{
    /**
     * The action done by this command.
     *
     * @param array $args The conmmand arguments as returned by WpCLI.
     * @return void
     */
    public function execute(array $args);
}
