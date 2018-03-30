<?php

function load_json($file){
	$fp = fopen($file.".json","r");
	$result = $fp->read();
	return json_decode($result, true);
}

//第一个短数据，第二个长数据
function judge_equal($first,$second){
	if(strpos($second,$first)===false){
		return false;
	}
	return true;
}

$arrWeather = load_json("b");
$arrCity = load_json("city");

