<?php
class Td_idf{
    private $dir;
    private $csvFile;
    private $dataDir;
    private $resultFile;
    private $appendenceFile;
    private $uselessWords;
    function __construct($dir, $csvFile, $dataDir, $resultFile, $appendenceFile){
        $this->dir = $dir;
        $this->csvFile = $csvFile;
        $this->dataDir = $dataDir;
        $this->resultFile = $resultFile;
        $this->appendenceFile = $appendenceFile;
        $handle = fopen('uselessWords.txt', 'r');
        while(!feof($handle))
            $this->uselessWords[] = trim(fgets($handle));
        fclose($handle);
    }

    /**
     * 读取数据
     * @param  int $type 读取类型 1.全部 2.摘要
     * @return         
     */
    public function readCSV($type = 2){
        //1.读取论文的id
        $handle = fopen($this->dir.$this->csvFile,'r');
        fgetcsv($handle, 1000, ',');
        while($data = fgetcsv($handle, ','))
            $paper[] = $data[0];
        fclose($handle);

        //根据读取type读取每篇论文并记录返回的词组
        $txtCount = count($paper);
        $wordCount = 0;
        $appendence = array();
        foreach($paper as $id){
            $temp = $this->readPaper($id, $type);
            //var_dump($temp);
            //exit();
            foreach($temp as $key=>$value){
                //var_dump($key , $value);
                if(isset($resultData[$key])){
                    $resultData[$key][0]+=$value;
                    $resultData[$key][1]++;
                }else{
                    $resultData[$key] = array($value,1);
                }
                $appendence[$key][] = $id;
                $wordCount += $value;
            }
        }
        //var_dump($resultData);

        $file = fopen($this->dir.$this->resultFile, 'w');
        foreach($resultData as $key => $value){
            fputcsv($file, array($key, ($value[0]/$wordCount)/log10($txtCount/$value[1])));
        }
        fclose($file);
        $file = fopen($this->dir.$this->appendenceFile, 'w');
        $first = array('key');
        foreach($paper as $id){
            $first[] = $id;
        }
        fputcsv($file, $first);
        foreach($appendence as $key => $arr){
            $row = array($key);
            foreach($paper as $id){
                if(in_array($id, $arr))
                    $row[] = 1;
                else
                    $row[] = 0;
            }
            fputcsv($file, $row);
        }
        fclose($file);
    }

    /**
     * 读取论文
     * @param  int $id   论文ID
     * @param  int $type 1读取全部 2读取摘要
     * @return array       
     */
    private function readPaper($id, $type){
        if(!file_exists($this->dir.$this->dataDir.$id.'.txt')){
            return;
        }
        //读取摘要
        if(file_exists($this->dir.$this->dataDir.$id.'.abs.txt'))
            $str = file_get_contents($this->dir.$this->dataDir.$id.'.abs.txt');

        //读取正文
        if($type == 1 && file_exists($this->dir.$this->dataDir.$id.'.txt'))
            $str .= file_get_contents($this->dir.$this->dataDir.$id.'.txt');

        if(empty($str))
            return array();
        $str = $this->_clean($str);
        $str = explode(' ', $str);
        $temp = array();
        $count = count($str);
        for($i=0; $i<$count; $i++){
            $word = '';
            for($j=0; $j<3 && $i+$j<$count; $j++){
                $word .= ' ' . $str[$i+$j];
                $word = trim($word);
                if(isset($temp[$word]))
                    $temp[$word]++;
                else
                    $temp[$word] = 1;
            }
        }
        unset($temp['']);
        return $temp;
    }

    /**
     * 清理文本内容
     * @param  String $str 文本内容
     * @return String      清理后的文本内容
     */
    private function _clean($str){
        $str = trim($str);
        $str = strtolower($str);
        $str = preg_replace('/[(\xA1|\xA8|\xAE|\xAF|\xA6|\xB0|\xB1|\xEF|\xBD|\xCC)]/',' ', $str);
        $str = preg_replace('/[[:punct:]\s]/', ' ', $str);
        $str = preg_replace('/[[:digit:]\s]/', ' ', $str);
        $str = preg_replace('/\s[a-z]{1}\s/', ' ', $str);
        //去除停用词
        $i = 0;
        foreach($this->uselessWords as $words){
            if($i < 7)
                $str = preg_replace('/'.$words.'\b/', ' ', $str);
            else
                $str = preg_replace('/\b'.$words.'\b/', ' ', $str);
            $i++;
        }
        //var_dump($this->uselessWords);
        return $str;
    }
}
?>