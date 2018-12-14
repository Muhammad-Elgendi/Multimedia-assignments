<?php

    $table = ['B'=> 3,'L'=> 2,'E'=> 2,'I'=>1,'A'=>1,'T'=>1,'S'=>1,'N'=>1];
    $shannon = new ShannonFano($table);
    $shannon->build_code_book();    
    print_r($shannon->codeBook);

class ShannonFano{

    public $table;
    public $codeBook;

    function __construct($table){
        arsort($table);
        $this->table = $table;
        $this->codeBook = array_fill_keys(array_keys($this->table), '');
    }

    public function build_code_book(){ 
        foreach($this->table as $key => $value){
            $tempTable =$this->table;
    
            while( count($tempTable) > 1){
        
                $tempFrequency =array_values($tempTable);
                $point = $this->get_sepration_point($tempFrequency);
                $leftBranch = array_slice($tempTable,0, $point+1, true);
                $rightBranch = array_slice($tempTable,$point+1,(count($tempTable)-($point+1)),true);
                if(in_array($key,array_keys($leftBranch))){
                    $this->codeBook[$key] .= '0';
                    $tempTable = $leftBranch;
                }
                else{
                    $this->codeBook[$key] .= '1';
                    $tempTable = $rightBranch;
                }
            }
        }
    }

    private function get_sepration_point($frequency){
        $result = [];
        for($i =0;$i<count($frequency);$i++){
            $upperHalf = $this->sum($frequency,0,$i);
            $lowerHalf = $this->sum($frequency,$i+1,count($frequency)-1);
            $result[$i] = abs($upperHalf -$lowerHalf);
        }
        return array_search(min($result),$result);
    }

    private function sum($array,$start,$end){
        $sum =0;

        for($i=$start;$i<=$end;$i++){
            $sum+=$array[$i];
        }

        return $sum;
    }
}