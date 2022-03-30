<?php

ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);

$firms = file_get_contents('json/test.json');
$firms = json_decode($firms, true);

$categories = file_get_contents('json/categories.json');
$categories = json_decode($categories, true);

foreach($firms as $key => &$value){
    if($value['subcat_1'] != '' && $value['subcat_1'] != null && strpos($value['subcat_1'], '-') !== false){
        $exp = explode('-', $value['subcat_1']);
        $value['subcat_1'] = trim($exp[0]);

        foreach($categories as $k => $v){
            if($value['subcat_1'] == $v['SIC N']){
                $value['Subcat'] = $v['Subcat'];
                $value['Keywords'] = $v['Keywords'];
                unset($value['subcat_1']);
            }
        }

        if(!isset($value['Subcat']) && !isset($value['Keywords'])) unset($firms[$key]);
    }else{
        unset($firms[$key]);
    }
}
unset($value);

echo "<pre>";
var_dump($firms);
echo "</pre>";