<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title> RADIO RIVENDELL STATISTICS </title>
</head>

<body>
<?php
/*
**	Get shoutcast PLS file with all the relay server available for this station.
*/
$statPage="7";
$url="http://yp.shoutcast.com/sbin/tunein-station.pls?id=7017";
$contents = file($url);
$i=0;
foreach ($contents as $line) {
	if (stristr($line,'File'))
	{
		$IPAddress=explode('=',$line);
		$serverShoutcast[$i++] = trim($IPAddress[1]);
	}
}

//print_r($serverShoutcast); 

/*
**	for each IP address, we check the status page and extract the listeners
*/

$j=0;
$ecouteurs=array();
$timeout = "1";
$currentListeners=0;
$total=0;
foreach ($serverShoutcast as $key => $value) 
{
	$url=explode(':',$value);
	$ip=substr($url[1],2,strlen($url[1]));
	$port=$url[2];
		
	$fp = @fsockopen($ip,$port,$errno,$errstr,$timeout);
	if (!$fp) 
	{ 	
		$msg[$i] = "<span class=\"red\">ERROR [Connection refused / Server down]</span>";
	} 
	else
	{	
		fputs($fp, "GET /7.html HTTP/1.0\r\nUser-Agent: Mozilla\r\n\r\n");
		while (!feof($fp)) 
			{
			$info = fgets($fp);
			}
		$info = str_replace('<HTML><meta http-equiv="Pragma" content="no-cache"></head><body>', "", $info);
		$info = str_replace('</body></html>', "", $info);
		$stats = explode(',', $info);
	}
	$total = $total + $stats[3];
	$currentListeners = $currentListeners + $stats[0];
}

print '<h2>- RADIO RIVENDELL STATISTICS -</h2>';
print '<ul>Current listeners (128 Kbps stream) : <strong>'.$currentListeners.'</strong> / '.$total.' slots on '.$i.' servers.</ul>';
?>
</body>
</html>