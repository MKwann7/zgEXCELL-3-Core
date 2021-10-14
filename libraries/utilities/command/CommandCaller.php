<?php

namespace App\Utilities\Command;

use App\Core\App;

class CommandCaller
{
    public $name;
    protected $commandId;
    protected $startTime;
    protected $startUnixTimestamp;
    protected $endtTime;
    protected $endUnixTimestamp;
    protected $strFileInstancePath;
    protected $intInstatiationTime;
    protected $intNextTime;
    protected $intInterval = 60;
    protected $strCommandExecutionType = "interval";

    /** @var Command $this->objCommand */
    protected $objCommand;
    protected $objCommandInsanceData;
    protected $objHistory;
    protected $intTries;
    protected $blnBootError;
    protected $instanceLimit = 1;
    protected $haltProcess = false;

    public function __construct($name, $objCommand = null)
    {
        $this->name = $name;
        $this->commandId = rand(10000,99999);
        $this->startTime = date("Y-m-d H:i:s");
        $this->startUnixTimestamp = strtotime("now");
        $this->strFileInstancePath = AppStorage . "commands/{$this->name}.json";
        $this->objCommand = $objCommand;
        $this->intTries = $objCommand->tries ?? 5;

        if (empty($this->objCommand))
        {
            return;
        }

        $this->objCommand->Init($this);
    }

    public function Run(App $app) : void
    {
        if(!$this->getOrCreateCommandInstanceData() && $app->blnForceCommands !== true)
        {
            return;
        }

        $this->intInstatiationTime = strtotime("now");

        if ($this->strCommandExecutionType === "interval")
        {
            $this->intNextTime = ($this->objHistory->last_execution + $this->intInterval);
        }

        if ($this->intNextTime <= $this->intInstatiationTime || $app->blnForceCommands === true )
        {
            if (method_exists($this->objCommand, "Run"))
            {
                try
                {
                    $this->updateCommandInsance(true, strtotime("now"), "processing");

                    $result = $this->objCommand->Run();

                    $this->clearCommandInsance();
                }
                catch(\Exception $ex)
                {
                    $this->clearCommandInsance();
                }
            }
            else
            {
                $this->updateCommandInsance(
                    false,
                    strtotime("now"),
                    "fail",
                    "Run method does not exist in " . $this->name,
                    ["Cannot execute 'Run method on command in ExcellCommand::Run() on line 1405.'"]);
            }
        }
        else
        {
            echo ($this->intNextTime . " is greater than " .  $this->intInstatiationTime . PHP_EOL);
        }
    }

    public function updateCommandInsance($running = null, $lastExecution = null, $result = null, $message = null, $errors = null) : void
    {
        $this->objCommandInsanceData = $this->addCommandInstanceToData($this->objCommandInsanceData, $lastExecution);
        $this->installCommandInstance($running, $lastExecution, $result, $message, $errors);
    }

    protected function installCommandInstance($running = null, $lastExecution = null, $result = null, $message = null, $errors = null) : void
    {
        $this->objCommandInsanceData["history"]["last_execution"] = $lastExecution ?? $this->objCommandInsanceData["history"]["last_execution"] ?? 0;
        $this->objCommandInsanceData["history"]["running"] = $running ?? $this->objCommandInsanceData["history"]["running"] ?? false;
        $this->objCommandInsanceData["history"]["result"] = $result ?? $this->objCommandInsanceData["history"]["result"] ?? null;
        $this->objCommandInsanceData["history"]["message"] = $message ?? $this->objCommandInsanceData["history"]["message"] ?? null;
        $this->objCommandInsanceData["history"]["errors"] = $errors ?? $this->objCommandInsanceData["history"]["errors"] ?? [];

        if (!file_put_contents($this->strFileInstancePath, json_encode($this->objCommandInsanceData)))
        {
            die("We were unable to Update the file: " . $this->strFileInstancePath);
        }
    }

    protected function clearCommandInsance()
    {
        $this->endtTime = date("Y-m-d H:i:s");
        $this->endUnixTimestamp = strtotime("now");

        $this->objCommandInsanceData = $this->addCommandInstanceToData($this->objCommandInsanceData, time());

        $blnStillRunning = false;
        $strProcessing = "completed";
        $strMessage[] = "Process completed.";

        foreach ($this->objCommandInsanceData["commands"] as $commandId => $command)
        {
            if (empty($command["end"]))
            {
                $blnStillRunning = true;
                $strProcessing = "processing";
                $strMessage[] = "{$commandId} is running.";
            }
        }

        if ($blnStillRunning === true)
        {
            unset($strMessage[0]);
        }

        $this->updateCommandInsance(
            $blnStillRunning,
            strtotime("now"),
            $strProcessing,
            implode(" ", $strMessage),
            []);

        $this->objCommandInsanceData = $this->getUpdateCommandInstance();

        $arCommands = [];

        foreach ($this->objCommandInsanceData["history"]["commands"] as $commandId)
        {
            if ($commandId !== $this->commandId)
            {
                $arCommands[] = $commandId;
            }
        }

        $this->objCommandInsanceData["history"]["commands"] = $arCommands;
        $this->updateCommandInsance(
            $blnStillRunning,
            strtotime("now"),
            $strProcessing,
            implode(" ", $strMessage),
            []);
    }

