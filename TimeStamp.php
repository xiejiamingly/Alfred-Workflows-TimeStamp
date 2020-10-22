<?php
ini_set('date.timezone','Asia/Shanghai');
require_once('workflows.php');

class TimeStamp{
    private function isDateTime($dateTime){
        $ret = strtotime($dateTime);
        return $ret !== FALSE && $ret != -1;
    }

    function msectime() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }

    //时间戳转成日期（毫秒级别）
    function getMsecToMescdate($msectime){
        $msectime = $msectime * 0.001;
        if(strstr($msectime,'.')){
            sprintf("%01.3f",$msectime);
            list($usec, $sec) = explode(".",$msectime);
            $sec = str_pad($sec,3,"0",STR_PAD_RIGHT);
        }else{
            $usec = $msectime;
            $sec = "000";
        }
        $date = date("Y-m-d H:i:s .x",$usec);
        return $mescdate = str_replace('x', $sec, $date);
    }

    public function getTimeStamp($query){
        $workflows = new Workflows();
        $now = $this->msectime();
        $query = trim($query);

        if ($query == 'now') {
            $workflows->result( $query,
                $this->msectime(),
                '当前时间戳(ms)：'.$this->msectime(),
                '当前时间：'.$this->getMsecToMescdate($now),
                'icon.png',CURLOPT_SSL_FALSESTART);

            echo $workflows->toxml();
        }
        if(is_numeric($query)){
            $cle = $query - $now;
            if ($cle > 0) {
                $d = floor($cle/1000/60/60/24);
                $h = floor($cle%(24*60*60*1000)/1000/60/60);
                $m = floor(($cle%(60*60*1000)/1000/60));
                $s = floor(($cle%(60*1000)/1000));
                $ms = floor($cle%1000);
            }elseif ($cle < 0) {
                $d = ceil($cle/1000/60/60/24);
                $h = ceil($cle%(24*60*60*1000)/1000/60/60);
                $m = ceil(($cle%(60*60*1000)/1000/60));
                $s = ceil(($cle%(60*1000)/1000));
                $ms = ceil($cle%1000);
            }else {
                $d = 0;
                $h = 0;
                $m = 0;
                $s = 0;
                $ms = 0;
            }
            $workflows->result( $query,
                // date('Y-m-d H:i:s.u',floor($query/1000)),
                $this->getMsecToMescdate($query),
                // '目标时间：'.date('Y-m-d H:i:s',$query/1000),
                '目标时间：'.$this->getMsecToMescdate($query),
                "当前时间差 $d 天 $h 小时 $m 分 $s 秒 $ms 毫秒",
                'icon.png',false);
            echo $workflows->toxml();
        }
        if ($this->isDateTime($query)) {
            $workflows->result( $query,
                strtotime($query)*1000,
                '目标时间戳(ms)：'.strtotime($query)*1000,
                '与当前时间戳差：'.(strtotime($query)-floor($now/1000)).'秒',
                'icon.png',false);
            echo $workflows->toxml();
        }
        exit;

    }

}
