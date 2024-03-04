<?php

namespace App\Core\Commands;

use App\Core\App;
use App\Utilities\Command\Command;
use App\Utilities\Command\CommandCaller;
use App\Utilities\Excell\ExcellCollection;
use Error;

class AppCommands
{
    public function Run(App &$app): void
    {
        $commands = explode(",", env("COMMANDS"));
        $commandRegistration = [];

        if (count($commands) > 0)
        {
            foreach($commands as $currCommand)
            {
                $commandDetails = explode("|", $currCommand);

                if (empty($commandDetails[1]) || !method_exists(\App\Utilities\Command\CommandCaller::class, $commandDetails[1]))
                {
                    $commandRegistration[] = $this->registerCommand($app, $commandDetails[0])->always();
                }
                else
                {
                    $commandRequest = $commandDetails[1];
                    $commandRegistration[] = $this->registerCommand($app, $commandDetails[0])->$commandRequest();
                }

            }
        }

        $app->lstAppCommands = (new ExcellCollection())->Load($commandRegistration);
    }


    public function registerCommand(App &$app, $command_title) : CommandCaller
    {
        // Find command
        $objCommand = null;

        foreach($app->objAppEntities as $currModuleName => $currModule) {
            if (empty($currModule["Main"]["Commands"]) || !is_array($currModule["Main"]["Commands"]) || count($currModule["Main"]["Commands"]) === 0) {
                continue;
            }

            $arModuleCommands = $currModule["Main"]["Commands"];

            foreach($arModuleCommands as $currCommandName => $currCommandFileName) {
                $objCommandInstanceName = $currCommandFileName["name"] ?? "";
                if (empty($objCommandInstanceName)) {
                    continue;
                }

                if (strtolower($currCommandName) === strtolower($command_title)) {
                    /** @var Command $objCommandInstance */
                    try {
                        $objCommandInstance = new $currCommandFileName["class"]();
                        return new CommandCaller($objCommandInstanceName, $objCommandInstance);
                    } catch(Error $ex) {
                        return new CommandCaller($objCommandInstanceName, null);
                    }
                }
            }
        }

        return new CommandCaller($command_title, null);
    }
}