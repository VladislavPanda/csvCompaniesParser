<?php
exit;
ini_set('max_execution_time', 0);

require_once 'CsvParser.php';
$file = 'csv/sicsubcatkeywrds.csv';
//$file = 'csv/test.csv';
   
(new CsvParser())->open($file)->parse(function ($data, CsvParser $csv) {

});


