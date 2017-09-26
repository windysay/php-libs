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
        foreach ($this->commands as $count => $command) {
            $data = $command->execute();
            $format = new DataFormat($data);
            if ($format->isError()) {
                return [
                    'error' => 1,
                    'msg' => $format->getMsg(),
                    'currentCount' => $count + 1
                ];
            }
        }
        return ['error' => 0, 'msg' => ''];
    }

}