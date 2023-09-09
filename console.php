#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

use App\Command\CreateCommand;

// Read Averages
use App\Command\ReadCommandAvgGroup;
use App\Command\ReadCommandAvgSubject;
use App\Command\ReadCommandAvgGroupSubject;

// Read Averages - Over 70%
use App\Command\ReadCommandAvgGroup70;
use App\Command\ReadCommandAvgSubject70;
use App\Command\ReadCommandAvgGroupSubject70;

use App\Command\UpdateCommand;
use App\Command\DeleteCommand;

$app = new Application();
$app->add(new CreateCommand());
$app->add(new ReadCommandAvgGroup());
$app->add(new ReadCommandAvgSubject());
$app->add(new ReadCommandAvgGroupSubject());

// Read Averages - Over 70%
$app->add(new ReadCommandAvgGroup70());
$app->add(new ReadCommandAvgSubject70());
$app->add(new ReadCommandAvgGroupSubject70());

$app->add(new UpdateCommand());
$app->add(new DeleteCommand());
$app->run();