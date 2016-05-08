<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
readCSV();
function readCSV(){
    $dir = '//Users//echo/Desktop//大三下//大数据//样例';
    $csvFile = '/paperSample.csv';
    $dataDir = '//nips//';
    $resultFile = '/result.csv';
    $handle = fopen($dir.$csvFile,'r');
    //$handle = fopen($dir.$dataDir.'5677.txt', 'r');
    fgetcsv($handle, 1000, ',');
    while($data = fgetcsv($handle, ','))
        $paper[] = $data[0];
    fclose($handle);

    $txtCount = count($paper);
    $wordCount = 0;
    foreach($paper as $id){
        $temp = readPaper($dir, $dataDir, $id);
        //var_dump($temp);
        foreach($temp as $key=>$value){
            //var_dump($key , $value);
            if(isset($resultData[$key])){
                $resultData[$key][0]+=$value;
                $resultData[$key][1]++;
            }else{
                $resultData[$key] = array($value,1);
            }
            $wordCount += $value;
        }
    }
    //var_dump($resultData);

    $file = fopen($dir.$resultFile, 'w');
    foreach($resultData as $key => $value){
        fputcsv($file, array($key, ($value[0]/$wordCount)/log10($txtCount/$value[1])));
    }
    fclose($file);
}
function readPaper($dir, $dataDir, $id){
    if(!file_exists($dir.$dataDir.$id.'.txt')){
        return;
    }
    $str = file_get_contents($dir.$dataDir.$id.'.txt');
    //var_dump($str);
    if(file_exists($dir.$dataDir.$id.'.abs.txt')){
        $str .= file_get_contents($dir.$dataDir.$id.'.abs.txt');
    }
    //var_dump($str);
    // $str = 'e lunch � axiom says that if all the questions attempted by the worker are answered incorrectly then the payment must be zero we propose payment mechanism for the aforementioned setting incentive compatibility � plus no free lunch � and show that surprisingly this is the only possible mechanism we also show that additionally our mechanism makes the smallest possible payment to spammers among all possible incentive compatible mechanisms that may or may not satisfy the no free lunch axiom our payment mechanism takes multiplicative form the evaluation of the worker �s response to each ques';
    // echo $str;
    // echo '<br/><br/>';
    $str = trim($str);
    $str = strtolower($str);
    $str = preg_replace('/[(\xA1|\xA8|\xAE|\xAF|\xA6|\xB0|\xB1|\xEF|\xBD|\xCC)]/',' ', $str);
    $str = preg_replace('/[[:punct:]\s]/', ' ', $str);
    $str = preg_replace('/[[:digit:]\s]/', ' ', $str);
    //$str = preg_replace('/[a-z]/', '', $str);
    $str = preg_replace('/\s[a-z]{1}\s/', ' ', $str);
    // $arr = explode('natural', $str);
    // $arr = explode('no', $arr[1]);
    // $arr = $str;
    // var_dump($arr, strlen($arr));
    // for($i = 0; $i<strlen($arr); $i++)
    //     if(ord($arr[$i]) != 32)
    //         echo $arr[$i] . ' ' . ord($arr[$i]). ' %%% ';
    //var_dump($arr[0][2]);
    //var_dump(ord($arr[0][2]));
    $str = explode(' ', $str);
    $temp = array();
    foreach($str as $word){
        if(isset($temp[$word])){
            $temp[$word]++;
        }else{
            $temp[$word] = 1;
        }
    }
    //var_dump($temp);
    return $temp;
}
?>