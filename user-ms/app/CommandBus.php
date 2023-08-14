<?php

namespace App;

use Illuminate\Support\Facades\App;
use ReflectionClass;

class CommandBus
{
    public function handle($command)
    {
        $reflection = new ReflectionClass($command);
        $name = str_replace("Command", "Handler", $reflection->getShortName());
        $name = str_replace($reflection->getShortName(), $name, $reflection->getName());
        $handler = App::make($name);

        return $handler($command);
    }
}
