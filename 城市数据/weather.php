<?php
$URLStyle = "http://flash.weather.com.cn/wmaps/xml/%s.xml";
$chinaURL = sprintf($URLStyle, "china");
$chinaStr = file_get_contents($chinaURL);
$chinaObj = simplexml_load_string($chinaStr);
$chinaObjLen = count($chinaObj->city);
$fp = fopen("a.json", "w+");
$result = [];
echo "chinaObjLen = " . $chinaObjLen . "\n";
for ($i = 0; $i < $chinaObjLen; $i++) {
//遍历省一级节点，共37个
    $result[$i] = [
        'title' => sprintf("%s", $chinaObj->city[$i]['quName']),
        'code' => sprintf("%s", $chinaObj->city[$i]['url']),
        'child' => [],
    ];
    $level1 = $chinaObj->city[$i]["pyName"];
    $shengjiURL = sprintf($URLStyle, $level1);
    $shengjiStr = file_get_contents($shengjiURL);
    $shengjiObj = simplexml_load_string($shengjiStr);
    $shengjiObjLen = count($shengjiObj->city);
    for ($j = 0; $j < $shengjiObjLen; $j++) {
        //遍历市一级节点
        $level2 = $shengjiObj->city[$j]["pyName"];
        $result[$i]['child'][$j] = [
            'title' => sprintf("%s", $shengjiObj->city[$j]["cityname"]),
            'code' => sprintf("%s", $shengjiObj->city[$j]['url']),
            'child' => [],
        ];
        if (!$result[$i]['code'] && sprintf("%s", $chinaObj->city[$i]['cityname']) == sprintf("%s",
                $shengjiObj->city[$j]["cityname"])) {
            $result[$i]['code'] = sprintf("%s", $shengjiObj->city[$j]['url']);
        }
        $shijiURL = sprintf($URLStyle, $level2);
        $shijiStr = file_get_contents($shijiURL);
        $shijiObj = simplexml_load_string($shijiStr);
        //直辖市和海南、台湾、钓鱼岛等没有县级节点
        if (!$shijiObj) {
            echo "WARNNING: not exsit next level node. - " . $level1 . "-" . $shijiURL . "\n";
            echo '  "' . $shengjiObj->city[$j]["cityname"] . '" => ';
            echo $shengjiObj->city[$j]["url"] . ",\n";
            continue;
        }
        $shijiObjLen = count($shijiObj->city);
        for ($k = 0; $k < $shijiObjLen; $k++) {
            //遍历县一级节点
            $result[$i]['child'][$j]['child'][$k] = [
                'title' => sprintf("%s", $shijiObj->city[$k]["cityname"]),
                'code' => sprintf("%s", $shijiObj->city[$k]['url']),
            ];
            $xianji_code = $shijiObj->city[$k]["url"];
            echo '  "' . $shijiObj->city[$k]["cityname"] . '" => ';
            echo $shijiObj->city[$k]["url"] . ",\n";
        }
    }
}
fwrite($fp, json_encode($result));
fclose($fp);
?>