    protected function getUpdateCommandInstance()
    {
        return $this->objCommandInsanceData = json_decode(file_get_contents($this->strFileInstancePath), true);
    }

    protected function generateCommandInstanceData($currentTime = null) : array
    {
        $intDuration = $currentTime ?? time();

        return [
            "start_time" => $this->startTime,
            "start" => $this->startUnixTimestamp,
            "duration" => ($intDuration - $this->startUnixTimestamp),
            "end_time" => $this->endtTime,
            "end" => $this->endUnixTimestamp
        ];
    }

    protected function addCommandInstanceToData($commandInstanceData, $currentTime = null) : array
    {
        $commandInstanceData["commands"][$this->commandId] = $this->generateCommandInstanceData($currentTime);

        return $commandInstanceData;
    }

    protected function addCommandIdToHistory(&$commandInstanceData) : void
    {
        if (empty($commandInstanceData["history"]))
        {
            $commandInstanceData["history"] = [
                "commands" => [$this->commandId],
            ];
            return;
        }

        if (in_array($this->commandId, $commandInstanceData["history"]["commands"]))
        {
            return;
        }

        $commandInstanceData["history"]["commands"][] = $this->commandId;
    }

    protected function getOrCreateCommandInstanceData() : bool
    {
        if (is_file($this->strFileInstancePath))
        {
            $this->objCommandInsanceData = $this->addCommandInstanceToData($this->getUpdateCommandInstance());

        }
        else
        {
            $this->objCommandInsanceData = [
                "commands" => [$this->commandId => $this->generateCommandInstanceData()]
            ];
        }

        if (count(array_filter($this->objCommandInsanceData["history"]["commands"])) >= $this->instanceLimit)
        {
             return false;
        }

        $this->addCommandIdToHistory($this->objCommandInsanceData);

        if ($this->objCommand === null)
        {
            $this->updateCommandInsance(
                false,
                0,
                null,
                "A null command object was passed in for " . $this->name,
                ["Command object was null during command caller instantiation."]);

            $this->blnBootError = true;

            return false;
        }

        $this->updateCommandInsance(false );

        $this->objHistory = json_decode(json_encode($this->objCommandInsanceData["history"]));

        return true;
    }

    public function onDemand()  : self
    {
        $this->intInterval = -1;
        $this->setOnDemand();
        return $this;
    }

    public function always()  : self
    {
        $this->intInterval = 0;
        $this->setInterval();
        return $this;
    }

    public function everyMinute()  : self
    {
        $this->intInterval = 60;
        $this->setInterval();
        return $this;
    }

    public function everyTwoMinutes() : self
    {
        $this->intInterval = 120;
        $this->setInterval();
        return $this;
    }

    public function everyFiveMinutes() : self
    {
        $this->intInterval = 300;
        $this->setInterval();
        return $this;
    }

    public function everyTenMinutes() : self
    {
        $this->intInterval = 600;
        $this->setInterval();
        return $this;
    }

    public function everyFifteenMinutes() : self
    {
        $this->intInterval = 900;
        $this->setInterval();
        return $this;
    }

    public function everyTwentyMinutes() : self
    {
        $this->intInterval = 1200;
        $this->setInterval();
        return $this;
    }

    public function everyThirtyMinutes() : self
    {
        $this->intInterval = 1800;
        $this->setInterval();
        return $this;
    }

    public function everyHour() : self
    {
        $this->intInterval = 3600;
        $this->setInterval();
        return $this;
    }

    public function dailyAt($hour_minutes) : self
    {
        $this->intInterval = strtotime( date("Y-m-d " . $hour_minutes));
        $this->setAtTime();
        return $this;
    }

    protected function setOnDemand()
    {
        $this->strCommandExecutionType = "on-demand";
        return $this;
    }

    protected function setInterval()
    {
        $this->strCommandExecutionType = "interval";
        return $this;
    }

    protected function setAtTime()
    {
        $this->strCommandExecutionType = "at-time";
        return $this;
    }
}
