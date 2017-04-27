<?php

$ipcKey = ftok(__FILE__ , 'a');

$pid = pcntl_fork();

if ($pid > 0) {

} elseif ($pid == 0) {
    $semId = sem_get($ipcKey);

    sem_acquire($semId);
    echo 'child';
    sleep(5);
    sem_release($semId);
    exit;
} else {
    exit('end');
}

sleep(2);
$semId = sem_get($ipcKey);
$result = sem_acquire($semId, true);
var_dump($result);