<?php
$url = "https://restapi.amap.com/v3/weather/weatherInfo?key=c097f6546f3ff453f9c7bdd536cfdfa4&city="; //接口
$city = array("110000" => "北京", "310000" => "上海", "440100" => "广州", "440300" => "深圳");
$con=mysqli_connect("*","*","*","*");
// 检测连接
if (mysqli_connect_errno())
{
    echo "连接失败: " . mysqli_connect_error();
}

foreach ($city as $code => $name) {
    $data = json_decode(get_url($url . $code), true);
//    var_dump($data);
    if ($data["status"] == "1" && $data["infocode"] == "10000") {
        $up = $data["lives"][0];
        $sql = <<<EOF
update weathering
set weather       = '%s',
    temperature   = %d,
    winddirection = '%s',
    windpower     = '%s',
    humidity      = %d,
    reporttime    = '%s'
where adcode = '%s' ;
EOF;
        $sql = sprintf($sql, $up["weather"], $up["temperature"], $up["winddirection"], $up["windpower"], $up["humidity"], $up["reporttime"], $up["adcode"]);

//        var_dump($up);
        mysqli_query($con, $sql);
    }
}

mysqli_close($con);

function get_url($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);  //设置访问的url地址
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不输出内容
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}