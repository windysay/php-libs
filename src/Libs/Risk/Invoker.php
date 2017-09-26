<?php
namespace JMD\Libs\Risk;

use JMD\Libs\Risk\Interfaces\Request;


class Invoker
{

    private $commands;

    public function setCommands(Request $command)
    {
        $this->commands[] = $command;
    }


    public function execute()
    {
        /** @var Request $command */
        foreach ($this->commands as $command) {
            if (!$command->execute()) {
                return false;
            }
        }
        return true;
    }

}