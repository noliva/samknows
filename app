#!/usr/bin/env php
<?php

namespace App;

require __DIR__.'/vendor/autoload.php';

use App\Console\FetchDataConsole;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new FetchDataConsole());

$application->run();
