<?php

ini_set('max_execution_time', 0);

// класса для разбора csv файла
class CsvParser
{
    // указатель на файл
    protected $handle;
   
    // заголовок csv файла
    protected $header;

    // Результирующий массив
    public static $result;

    // Массив необходимых полей
    private const HEADER_KEYS = [
                                 "company_name" => "CompanyName", 
                                 "comp_reg_number" => "CompanyNumber", 
                                 "address_1" => "RegAddress.AddressLine1", 
                                 "address_2" => "RegAddress.AddressLine2", 
                                 "address_city_town" => "RegAddress.PostTown", 
                                 "address_3" => "RegAddress.County", 
                                 "address_country_place" => "RegAddress.Country", 
                                 "postal_code" => "RegAddress.PostCode", 
                                 "company_category" => "CompanyCategory",
                                 "establish_date" => "IncorporationDate", 
                                 "accounts_lastmade" => "Accounts.LastMadeUpDate",
                                 "subcat_1" => "SICCode.SicText_1", 
                                 "subcat_2" => "SICCode.SicText_2", 
                                 "subcat_3" => "SICCode.SicText_4", 
                                 "url_comp_det" => "URI", 
                                 "status" => "CompanyStatus", 
                                 "origin" => "CountryOfOrigin"
                                ];


    /**
     * Открываем файл на чтение
     *
     * @param $file
     * @param bool $header
     * @return $this
     * @throws \Exception
     */
    public function open($file, $header = true)
    {
        $this->handle = fopen($file, 'r');
        if (!$this->handle) {
            throw new Exception('Невозможно прочитать файл ' . $file);
        }
        if ($header) {
            $this->header = fgetcsv($this->handle, 0, ";");

            foreach($this->header as $key => &$value){
                $value = trim($value);
            }
        }
        return $this;
    }

    /**
    * Разбирает файл, передавая данные из файла
    * в коллбэк функцию
    *
    */
    public function parse(callable $callable)
    {
        if (!$this->handle) {
            throw new Exception('Файл не открыт!');
        }
        while (($data = fgetcsv($this->handle, 0, ";")) !== false) {
            echo "<pre>";
            var_dump($data);
            echo "</pre>";
    
            if ($this->header) {
                $data = array_combine($this->header, $data);
            }

            /*$dataReplaced = [];
            foreach($data as $key => &$value){
                foreach(CsvParser::HEADER_KEYS as $k => $v){
                    if($key == $v) $dataReplaced[$k] = $value;
                }
            }*/

            //self::$result[] = $dataReplaced;
            self::$result[] = $data;
            // вызываем коллбэк, первый аргумент данные, второй ссылка на объект
            $callable($data, $this);
        }

        // Из cleaner.php
        /*foreach(self::$result as $key => &$value){
            // Удаляем символ " в названии компании
            $value['company_name'] = str_replace('"', '', $value['company_name']);
            
            // Проверка на символ ! в названии
            if(mb_substr($value['company_name'], 0, 1) == '!') unset(self::$result[$key]);
        
            // Проверка на статус
            if($value['status'] != 'Active') unset(self::$result[$key]);
        
            // Проверка на страну
            if($value['origin'] != 'United Kingdom') unset(self::$result[$key]);
        }

        // Из address.php
        foreach(self::$result as $key => &$value){
            $value['full_address'] = '';
            if($value['address_1'] != '') $value['full_address'] .= $value['address_1'] . ',';
            if($value['address_2'] != '') $value['full_address'] .= $value['address_2'] . ',';
            if($value['address_city_town'] != '') $value['full_address'] .= $value['address_city_town'] . ',';
            if($value['address_3'] != '') $value['full_address'] .= $value['address_3'] . ',';
            if($value['postal_code'] != '') $value['full_address'] .= $value['postal_code'];
            else $value['full_address'] = substr($value['full_address'], 0, -1);

            $value['full_address'] = trim($value['full_address']);

            unset($value['address_1']);
            unset($value['address_2']);
            unset($value['address_city_town']);
            unset($value['address_3']);

            $value['company_name'] = trim($value['company_name']);
        }*/

        // Переводим в json и записываем результирующий массив в файл
        $test = null;

        $res = '[';
        $prefix = '';

        foreach(self::$result as $row) {
            $test =  json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            if(json_last_error() > 0){
                continue;
            }

            //adding
            $res .=  $prefix . json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            $prefix = ',';

        }
        $res .= ']'; 

        //self::$result = json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        file_put_contents('json/test.json', $res);
    }


    public function __destruct() {
        $this->close();
    }


    public function close()
    {
        if ($this->handle) {
            fclose($this->handle);
            $this->handle = null;
        }
    }


    public function value($row, $field) {
        return $row[$field];
    }
}