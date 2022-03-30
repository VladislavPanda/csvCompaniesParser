<?php

ini_set('max_execution_time', 0);

require_once 'CsvParser.php';
$file = 'csv/samplesort1.csv';
//$file = 'csv/test.csv';
   
(new CsvParser())->open($file)->parse(function ($data, CsvParser $csv) {

});


