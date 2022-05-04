#!/usr/bin/php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\ApiController;
use App\Exceptions\CommandException;

try {
    unset($argv[0]);
    $commands = ['get','add','delete'];
    $storage = array_shift($argv);
    $storage = ($storage) ? strtolower($storage) : $storage;
    if (empty($storage) || !ctype_alpha($storage)) {
        throw new CommandException('Provide Storage details');
    }
    $count = count($argv);
    if (empty($argv) || $count < 1) {
        throw new CommandException('Provide Command');
    }
    $command = strtolower($argv[0]);
    $key = (isset($argv[1])) ? strtolower($argv[1]) : null;
    $value = (isset($argv[2])) ? strtolower($argv[2]) : null;
    $api = new ApiController(true);
    if (!method_exists($api, $command) && !in_array($command, $commands)) {
        throw new CommandException('Command "' . $command . '" not found');
    }
    if ($command == $commands[0]) {
        $query = $api->{$command}($storage);
    }
    if ($command == $commands[1]) {
        if (is_null($key) || is_null($value)) {
            throw new CommandException('Provide Command details with options (key and value)');
        }
        $query = $api->{$command}($storage, $key, $value);
    }
    if ($command == $commands[2]) {
        if (is_null($key)) {
            throw new CommandException('Provide Command with key');
        }
        $query = $api->{$command}($storage, $key);
    }
    if (isset($query['data'])) {
        $result = (isset($query['data']['message']))
            ? $query['data']['message']
            : print_r($query['data'], true);
        echo $result . "\n";
    }
} catch (CommandException $e) {
    echo 'Error: ' . $e->getMessage(). "\n";
}