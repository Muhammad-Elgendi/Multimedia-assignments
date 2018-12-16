<?php
/**
 * Developed by : Muhammad Elgendi
 * Date : 14/12/2018
 * 
 * || Notes ||
 *  This class implemented to comply Fluent interface pattern 
 *  You can build the tree of Shannon Fano from a message or predefined table
 */

    // build code book from a predefined table (letters with frequencies)
    // $table = ['B'=> 3,'L'=> 2,'E'=> 2,'I'=>1,'A'=>1,'T'=>1,'S'=>1,'N'=>1];
    // $shannon = (new ShannonFano())->setTable($table);

    // // build code book from a message
    // $shannon =(new ShannonFano())->setMessage('BBBLLEEIATSN');

    // $shannon->buildCodeBook()->encodeMessage();
    
    // // print code book
    // print_r($shannon->getCodeBook());
    
    // // print encoded message
    // echo $shannon->getCompressedCode()."\n";


class ShannonFano{

    private $table;
    private $codeBook;
    private $message;
    private $compressedCode;

    // Build the table from an assiochative array
    public function setTable($table){
        arsort($table);
        $this->table = $table;
        $this->codeBook = array_fill_keys(array_keys($this->table), '');
        return $this;
    }

    // Build the table from a message
    public function setMessage($msg){
        $this->message = $msg;
        $chars = array_unique(str_split($msg));
        $frequency = count_chars($msg,1);
        $table = array_fill_keys(array_values($chars), 0);
        $keys = array_keys($frequency);
        $values = array_values($frequency);
        for($i=0;$i<count($frequency);$i++){
            $key = chr($keys[$i]);
            $table[$key] = $values[$i];
        }
        arsort($table);
        $this->table = $table;
        $this->codeBook = array_fill_keys(array_keys($this->table), '');
        return $this;
    }

    public function buildCodeBook(){ 
        foreach($this->table as $key => $value){
            $tempTable =$this->table;
    
            while( count($tempTable) > 1){
        
                $tempFrequency =array_values($tempTable);
                $point = $this->getSeprationPoint($tempFrequency);
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
        return $this;
    }

    public function encodeMessage(){
        $this->compressedCode = "";
        $array = str_split($this->message);
        foreach($array as $letter){
                $this->compressedCode .= $this->codeBook[$letter];
        }
        return $this;
    }

    public function getCodeBook(){
        return $this->codeBook;
    }

    public function getCompressedCode(){
        return $this->compressedCode;
    }

    private function getSeprationPoint($frequency){
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