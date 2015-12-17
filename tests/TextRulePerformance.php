#!/usr/bin/env php
<?php

use Trellis\Runtime\Validator\Rule\Type\TextRule;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$rule = new TextRule('text', []);

$times = [];

$num = 10;
$repeats = 100000;

for ($t = 0; $t < $num; $t++) {
    $start = microtime(true);
    for ($i = 0; $i < $repeats; $i++) {
        //$rule->apply(' this is a test string w/o very special characters… ');
        $rule->apply(' this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters…  this is a test string w/o very special characters… ');
    }
    $end = microtime(true);
    $times[$t] = round(($end - $start) * 1000, 3);
    echo $times[$t] . 'ms ';
}

$sum = 0;
for ($i = 0; $i < $num; $i++) {
    $sum += $times[$i];
}

echo PHP_EOL . 'Average time for ' . $repeats . ' text rule validations: ' . round($sum / $num, 3) . PHP_EOL;

// String: ' this is a test string w/o very special characters… ' & the same x20
// is_string only in TextRule: avg=183ms => w/ 20x longer text: avg=183ms
//
// preg_match => 10049.92 – preg_replace => 11683.078 – replace ".*?" with "[^\pC\pZ]*" => 5148ms (but breaks the tests)
//
// default options on trimmable string: avg=3267ms 3205ms => w/ 20x longer text: avg=10290ms
// default options on trimmable string w/o toBoolean: avg=2660ms, 2690ms => w/ 20x longer text: avg=9777ms, 9885ms
// default options on trimmable string w/o toBoolean and simple trim(): avg=2066ms, 2100ms => w/ 20x longer text: avg=4362ms, 4415ms
//
// w/o toBoolean: ~18% faster on short text, 5% on 20x longer text
// w/o toBoolean and w/ trim(): ~36,7%, ~34,5% on short text, ~57,6%, 57,1% on 20x longer text
//
