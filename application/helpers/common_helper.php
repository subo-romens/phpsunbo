<?php
function ReturnUnlogin($value){
 	//$error=base64_encode(utf8_encode($value));
	$error=base64_encode($value);
//	$error=base64_encode($value);
	header("Content-Type:text/html;charset=UTF-8");
	echo header("Content-type:error=1;ERRORINFO=".$error);
	exit();
}
function ReturnErrorUnlogin($value){
	header("Content-Type:text/html;charset=UTF-8");
	echo header("Content-type:error=1;ERRORINFO=".$value);
	exit();
}
function returneror($code){
	switch ($code)
	{
		case "1000":
			return "访问商城出错";
			break;
		case "1001":
			return "获取用户信息失败01";
			break;
		case "1002":
			return "修改数据库用户密码失败";
			break;
		case "1003":
			return "插入短信数据库信息失败";
			break;
		default:
			return "不存在的错误编码";
			break;
	}

}

/*获取错误码*/
function wholelogtype($code){
	switch ($code)
	{
		case "0":
			return "ERRORMSG";
			break;
		case "1":
			return "SMS";
			break;
		default:
			return "不存在的错误编码";
			break;
	}
}
/*旧短信接口*/
function oldsend_SMS($mobile,$content,$mobileids='',$time='',$mid='')
{

	$data = array
	(
		'uid'=>olduid, //用户账号
		'pwd'=>md5(oldpwd.olduid), //MD5位32密码,密码和用户名拼接字符
		'mobile'=>$mobile, //号码
		'content'=>$content, //内容
		'mobileids'=>$mobileids,
		'time'=>$time, //定时发送
	);
	$re= oldpostSMS(oldsms_url,$data); //POST方式提交
	return $re;
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function _safe_replace($string) {
	$string = str_replace('%20','',$string);
	$string = str_replace('%27','',$string);
	$string = str_replace('%2527','',$string);
	$string = str_replace('*','',$string);
	$string = str_replace('"','&quot;',$string);
	$string = str_replace("'",'',$string);
	$string = str_replace('"','',$string);
	$string = str_replace(';','',$string);
	$string = str_replace('<','&lt;',$string);
	$string = str_replace('>','&gt;',$string);
	$string = str_replace("{",'',$string);
	$string = str_replace('}','',$string);
	$string = str_replace('\\','',$string);
	return $string;
}

function oldpostSMS($url,$data='')
{
	$port="";
	$post="";
	$row = parse_url($url);
	$host = $row['host'];
	//$port = $row['port'] ? $row['port']:80;
	$port = 80;
	$file = $row['path'];
	while (list($k,$v) = each($data))
	{
		$post .= rawurlencode($k)."=".rawurlencode($v)."&"; //转URL标准码
	}
	$post = substr( $post , 0 , -1 );
	$len = strlen($post);
	$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
	if (!$fp) {
		return "$errstr ($errno)\n";
	} else {
		$receive = '';
		$out = "POST $file HTTP/1.1\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Content-type: application/x-www-form-urlencoded\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Content-Length: $len\r\n\r\n";
		$out .= $post;
		fwrite($fp, $out);
		while (!feof($fp)) {
			$receive .= fgets($fp, 128);
		}
		fclose($fp);
		$receive = explode("\r\n\r\n",$receive);
		unset($receive[0]);
		return implode("",$receive);
	}
}