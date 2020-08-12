<?php

if($did = intval($_SERVER['QUERY_STRING'])){
	
getBaib('https://baibako.tv/download.php?id='.$did, 1);

}else{

header('Content-type: text/xml');


preg_match("#<tbody id=\"highlighted\">(.*?)</tbody>#si", getBaib('https://baibako.tv/browse.php'), $rss);

$rss = mb_convert_encoding($rss[1], 'UTF-8', 'Windows-1251');


$total = preg_match_all("#<tr(.*?)</tr>#si",$rss,$data);

if($total){

echo "<?xml version='1.0' encoding='UTF-8'?>
<rss version='2.0'>
<channel> 
<title>BaibaKo.TV</title>
<link>https://baibako.tv</link>
<language>ru</language>
";


for ($i=0; $i<$total; $i++){

preg_match("#download.php\?id=(.*?)\">#si",$data[1][$i],$id);
preg_match("#hit=1\">(.*?)</a>#si",$data[1][$i],$name);
preg_match("#<small>(.*?)</small>#si",$data[1][$i],$date);

$rd = [

	'id'   => intval($id[1]),
	'name' => trim($name[1]),
	'date' => date('r', strtotime(trim(str_replace(['Загружена: ',' в','января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'],['','','january','february','march','april','may','june','july','august','september','october','november','december'], $date[1])))),
	

];

echo "<item>
  <title><![CDATA[".$rd['name']."]]></title>
  <pubDate>".$rd['date']."</pubDate>
  <link>http://bt.test/baibako.tv.php?".$rd['id']."</link>
</item>
";

}


echo "</channel>
</rss>";

}

}

function getBaib($url, $pass=0)
{

global $did;

$c = "curl '".$url."' \
  -H 'Connection: keep-alive' \
  -H 'Cache-Control: max-age=0' \
  -H 'Upgrade-Insecure-Requests: 1' \
  -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36' \
  -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9' \
  -H 'Referer: https://baibako.tv/browse.php' \
  -H 'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7' \
  -H 'Cookie: uid=12345; pass=67891011121314151766464426789321' \
  --compressed \
  --insecure --connect-timeout 10 --max-time 10";

if($pass){

header('Content-Transfer-Encoding: binary');
header('Content-Disposition: attachment; filename="'.$did.'.torrent"');
header('Content-Type: application/x-bittorrent');

passthru($c);

}else{

exec($c, $result);

return implode("\n", $result);
}

}
