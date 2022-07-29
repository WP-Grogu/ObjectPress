<?php

namespace OP\Framework\Wordpress\Concerns;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.1.1
 * @access   public
 * @since    2.2.1
 */
trait Schedulable
{
    /**
     * Return the command as a scheduled event.
     */
    public function runSchedule()
    {
        return $this->execute([]);
    }
}
