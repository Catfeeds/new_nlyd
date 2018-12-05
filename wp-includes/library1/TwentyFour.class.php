<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/6/28
 * Time: 17:38
 */

namespace library;

class TwentyFour
{

    public $needle = 24;
    public $precision = '1e-6';
    public $list = array();

    /*private function notice($mesg) {
        var_dump($mesg);
    }*/
    /**
     * 取得用户输入方法
     */
    public function calculate($operants = array()) {
        //$operants = array(8,8,5,4);
        try {
            $this->list = array();
            $this->search($operants, 4);
        }
        catch (Exception $e) {
            //$this->notice($e->getMessage());
            return false;
        }
        //print_r($this->list);
        //$this->notice('can\'t compute!');
        return array_unique($this->list);
    }
    /**
     * 求24点算法PHP实现
     */
    private function search($expressions, $level) {

        if ($level == 1) {
            $result = 'return ' . $expressions[0] . ';';

            if(abs(eval($result) - $this->needle) == 0){

                $my_answer = str_replace('*','×',$expressions[0]);
                $my_answer = str_replace('/','÷',$my_answer);

                $this->list[] = $my_answer;
                //print_r($my_answer);
            }
            /*var_dump(eval($result));
            var_dump($expressions[0]);
            if ( abs(eval($result) - $this->needle) <= $this->precision) {
                throw new Exception($expressions[0]);
            }*/
        }
        for ($i=0;$i<$level;$i++) {
            for ($j=$i+1;$j<$level;$j++) {
                $expLeft = $expressions[$i];
                $expRight = $expressions[$j];
                $expressions[$j] = $expressions[$level - 1];
                $expressions[$i] = '(' . $expLeft . ' + ' . $expRight . ')';
                $this->search($expressions, $level - 1);
                $expressions[$i] = '(' . $expLeft . ' * ' . $expRight . ')';
                $this->search($expressions, $level - 1);
                $expressions[$i] = '(' . $expLeft . ' - ' . $expRight . ')';
                $this->search($expressions, $level - 1);
                $expressions[$i] = '(' . $expRight . ' - ' . $expLeft . ')';
                $this->search($expressions, $level - 1);
                if ($expLeft != 0) {
                    $expressions[$i] = '(' . $expRight . ' / ' . $expLeft . ')';
                    $this->search($expressions, $level - 1);
                }
                if ($expRight != 0) {
                    $expressions[$i] = '(' . $expLeft . ' / ' . $expRight . ')';
                    $this->search($expressions, $level - 1);
                }
                $expressions[$i] = $expLeft;
                $expressions[$j] = $expRight;
            }
        }
        //leo_dump($expressions[0]);
        return false;
    }


    function __destruct() {
    }
}
