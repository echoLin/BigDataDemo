<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('Td_idf.php');
$dir = '//Users//echo/Desktop//大三下//大数据//样例';
$csvFile = '/paperSample.csv';
$dataDir = '//nips//';
$resultFile = '/result.csv';

$tdidf = new Td_idf($dir, $csvFile, $dataDir, $resultFile);
$tdidf->readCSV(2);
?>