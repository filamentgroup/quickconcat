<?php

require dirname(__DIR__). '/vendor/autoload.php';

use Symfony\Component\Process\Process;
	
// bootup a server for running the tests
$process = new Process('php -S localhost:8888 -t '. realpath(dirname(__DIR__)));
$process->setTimeout(1800);
$process->run();

if (!$process->isSuccessful()) {
    throw new RuntimeException($process->getErrorOutput());
}
