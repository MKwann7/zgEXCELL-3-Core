<?php
/**
 * ENGINECORE Configuration File for zgWeb.Solutions Web.CMS.App
 */

use App\Utilities\Excell\ExcellCollection;

$commands = explode(",", env("COMMANDS"));
$commandRegistration = [];

if (count($commands) > 0)
{
    foreach($commands as $currCommand)
    {
        $commandDetails = explode("|", $currCommand);

        if (empty($commandDetails[1]) || !method_exists(\App\Utilities\Command\CommandCaller::class, $commandDetails[1]))
        {
            $commandRegistration[] = $this->registerCommand($commandDetails[0])->always();
        }
        else
        {
            $commandRequest = $commandDetails[1];
            $commandRegistration[] = $this->registerCommand($commandDetails[0])->$commandRequest();
        }

    }
}

$this->lstAppCommands = (new ExcellCollection())->Load($commandRegistration);