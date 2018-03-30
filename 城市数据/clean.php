<?php


$fp1=fopen("b.json","w");
$json = file_get_contents("a.json");

$cities = json_decode($json,true);

foreach ($cities as $i=>$provinces) {
	if(!$provinces['code']){
		unset($cities[$i]);
		continue;
	}
	foreach ($provinces['child'] as $j=>$districts) {
		if(empty($districts['child'])){
			$temp = $districts;
			unset($provinces['child'][$j]);
			$provinces['child'][$j]=$temp;
			continue;
		}
		foreach ($districts['child'] as $key => $district) {
			if(!$district['code']){
				unset($cities[$i]['child'][$j]['child'][$key]);
				continue;
			}
		}
	}
}

fwrite($fp1, json_encode(array_values($cities)));
fclose($fp1);
