<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

include_once (APPPATH . "core/Yjk_Controller.php");
class UnHandle extends Yjk_Controller {
	public function __construct() {
		parent::__imghandle ();
		$this->load->helper ( 'url' );
		$this->load->helper ( 'common_helper' );
	}
	
	// 登录首页，拆分登录信息，并访问真实访问信息3333332222
	public function index() {
		$type = $_POST ['QueryType'];
		$params = $_POST ['Params'];
		$array = json_decode ( $params, true );
		$this->$type ( $array );
	}
	
	/* 获取广告详情 */
	public function GetAdInfoBy($array) {
		$guid = $array ['GUID'];
		$page = $array ['PAGE']; // 第几页 第一页为0
		$count = $array ['COUNT']; // 多少条
		$data = array (
				'guid' => $guid,
				'page' => $page,
				'count' => $count,
				'orgguid' => $this->GetUserOrgguid () 
		);
		$url = weburlweishop . 'API/GetHomeAdInfoList';
		$resul = $this->GetRemotingHandlerInfo ( $url, $data );
		echo $resul;
		die ();
	}
	
	/**
	 * 用户注册
	 * modified by xueji at 2015-9-29
	 *
	 * @param unknown $array        	
	 */
	public function Register($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		$return_data = array (
				"success" => "no",
				"errorCode" => "0001",
				"errorMsg" => "用户参数错误！",
				"otherMsg" => "" 
		);
		
		if (array_key_exists ( 'ORGGUID', $array )) { // 判断组织结构ID是否存在
			$ORGGUID = $array ['ORGGUID'];
			$phone_number = $array ['PHONENUMBER'];
			$verifycode = $array ['VERIFYCODE'];
			
			
			
			// 这里校验验证码
			// $this->db->where('smsCode',$verifycode);
			
			
		}
		echo json_encode ( $return_data );
		die ();
	}
	
	/* 检测手机号 */
	public function CheckPhoneNumber($array) {
		$orgguid = $array ['ORGGUID'];
		$phone = $array ['PHONENUMBER'];
		$data = array (
				'orgguid' => $orgguid,
				'phone' => $phone 
		);
		$url = weburlweishop . 'API/CheckPhoneNumber';
		$resul = $this->GetRemotingHandlerInfo ( $url, $data );
		$resulsms = '';
		$res = json_decode ( $resul, true );
		if (array_key_exists ( 'ERROR', $array )) {
			// ReturnUnlogin("注册失败，请重试");
			ReturnUnlogin ( "注册失败，请重试" );
			exit ();
		}
				
		$code = rand ( 100000, 999999 );
		$name = $res ['GUID'];
		$pwd = md5 ( md5 ( $code ) . '0' );
		$result_arr = '';
		$resuhx = '';
		if ($res ['ISUPLOADHX'] == '0') {
			$sql = "select * from orgdatabase where orgguid='" . $orgguid . "'";
			$resorg = $this->db->query ( $sql )->result_array ();
			if ($resorg && count ( $resorg ) > 0) {
				$session = array (
						'orgguid' => $orgguid,
						'client_id' => $resorg [0] ['client_id'],
						'client_secret' => $resorg [0] ['client_secret'],
						'appkey' => $resorg [0] ['appkey'] 
				);
				$this->session->set_userdata ( $session );
			}
			$resuhx = $this->CreateUser ( $name, $pwd );
		}
		// if($resuhx==''&&$res['ISVALIDITY']=='1'){
		// $yzmContent=smsfirst.$code;
		// $resulsms=$this->newsmssend($phone, $yzmContent);
		// $this->CreateWholeLog('1', $yzmContent, '0', $phone);
		// }
		$resuchangepwd = '';
		// if($res['ISVALIDITY']=='1'||$res['ISUPLOADHX']=='0'){
		// $resuchangepwd=$this->ChangUserPwd($name,$pwd);
		// }
		$return_data = array (
				'ISVALIDITY' => $res ['ISVALIDITY'],
				'NAME' => $res ['GUID'],
				'USERNAME' => $res ['NAME'],
				'SMS' => $resulsms,
				'HX' => $resuhx,
				'USERGUID' => ""
		);
		//要健康注册与查询
		$this->db->where ( 'mobile', $phone );
		$this->db->select ( 'ID' );
		$result = $this->db->get ( 'member' )->result_array ();
			
		if (count ( $result ) > 0) { // 手机号已经注册过
			$return_data ["USERGUID"] = $result [0] ['ID'];
		} else {
			$data = array (
					'ID' => uniqid (),
					'mobile' => $phone,
					'memberName' => $phone,
					'memberStatus' => 1
			);
			$this->db->insert ( 'member', $data );
			$return_data ["USERGUID"] = $data ['ID'];
		}
		//结束		
		
		echo json_encode ( $return_data );
		die ();
	}
	public function UserLogin($array) {
		$phone = $array ['PHONE'];
		$pwd = $array ['PWD'];
		$orgguid = $array ['ORGGUID'];
		$data = array (
				'phone' => $phone,
				'pwd' => $pwd,
				'orgguid' => $orgguid 
		);
		$url = weburlweishop . 'API/CheckUserPhonePwdBy';
		$resul = $this->GetRemotingHandlerInfo ( $url, $data );
		echo $resul;
		die ();
	}
	// 发送短信设置初始密码
	public function sendsms($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		$phone = $array ['PHONENUMBER'];
		$name = "";
		if (array_key_exists ( 'USERNAME', $array )) {
			$name = $array ['USERNAME'];
		} else {
			$name = $phone;
		}
		$orgguid = $array ['ORGGUID'];
		$sql = "select * from orgdatabase where orgguid='" . $orgguid . "'";
		$resorg = $this->db->query ( $sql )->result_array ();
		if ($resorg && count ( $resorg ) > 0) {
			$session = array (
					'orgguid' => $orgguid,
					'client_id' => $resorg [0] ['client_id'],
					'client_secret' => $resorg [0] ['client_secret'],
					'appkey' => $resorg [0] ['appkey'] 
			);
			$this->session->set_userdata ( $session );
		}
		$flag = $array ['FLAG'];
		$code = rand ( 100000, 999999 );
		$yzmContent = '';
		if ($flag == '0') {
			$yzmContent = smsfirst . $code;
		} else {
			$yzmContent = smscontent . $code;
		}
		$pwd = md5 ( md5 ( $code ) . '0' );
		
		$easouresult = '';
		$resulpwd = "";
		$result_arr = "";
		for($i = 0; $i < 3; $i ++) {
			$resulpwd = $this->Changepwdphone ( $name, $pwd );
			$result_arr = json_decode ( $resulpwd, true );
			if (! isset ( $result_arr ['error'] )) {
				break;
			}
		}
		if (isset ( $result_arr ['error'] )) {
			$easouresult = 'error';
			$array = array (
					'SMSRESULT' => '1000' 
			);
			echo json_encode ( $array );
			die ();
		}
		$this->ChangUserPwd ( $name, $pwd );
		$resul = $this->newsmssend ( $phone, $yzmContent );
		if ($resul && $resul == '1') {
			$createtime = $this->returnnowtime ();
			$res = $this->CreateWholeLog ( '1', $yzmContent, '0', $phone );
			$array = array (
					'SMSRESULT' => '1' 
			);
			echo json_encode ( $array );
			die ();
		} else {
			$this->CreateWholeLog ( '0', 'sms1003', '1', $phone );
			$array = array (
					'SMSRESULT' => '1003' 
			);
			echo json_encode ( $array );
			die ();
		}
	}
	
	// 新短信中心发送代码
	public function newsmssend($phone, $yzmContent) {
		$sn = smssn;
		$pwd = smspwd;
		$sign = smssign;
		header ( "Content-Type: text/html; charset=UTF-8" );
		
		$flag = 0;
		$params = '';
		// 要post的数据
		$argv = array (
				'sn' => $sn, // //替换成您自己的序列号
				'pwd' => strtoupper ( md5 ( $sn . $pwd ) ), // 此处密码需要加密 加密方式为 md5(sn+password) 32位大写
				'mobile' => $phone, // 手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
				'content' => $yzmContent . $sign, // iconv( "GB2312", "gb2312//IGNORE" ,'您好测试短信[XXX公司]'),//'您好测试,短信测试[签名]',//短信内容
				'ext' => '',
				'stime' => '', // 定时时间 格式为2011-6-29 11:09:21
				'msgfmt' => '',
				'rrid' => '' 
		);
		// 构造要post的字符串
		foreach ( $argv as $key => $value ) {
			if ($flag != 0) {
				$params .= "&";
				$flag = 1;
			}
			$params .= $key . "=";
			$params .= urlencode ( $value ); // urlencode($value);
			$flag = 1;
		}
		$length = strlen ( $params );
		// 创建socket连接
		$fp = fsockopen ( "sdk.entinfo.cn", 8061, $errno, $errstr, 10 ) or exit ( $errstr . "--->" . $errno );
		// 构造post请求的头
		$header = "POST /webservice.asmx/mdsmssend HTTP/1.1\r\n";
		$header .= "Host:sdk.entinfo.cn\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . $length . "\r\n";
		$header .= "Connection: Close\r\n\r\n";
		// 添加post的字符串
		$header .= $params . "\r\n";
		// 发送post的数据
		// echo $header;
		// exit;
		fputs ( $fp, $header );
		$inheader = 1;
		while ( ! feof ( $fp ) ) {
			$line = fgets ( $fp, 1024 ); // 去除请求包的头只显示页面的返回数据
			if ($inheader && ($line == "\n" || $line == "\r\n")) {
				$inheader = 0;
			}
			if ($inheader == 0) {
				// echo $line;
			}
		}
		// <string xmlns="http://tempuri.org/">-5</string>
		$line = str_replace ( "<string xmlns=\"http://tempuri.org/\">", "", $line );
		$line = str_replace ( "</string>", "", $line );
		$result = explode ( "-", $line );
		// return $result;
		// echo $line."-------------";
		if (count ( $result ) > 1) {
			// echo '发送失败返回值为:'.$line.'。请查看webservice返回值对照表';
			return 0;
		} else {
			return 1;
		}
		return 0;
	}
	
	/* 获取首页配置信息 */
	public function GetHomeConfig($array) {
		$where = '';
		$tabtable = '';
		$userguid = '';
		if (array_key_exists ( 'USERGUID', $array )) {
			$userguid = $array ['USERGUID'];
			if ($userguid && $userguid != '') {
				$sqltab = "SELECT LABELGUID FROM userlabels where USERGUID='" . $userguid . "'";
				$tabtable = $this->db->query ( $sqltab )->result_array ();
				if ($tabtable && count ( $tabtable ) > 0) {
					foreach ( $tabtable as $key => $value ) {
						if ($tabtable [$key] ['LABELGUID'] && $where == '') {
							$where .= " b.LABELGUID='" . $tabtable [$key] ['LABELGUID'] . "'";
						} else if ($tabtable [$key] ['LABELGUID'] && $where != '') {
							$where .= " or  b.LABELGUID='" . $tabtable [$key] ['LABELGUID'] . "'";
						}
					}
				}
			}
		}
		$sql = "select distinct a.GUID,a.SORTINDEX,a.`KEY`,a.VALUE,a.TYPE,case when a.TYPE='0' then '轮询广告' when a.TYPE='1'
		 then '快捷功能' when a.TYPE='2' then '促销广告' when a.TYPE='3' then '商品推荐' when a.TYPE='4' then '健康资讯' end TYPENAME
		,a.STATE,case when a.STATE='0' then '正常' when a.STATE='1' then '作废' end STATENAME
		,a.CREATEDATE  from homeconfig a left join homeconfiglabels b on a.`KEY`=b.HOMEGUID";
		if ($where) {
			$sql = $sql . " where b.LABELGUID='00000000' or " . $where;
		} else {
			$sql = $sql . " where b.LABELGUID='00000000' ";
		}
		$sql = $sql . ' order by a.SORTINDEX';
		$data = $this->db->query ( $sql )->result_array ();
		echo json_encode ( $data );
		exit ();
	}
	
	// 获取商品分类
	public function GetGoodsClass($array) {
		$id = 'goodssortclasslist';
		if ($resul = $this->getcache ( $id )) {
			echo $resul;
			die ();
		} else {
			$data = array (
					'orgguid' => $this->GetUserOrgguid () 
			);
			$url = weburlweishop . 'API/getGoodsSort';
			$resul = $this->GetRemotingHandlerInfo ( $url, $data );
			$this->setcache ( $id, $resul, cachetimecontinue );
			echo $resul;
			die ();
		}
	}
	
	// 获取商品列表
	public function GetGoodsList($array) {
		// $guid=$array['GUID'];//商品分类GUID
		// $id='goodslist'.$guid;
		// if($resul=$this->getcache($id)){
		// echo $resul;
		// die;
		// }else{
		// $page=$array['PAGE'];//第几页 第一页为0
		// $count=$array['COUNT'];//多少条
		// $data=array(
		// 'page'=>$page,
		// 'count'=>$count,
		// 'orgguid'=>$this->GetUserOrgguid(),
		// 'guid'=>$guid
		// );
		// $url=weburlweishop.'API/getGoodsList';
		// $resul=$this->GetRemotingHandlerInfo($url,$data);
		// $this->setcache($id, $resul, cachetimecontinue);
		// echo $resul;die;
		// }
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		if (array_key_exists ( 'GUID', $array )) {
			$guid = $array ['GUID'];
			
			$page = 0;
			if (array_key_exists ( 'PAGE', $array )) {
				$page = $array ['PAGE'];
			}
			
			$count = 10;
			if (array_key_exists ( 'COUNT', $array )) {
				$count = $array ['COUNT'];
			}
			
			$from = $page * $count;
			// 拼接sql
			$sql = "";
			$sql .= "SELECT                                                                   ";
			$sql .= "  bb.NAME MEDICINENAME,                                                  ";
			$sql .= "  bb.SPEC MEDICINESPEC,                                                  ";
			$sql .= "  bb.SMALLURL PICSMALL,                                                  ";
			$sql .= "  bb.BIGURL PICBIG,                                                      ";
			$sql .= "  bb.ID MERCHANDISEID,                                                   ";
			$sql .= "  c.ID SHOPID,                                                           ";
			$sql .= "  c.pharmacyName SHOPNAME,                                               ";
			$sql .= "  (case when c.showprice=1 then bb.PRICE when c.showprice=0 then '' end) PRICE,";
			$sql .= "  (case when c.showprice=1 then bb.MEMPRICE when c.showprice=0 then '' end) MEMBERPRICE  ";
			$sql .= "FROM (SELECT                                                             ";
			$sql .= "  aa.NAME,                                                               ";
			$sql .= "  aa.SPEC,                                                               ";
			$sql .= "  aa.SMALLURL,                                                           ";
			$sql .= "  aa.BIGURL,                                                             ";
			$sql .= "  b.ID,                                                                  ";
			$sql .= "  b.`pharmacyId` SHOPID,                                                 ";
			$sql .= "  CONVERT(b.price/100,DECIMAL(10,2)) PRICE,                              ";
			$sql .= "  CONVERT(b.`price`*b.`merchDiscount`/100,DECIMAL(10,2)) MEMPRICE        ";
			$sql .= "FROM                                                                     ";
			$sql .= "  (SELECT                                                                ";
			$sql .= "    a.ID,                                                                ";
			$sql .= "    a.`medicineName` NAME,                                               ";
			$sql .= "    a.`field` SPEC,                                                      ";
			$sql .= "    a.`picSmallURL` SMALLURL,                                            ";
			$sql .= "    a.`picBigURL` BIGURL                                                 ";
			$sql .= "  FROM                                                                   ";
			$sql .= "    medicine a                                                           ";
			$sql .= "  WHERE a.`medKindId` = '$guid') aa       								";
			$sql .= "  LEFT JOIN merchandise b                                                ";
			$sql .= "    ON aa.ID = b.medicineId ) bb LEFT JOIN pharmacy c ON bb.SHOPID = c.ID";
			$sql .= " LIMIT $from,$count 														";
			
			$return_data = $this->db->query ( $sql )->result_array ();
			echo json_encode ( $return_data );
			die ();
		} else {
			echo json_encode ( array () );
			die ();
		}
	}
	
	// 获取咨询信息
	public function GetHealthInfoList($array) {
		$time = $array ['TIME'];
		$time = 1436514695;
		$id = 'weixinhealth' . $time;
		if ($resul = $this->getcache ( $id )) {
			echo $resul;
			die ();
		} else {
			$url = webweixinurl;
			$token = 'bqwyrl1435734590';
			$wecha_id = '123';
			$urlall = webweixinurl . '&token=' . $token . '&wecha_id=' . $wecha_id . '&time=' . $time;
			$result = $this->GetHandlerInfoBYGET ( $urlall );
			$this->setcache ( $id, $result, cachetimecontinue );
			echo $result;
			die ();
		}
	}
	
	// 获取附近药店
	public function GetNearbyShops($array) {
		// if(!$array){
		// ReturnUnlogin("非法参数");
		// exit();
		// }
		// $page=$array['PAGE'];//第几页 第一页为0
		// $count=$array['COUNT'];//多少条
		// $key=$array['KEY'];//关键字
		// $lng=$array['LONG'];//经度
		// $lat=$array['LAT'];//纬度
		// $data=array(
		// 'orgguid'=>$this->GetUserOrgguid(),
		// 'page'=>$page,
		// 'count'=>$count,
		// 'key'=>$key,
		// 'lng'=>$lng,
		// 'lat'=>$lat
		// );
		// $url=weburlweishop.'API/getBranch';
		// $resul=$this->GetRemotingHandlerInfo($url,$data);
		// // if(array_key_exists ('ERROR', json_decode($resul) )){
		// // echo $resul;
		// // }
		// echo $resul;die;
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		$key = $array ['KEY'];
		$long = $array ['LONG'];
		$lat = $array ['LAT'];
		$page = $array ['PAGE'];
		$count = $array ['COUNT'];
		
		// 组装条件
		$from = $page * $count;
		$sql = "SELECT a.ID ,a.pharmacyName SHOPNAME,a.chainPharmacy CHAINPHARMACY,a.upPharmacyId PID,a.address ADDRESS,      ";
		$sql .= "a.contactPhone PHONE,a.permissionNo SHOPNO,a.latitude LAT,a.longitude LON ,								  ";
		$sql .= " CONVERT( ACOS( SIN(($lat * 3.1415) / 180) * SIN((latitude * 3.1415) / 180) + COS(($lat * 3.1415) / 180) *     ";
		$sql .= " COS((latitude * 3.1415) / 180) * COS(($long * 3.1415) / 180 - (longitude * 3.1415) / 180)) * 6378.137         ";
		$sql .= "	,DECIMAL (15, 3) ) DISTANCE	FROM pharmacy a  WHERE a.status =1 												  ";
		if (! empty ( $key )) {
			$sql .= " AND a.pharmacyName LIKE '%$key%' 																		  ";
		}
		$sql .= "ORDER BY ACOS( SIN(($lat * 3.1415) / 180) * SIN((latitude * 3.1415) / 180) + COS(($lat * 3.1415) / 180) *      ";
		$sql .= "COS((latitude * 3.1415) / 180) * COS(($long * 3.1415) / 180 - (longitude * 3.1415) / 180)) * 6378.137          ";
		$sql .= "ASC  LIMIT $from, $count                                                                                       ";
		
		// 查询数据并返回
		$return_data = $this->db->query ( $sql )->result_array ();
		
		echo json_encode ( $return_data );
		die ();
	}
	// 搜索疾病列表
	public function GetSearchDisease($array) {
		
		// echo json_encode($array);die;
		// $key=trim($array['KEY']);//搜索关键字
		// if(!$array){
		// ReturnUnlogin("非法参数");
		// exit();
		// }
		// $id='goodssearchdisease'.$key;
		// if($resul=$this->getcache($id)){
		// echo $resul;
		// exit();
		// }else{
		// $sql="select ID,diseaseName DISEASENAME,sortnumber SORTNUMBER,status STATUS,description DESCRIPTION,ifnull(created,0) CREATED,ifnull(updated,0) UPDATED from disease where diseaseName like '%".$key."%'";
		// $data=$this->db->query($sql)->result_array();
		// if($data&&count($data)>0){
		// $this->setcache($id, json_encode($data), cachetimecontinue);
		// }
		// echo json_encode($data);
		// exit();
		// }
		
		// 王烁修改
		if (array_key_exists ( 'KEY', $array )) {
			
			$key = $array ['KEY']; // 关键字
			$sql = "SELECT                          ";
			$sql .= "  a.ID DISEASEID,               ";
			$sql .= "  a.diseaseName DISEASENAME,    ";
			$sql .= "  a.description DESCRIPTION,    ";
			$sql .= "  FROM_UNIXTIME(   			   ";
			$sql .= "  a.created) CREATED,           ";
			$sql .= "  FROM_UNIXTIME(   			   ";
			$sql .= "  a.updated) UPDATED            ";
			$sql .= "FROM                            ";
			$sql .= "  disease a                     ";
			$sql .= "WHERE a.status = 1              ";
			$sql .= "  AND a.diseaseName LIKE '%$key%'  ";
		}
		
		$result_data = $this->db->query ( $sql )->result_array ();
		echo json_encode ( $result_data );
		die ();
	}
	
	// 搜索药品列表
	public function GetSearchDrug($array) {
		// echo json_encode($array);die;
		// $guid=trim($array['KEY']);//搜索关键字
		// $this->Savesearchlog($guid, '');
		// $id='goodssearch'.$guid;
		// if($resul=$this->getcache($id)){
		// echo $resul;
		// die;
		// }else{
		// $page=$array['PAGE'];//第几页 第一页为0
		// $count=$array['COUNT'];//多少条
		// $data=array(
		// 'page'=>$page,
		// 'count'=>$count,
		// 'orgguid'=>$this->GetUserOrgguid(),
		// 'key'=>$guid
		// );
		// $url=weburlweishop.'API/GetSearchDrug';
		// $resul=$this->GetRemotingHandlerInfo($url,$data);
		// $this->setcache($id, $resul, cachetimecontinue);
		
		// 王烁改
		if (array_key_exists ( 'KEY', $array )) {
			$key = $array ['KEY']; // 关键字
			$page = $array ['PAGE']; // 页数
			$count = $array ['COUNT']; // 每页数量
			$from = $page * $count; // 计算起始查询条数
			                        
			// 拼接字符串
			$sql = "SELECT                                            ";
			$sql .= "  aa.MEDICINENAME,                                ";
			$sql .= "  aa.MEDICINESPEC,                                ";
			$sql .= "  aa.PICBIG,                                      ";
			$sql .= "  aa.PICSMALL,                                    ";
			$sql .= "  aa.MERCHANDISEID,                               ";
			$sql .= "  (case when c.showprice=1 then aa.PRICE when c.showprice=0 then '' end) PRICE,";
			$sql .= "  (case when c.showprice=1 then aa.MEMBERPRICE when c.showprice=0 then '' end) MEMBERPRICE,";
			$sql .= "  c.ID SHOPID,                                    ";
			$sql .= "  c.pharmacyName SHOPNAME                         ";
			$sql .= "FROM                                              ";
			$sql .= "  (SELECT                                         ";
			$sql .= "    a.medicineName MEDICINENAME,                  ";
			$sql .= "    a.field MEDICINESPEC,                         ";
			$sql .= "    a.picBigURL PICBIG,                           ";
			$sql .= "    a.picSmallURL PICSMALL,                       ";
			$sql .= "    b.ID MERCHANDISEID,                           ";
			$sql .= "    CONVERT(b.price / 100, DECIMAL (15, 2)) PRICE,";
			$sql .= "    CONVERT(                                      ";
			$sql .= "      b.price * b.merchDiscount / 100,            ";
			$sql .= "      DECIMAL (15, 2)                             ";
			$sql .= "    ) MEMBERPRICE,                                ";
			$sql .= "    b.pharmacyId                                  ";
			$sql .= "  FROM                                            ";
			$sql .= "    medicine a                                    ";
			$sql .= "    LEFT JOIN merchandise b                       ";
			$sql .= "      ON a.ID = b.medicineId                      ";
			$sql .= "  WHERE a.medicineName LIKE '%$key%') aa          ";
			$sql .= "  LEFT JOIN pharmacy c                            ";
			$sql .= "    ON aa.pharmacyId = c.ID                       ";
			$sql .= "  LIMIT $from ,$count                             ";
			
			$result_data = $this->db->query ( $sql )->result_array ();
			echo json_encode ( $result_data );
			die ();
		} else {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		
		echo json_encode ( $result );
		die ();
	}
	
	// 根据疾病名称获取商品
	public function GetListByDisease($array) {
		// $guid=$array['GUID'];//疾病guid
		// $id='drugbydisease'.$guid;
		// if($resul=$this->getcache($id)){
		// echo $resul;
		// die;
		// }else{
		// $sql=" select ifnull(WSHOPGOODSGUID,'')GUID, ID,barCode BARCODE,medicineName MEDICINENAME,advicePrice ADVICEPRICE,status STATUS,ifnull(pzwh,'')PZWH from medicine where diseaseguid='".$guid."'";
		// $data=$this->db->query($sql)->result_array();
		// if($data&&count($data)>0){
		// $this->setcache($id, json_encode($data), cachetimecontinue);
		// }
		// echo json_encode($data);
		// exit();
		// }
		if (array_key_exists ( 'GUID', $array )) {
			$guid = $array ['GUID']; // 商品guid
			                         // 拼接sql
			$sql = "SELECT                              ";
			$sql .= "  bb.MEDICINENAME,                  ";
			$sql .= "  bb.MEDICINESPEC,                  ";
			$sql .= "  bb.PICBIG,                        ";
			$sql .= "  bb.PICSMALL,                      ";
			$sql .= "  bb.MERCHANDISEID,                 ";
			$sql .= "  d.ID SHOPID,                      ";
			$sql .= "  d.pharmacyName SHOPNAME           ";
			$sql .= "FROM                                ";
			$sql .= "  (SELECT                           ";
			$sql .= "    aa.MEDICINENAME,                ";
			$sql .= "    aa.MEDICINESPEC,                ";
			$sql .= "    aa.PICBIG,                      ";
			$sql .= "    aa.PICSMALL,                    ";
			$sql .= "    c.ID MERCHANDISEID,             ";
			$sql .= "    c.pharmacyId                    ";
			$sql .= "  FROM                              ";
			$sql .= "    (SELECT                         ";
			$sql .= "      b.ID,                         ";
			$sql .= "      b.medicineName MEDICINENAME,  ";
			$sql .= "      b.field MEDICINESPEC,         ";
			$sql .= "      b.picBigURL PICBIG,           ";
			$sql .= "      b.picSmallURL PICSMALL        ";
			$sql .= "    FROM                            ";
			$sql .= "      medicine b                    ";
			$sql .= "    WHERE EXISTS                    ";
			$sql .= "      (SELECT                       ";
			$sql .= "        1                           ";
			$sql .= "      FROM                          ";
			$sql .= "        medicine_disease a          ";
			$sql .= "      WHERE a.diseaseId = '$guid'   ";
			$sql .= "        AND a.medicineId = b.id)) aa";
			$sql .= "    LEFT JOIN merchandise c         ";
			$sql .= "      ON aa.ID = c.medicineId) bb   ";
			$sql .= "  LEFT JOIN pharmacy d              ";
			$sql .= "    ON bb.pharmacyId = d.ID         ";
			
			$result_data = $this->db->query ( $sql )->result_array ();
			echo json_encode ( $result_data );
			die ();
		} else {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
	}
	public function Savesearchlog($key, $id) {
		date_default_timezone_set ( 'PRC' );
		$createtime = date ( 'Y-m-d H:i:s', time () );
		$guid = uniqid () . time ();
		$params = array (
				'ID' => $guid,
				'memberId' => $id,
				'keyword' => $key,
				'createDate' => $createtime 
		);
		$this->load->model ( 'searchlog_model' );
		$result = $this->searchlog_model->insert ( $params );
	}
	
	/**
	 * 王烁
	 * 获取省市县信息
	 * 当PARENTGUID为空时返回所有省份列表
	 *
	 * @param unknown $array        	
	 */
	public function GetAddressDetail($array) {
		// $url=weburlweishop.'API/GetAddressLists';
		// $data=array();
		// $resul=$this->GetRemotingHandlerInfo($url,$data);
		// echo $resul;die;
		$sql = " SELECT a.ID GUID,a.districtName NAME,a.districtLevel LEVEL " . " FROM district a WHERE a.districtLevel =2 ORDER BY a.sortnumber ASC";
		if (array_key_exists ( 'PARENTGUID', $array )) {
			$pid = $array ['PARENTGUID'];
			if (! empty ( $array ['PARENTGUID'] )) { // 上级为空则查询全部省份
				$sql = " SELECT a.ID,a.districtName NAME,a.districtLevel LEVEL " . " FROM district a WHERE a.upDistrictId ='" . $pid . "' ORDER BY a.sortnumber ASC";
			}
		}
		$result = $this->db->query ( $sql )->result_array ();
		echo json_encode ( $result );
		die ();
	}
	
	/**
	 * 王烁
	 * 获取省市县信息
	 * 当PARENTGUID为空时返回所有省份列表
	 *
	 * @param unknown $array        	
	 */
	public function GetAllDistrict() {
		$sql = " SELECT a.ID GUID, ifnull(a.upDistrictId,'') PAEENTGUID, a.districtName NAME,a.districtLevel LEVEL " . " FROM district a  ORDER BY a.districtLevel,a.sortnumber ASC";
		$result = $this->db->query ( $sql )->result_array ();
		echo json_encode ( $result );
		die ();
	}
	
	/**
	 * 修改密码，暂时不做
	 *
	 * @param unknown $array
	 *        	added by wangshuo at 2015-9-23
	 */
	// public function Changepwd($array){
	// if (!$array) {
	// ReturnUnlogin ( "非法参数" );
	// exit ();
	// }
	// $return_data = array (
	// "success" => "no",
	// "errorCode" => "0001",
	// "errorMsg" => "用户参数错误！",
	// "otherMsg" => ""
	// );
	
	// if(array_key_exists('ORGGUID', $array)){ //判断组织结构ID是否存在
	// $json_data = $array['JSONDATA'];
	// $data_type = gettype($json_data);
	// if($data_type=='string'){
	// $json_data =json_decode($json_data,true);
	// }
	// $old_pwd = $json_data;
	// }
	// }
	
	/**
	 * 获取药品分类
	 *
	 * @param unknown $array        	
	 */
	public function GetMedicineKind($array) {
		$sql = " SELECT  a.ID ,a.medKindName NAME,ifnull(a.upKindId,'') PID FROM medKind a ORDER BY a.sortnumber";
		$return_data = $this->db->query ( $sql )->result_array ();
		echo json_encode ( $return_data );
		die ();
	}
	
	/**
	 * 获取商品详情
	 *
	 * @param unknown $array        	
	 */
	public function GetGoodsInfo($array) {
		// $guid = '';
		// $barcode = '';
		// $pzwh = ''; // 商品国药准字号
		// $data;
		// // $guid='C1256F37-51FB-4702-AACB-705BF1A6BFB4';//商品guid
		// // $pzwh='Z10880003';//商品国药准字号
		// if (array_key_exists ( 'GUID', $array )) {
		// $guid = $array ['GUID']; // 商品guid
		// $data = array (
		// 'guid' => $guid,
		// 'pzwh' => $pzwh,
		// 'orgguid' => $this->GetUserOrgguid ()
		// );
		// } else if (array_key_exists ( 'BARCODE', $array )) {
		// $barcode = $array ['BARCODE'];
		// $data = array (
		// 'barcode' => $barcode,
		// 'pzwh' => $pzwh,
		// 'orgguid' => $this->GetUserOrgguid ()
		// );
		// } else {
		// ReturnUnlogin ( "非法参数" );
		// exit ();
		// }
		// $url = weburlweishop . 'API/GetGoodInfo';
		// $resul = $this->GetRemotingHandlerInfo ( $url, $data );
		// echo $resul;
		// die ();
		$guid = ""; // 商品guid
		$barcode = ""; // 商品条形码
		if (array_key_exists ( 'GUID', $array )) {
			$guid = $array ['GUID']; // 商品guid
		} else if (array_key_exists ( 'BARCODE', $array )) { // 二维码
			$barcode = $array ['BARCODE'];
		} else {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		
		$sql = "SELECT                                                ";
		$sql .= "  aa.GUID,                                            ";
		$sql .= "  aa.GOODSSORTGUID,                                   ";
		$sql .= "  aa.NAME,                                            ";
		$sql .= "  aa.USERPRICE,                                       ";
		$sql .= "  aa.MARKETPRICE,                                     ";
		$sql .= "  aa.DETAILDESCRIPTION,                               ";
		$sql .= "  aa.SHORTDESCRIPTION,                                ";
		$sql .= "  aa.SPEC,                                            ";
		$sql .= "  aa.PZWH,                                            ";
		$sql .= "  aa.URL,             			                       ";
		$sql .= "  aa.SHOPID, aa.SHOPNAME, 		                       ";
		$sql .= "  aa.STORECOUNT, aa.TOTLESALEDCOUNT, aa.ASSESSCOUNT,  ";
		$sql .= "  c.manufactureName CD                                ";
		$sql .= "FROM                                                  ";
		$sql .= "  (SELECT                                             ";
		$sql .= "    ifnull(a.STORECOUNT,0) STORECOUNT,                ";
		$sql .= "    ifnull(a.TOTLESALEDCOUNT,0) TOTLESALEDCOUNT,      ";
		$sql .= "    ifnull(a.ASSESSCOUNT,0) ASSESSCOUNT,              ";
		$sql .= "    a.ID GUID,'' GOODSSORTGUID,                       ";
		$sql .= "    b.medicineName NAME,                              ";
		$sql .= "    CONVERT(a.price / 100, DECIMAL (15, 2)) USERPRICE,";
		$sql .= "    CONVERT(                                          ";
		$sql .= "      a.price * a.merchDiscount / 100,                ";
		$sql .= "      DECIMAL (15, 2)                                 ";
		$sql .= "    ) MARKETPRICE,                                    ";
		$sql .= "    b.description SHORTDESCRIPTION,                   ";
		$sql .= "    b.medDescription DETAILDESCRIPTION,               ";
		$sql .= "    b.field SPEC,                                     ";
		$sql .= "    b.pzwh PZWH,                                      ";
		$sql .= "    b.manufacturerId,                                 ";
		$sql .= "    b.picBigURL URL,                                   ";
		$sql .= "    p.id SHOPID, p.pharmacyname  SHOPNAME, p.address SHOPADDRESS   ";
		$sql .= "  FROM                                                ";
		$sql .= "    merchandise a,medicine b, pharmacy p              ";
		$sql .= "      where p.id=a.pharmacyid and a.medicineId = b.ID ";
		if (! empty ( $guid )) {
			$sql .= " and a.ID = '$guid' ";
		} else if (! empty ( $barcode )) {
			$sql .= " and b.barcode = '$barcode' ";
		}
		else{
			$sql .= " and a.id = '' ";
		}
		$sql .= ") aa  LEFT JOIN manufacturer c                            ";
		$sql .= "    ON aa.manufacturerId = c.ID  LIMIT 0,1    			  ";
		
		// echo json_encode ( $sql );die;
		
		$result_data = $this->db->query ( $sql )->result_array ();
		
		// //TODO 目前先把药品大图放入三次，后期添加药品轮询图数据库表
		$url = $result_data [0] ['URL'];
		$result_data [0] ['GOODSPICS'] = array (
				array (
						'URL' => $url 
				),
				array (
						'URL' => $url 
				),
				array (
						'URL' => $url 
				) 
		);
		echo json_encode ( $result_data );
		die ();
	}
	
	/**
	 * 2、商品详情的附近有售 薛骥 2015-10-16
	 *
	 * @param unknown $array        	
	 */
	public function SaleInShop($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		if (array_key_exists ( 'MERCHANDISEID', $array ) && array_key_exists ( 'LONGITUDE', $array ) && array_key_exists ( 'LATITUDE', $array )) {
			$MERCHANDISEID = $array ['MERCHANDISEID'];
			$long = $array ['LONGITUDE'];
			$lat = $array ['LATITUDE'];
			
			$page = 0;
			$count = 10;
			$key = "";
			if (array_key_exists ( '$key', $array )) {
				$key = $array ['$key'];
			}
			if (array_key_exists ( 'PAGE', $array )) {
				$page = $array ['PAGE'];
			}
			if (array_key_exists ( 'COUNT', $array )) {
				$count = $array ['COUNT'];
			}
			
			// 组装条件
			$from = $page * $count;
			$sql = "SELECT a.ID ,a.pharmacyName SHOPNAME,a.chainPharmacy CHAINPHARMACY,a.upPharmacyId PID,a.address ADDRESS,      	";
			$sql .= "  merch.PRICE, ifnull(merch.STORECOUNT,0) STORECOUNT,                ";
			$sql .= "    ifnull(merch.TOTLESALEDCOUNT,0) TOTLESALEDCOUNT,      ";
			$sql .= "    ifnull(merch.ASSESSCOUNT,0) ASSESSCOUNT, merch.id MERCHANDISEID,      ";
			$sql .= " a.contactPhone PHONE,a.permissionNo SHOPNO,a.latitude LAT,a.longitude LON ,								  	";
			$sql .= " CONVERT( ACOS( SIN(($lat * 3.1415) / 180) * SIN((latitude * 3.1415) / 180) + COS(($lat * 3.1415) / 180) *     ";
			$sql .= " COS((latitude * 3.1415) / 180) * COS(($long * 3.1415) / 180 - (longitude * 3.1415) / 180)) * 6378.137         ";
			$sql .= "	,DECIMAL (15, 3) ) DISTANCE	FROM pharmacy a, merchandise merch, ";
			$sql .= "(SELECT medicineid FROM merchandise WHERE id='$MERCHANDISEID') mm";
			$sql .= " WHERE a.status =1 and merch.medicineid=mm.medicineid and a.id=merch.pharmacyId";
			
			if (! empty ( $key )) {
				$sql .= " AND a.pharmacyName LIKE '%$key%'";
			}
			
			$sql .= " ORDER BY ACOS( SIN(($lat * 3.1415) / 180) * SIN((latitude * 3.1415) / 180) + COS(($lat * 3.1415) / 180) *      ";
			$sql .= " COS((latitude * 3.1415) / 180) * COS(($long * 3.1415) / 180 - (longitude * 3.1415) / 180)) * 6378.137          ";
			$sql .= " ASC  LIMIT $from, $count                                                                                       ";
			
			// 查询数据并返回
			$return_data = $this->db->query ( $sql )->result_array ();
			
			echo json_encode ( $return_data );
		} else {
			$return_data = array (
					"success" => "no",
					"errorCode" => "0001",
					"errorMsg" => "用户参数错误！",
					"otherMsg" => "" 
			);
			echo json_encode ( $return_data );
		}
		die ();
	}
	/**
	 * 4、搜索结果二次筛选 薛骥 2015-10-16
	 * TODO 需要加入条件参数值 CONDITION:
	 *
	 * @param unknown $array        	
	 */
	public function SearchFilter($array) {
		if (array_key_exists ( 'KEY', $array )) {
			$key = $array ['KEY']; // 关键字
			$page = $array ['PAGE']; // 页数
			$count = $array ['COUNT']; // 每页数量
			$from = $page * $count; // 计算起始查询条数
			                        
			// 拼接字符串
			$sql = "SELECT                                            ";
			$sql .= "  aa.MEDICINENAME,                                ";
			$sql .= "  aa.MEDICINESPEC,                                ";
			$sql .= "  aa.PICBIG,                                      ";
			$sql .= "  aa.PICSMALL,                                    ";
			$sql .= "  aa.MERCHANDISEID,                               ";
			$sql .= "  aa.PRICE,                                       ";
			$sql .= "  aa.MEMBERPRICE,                                 ";
			$sql .= "  c.ID SHOPID,                                    ";
			$sql .= "  c.pharmacyName SHOPNAME                         ";
			$sql .= "FROM                                              ";
			$sql .= "  (SELECT                                         ";
			$sql .= "    a.medicineName MEDICINENAME,                  ";
			$sql .= "    a.field MEDICINESPEC,                         ";
			$sql .= "    a.picBigURL PICBIG,                           ";
			$sql .= "    a.picSmallURL PICSMALL,                       ";
			$sql .= "    b.ID MERCHANDISEID,                           ";
			$sql .= "    CONVERT(b.price / 100, DECIMAL (15, 2)) PRICE,";
			$sql .= "    CONVERT(                                      ";
			$sql .= "      b.price * b.merchDiscount / 100,            ";
			$sql .= "      DECIMAL (15, 2)                             ";
			$sql .= "    ) MEMBERPRICE,                                ";
			$sql .= "    b.pharmacyId                                  ";
			$sql .= "  FROM                                            ";
			$sql .= "    medicine a                                    ";
			$sql .= "    LEFT JOIN merchandise b                       ";
			$sql .= "      ON a.ID = b.medicineId                      ";
			$sql .= "  WHERE a.medicineName LIKE '%$key%') aa          ";
			$sql .= "  LEFT JOIN pharmacy c                            ";
			$sql .= "    ON aa.pharmacyId = c.ID                       ";
			$sql .= "  LIMIT $from ,$count                             ";
			
			$result_data = $this->db->query ( $sql )->result_array ();
			echo json_encode ( $result_data );
			die ();
		} else {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		
		echo json_encode ( $result );
		die ();
	}
	/**
	 * 5、搜索结果二次排序 薛骥 2015-10-16
	 *
	 * @param unknown $array        	
	 */
	public function SearchSort($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		
		$page = 0;
		$count = 10;
		$key = "";
		$MEDKINDID = "";
		if (array_key_exists ( 'MEDKINDID', $array )) {
			$MEDKINDID = $array ['MEDKINDID'];
		}
		if (array_key_exists ( 'KEY', $array )) {
			$key = $array ['KEY'];
		}
		
		if (! empty ( $key ) || ! empty ( $MEDKINDID )) {
			
			if (array_key_exists ( 'PAGE', $array )) {
				$page = $array ['PAGE'];
			}
			
			if (array_key_exists ( 'COUNT', $array )) {
				$count = $array ['COUNT'];
			}
			$from = $page * $count; // 计算起始查询条数
			
			$SORTFIELD = "default";
			if (array_key_exists ( 'SORTFIELD', $array )) {
				$SORTFIELD = $array ['SORTFIELD'];
			}
			
			// 拼接字符串
			$sql = "SELECT                                            ";
			$sql .= "  aa.MEDICINENAME,                                ";
			$sql .= "  aa.MEDICINESPEC,                                ";
			$sql .= "  aa.PICBIG,                                      ";
			$sql .= "  aa.PICSMALL,                                    ";
			$sql .= "  aa.MERCHANDISEID,                               ";
			$sql .= "  (case when c.showprice=1 then aa.PRICE when c.showprice=0 then '' end) PRICE,";
			$sql .= "  (case when c.showprice=1 then aa.MEMBERPRICE when c.showprice=0 then '' end) MEMBERPRICE,";
			$sql .= "  c.ID SHOPID,                                    ";
			$sql .= "  c.pharmacyName SHOPNAME                         ";
			$sql .= "FROM                                              ";
			$sql .= "  (SELECT                                         ";
			$sql .= "    a.medicineName MEDICINENAME,                  ";
			$sql .= "    a.field MEDICINESPEC,                         ";
			$sql .= "    a.picBigURL PICBIG,                           ";
			$sql .= "    a.picSmallURL PICSMALL,                       ";
			$sql .= "    b.ID MERCHANDISEID,                           ";
			$sql .= "    CONVERT(b.price / 100, DECIMAL (15, 2)) PRICE,";
			$sql .= "    CONVERT(                                      ";
			$sql .= "      b.price * b.merchDiscount / 100,            ";
			$sql .= "      DECIMAL (15, 2)                             ";
			$sql .= "    ) MEMBERPRICE,                                ";
			$sql .= "    b.pharmacyId                                  ";
			$sql .= "  FROM                                            ";
			$sql .= "    medicine a                                    ";
			$sql .= "    LEFT JOIN merchandise b                       ";
			$sql .= "      ON a.ID = b.medicineId  WHERE a.status=1    ";
			if (! empty ( $key )) {
				$sql .= " and a.medicineName LIKE '%$key%'          ";
			} 
			if (! empty ( $MEDKINDID )) {
				$sql .= " and a.medKindId ='%$MEDKINDID%'          ";
			}
			$sql .= ") aa  LEFT JOIN pharmacy c                       ";
			$sql .= "    ON aa.pharmacyId = c.ID                       ";
			
			if ($SORTFIELD == "default") {
				$sql .= "  order by MEDICINENAME";
			} else if ($SORTFIELD == "priceUp") {
				$sql .= "  order by PRICE";
			} else if ($SORTFIELD == "priceDown") {
				$sql .= "  order by PRICE desc";
			}
			
			// else if ($SORTFIELD == "saleUp") {
			// $sql .= " order by ";
			// }
			// else if ($SORTFIELD == "saleDown") {
			// $sql .= " order by ";
			// }
			
			$sql .= "  LIMIT $from ,$count                             ";
			$result_data = $this->db->query ( $sql )->result_array ();
			echo json_encode ( $result_data );
			die ();
		} else {
			$return_data = array (
					"success" => "no",
					"errorCode" => "0001",
					"errorMsg" => "参数为空",
					"otherMsg" => "" 
			);
			echo json_encode ( $return_data );
			die ();
		}
	}
	/**
	 * 6、获取药店详细信息 薛骥 2015-10-16
	 *
	 * @param unknown $array        	
	 */
	public function ShopDetail($array) {
		if (array_key_exists ( 'SHOPID', $array )) {
			$return_data = array (
					"success" => "no",
					"errorCode" => "0001",
					"errorMsg" => "药店信息为空",
					"otherMsg" => "" 
			);
			
			$SHOPID = $array ['SHOPID']; // 商品guid
			$sql = "SELECT * FROM pharmacy p where p.id='" . $SHOPID . "'";
			$this->db->query ( $sql )->result_array ();
			$DBResult = $this->db->query ( $sql )->result_array ();
			
			if (count ( $DBResult ) > 0) {
				$return_data = array (
						"GUID" => $DBResult [0] ['ID'],
						"NAME" => $DBResult [0] ['pharmacyName'],
						"ADDRESS" => $DBResult [0] ['address'],
						"PHONE" => $DBResult [0] ['contactPhone'],
						"PHONE1" => "",
						"ZIPCODE" => "",
						"EMAIL" => "",
						"FAXNUM" => "",
						"DELIVERYLIMIT" => "",
						"LAT" => $DBResult [0] ['latitude'],
						"LNG" => $DBResult [0] ['longitude'],
						"DISTRCITID" => "",
						"DELIVERYLIMIT" => "",
						"PHARMCYLOGO" => $DBResult [0] ['pharmLogo'],
						"PHARMCYPIC1" => $DBResult [0] ['pharmPic'],
						"PHARMCYPIC2" => $DBResult [0] ['pharmPic2'],
						"PHARMCYPIC3" => $DBResult [0] ['pharmPic3'],
						"PHARMCYCER1" => $DBResult [0] ['cettiPic1'],
						"PHARMCYCER2" => $DBResult [0] ['cettiPic2'],
						"PHARMCYCER3" => $DBResult [0] ['cettiPic3'],
						"DESCRIPTION" => $DBResult [0] ['description'] 
				);
			}
			echo json_encode ( $return_data );
			die ();
		} else {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
	}
	/**
	 * 9、获取商品评价信息 薛骥 2015-10-16
	 *
	 * @param unknown $array        	
	 */
	public function GetAssessment($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		$return_data = array (
				"success" => "no",
				"errorCode" => "0001",
				"errorMsg" => "参数错误",
				"otherMsg" => "" 
		);
		if (array_key_exists ( "MERCHANDISEID", $array )) {
			$merchandise_id = $array ['MERCHANDISEID'];
			$page = 0;
			$count = 10;
			if (array_key_exists ( 'PAGE', $array )) {
				$page = $array ['PAGE'];
			}
			
			if (array_key_exists ( 'COUNT', $array )) {
				$count = $array ['COUNT'];
			}
			$from = $page * $count; // 计算起始查询条数
			$sql = "SELECT                                             ";
			$sql .= "  a.Id ID,                                         ";
			$sql .= "  a.memberId MEMBERID,                             ";
			$sql .= "  DATE_FORMAT(a.createDate,'%Y-%m-%d') ASSESSDATE, ";
			$sql .= "  a.isAppend ISAPPEND,                             ";
			$sql .= "  a.assessTXT ADVICE,                              ";
			$sql .= "  a.qualityLevel QUALITYLEVEL,                       ";
			$sql .= "  a.DILEVERYLEVEL DILEVERYLEVEL,                       ";
			$sql .= "  bb.counts ALLCOUNT                               ";
			$sql .= "FROM                                               ";
			$sql .= "  merassess a,                                     ";
			$sql .= "  (SELECT                                          ";
			$sql .= "    COUNT(1) counts                                ";
			$sql .= "  FROM                                             ";
			$sql .= "    merassess b                                    ";
			$sql .= "  WHERE b.merchandiseId = '$merchandise_id'        ";
			$sql .= "    AND b.status = 1) bb                           ";
			$sql .= "WHERE a.merchandiseId = '$merchandise_id'          ";
			$sql .= "  AND a.status = 1  LIMIT $from,$count			  ";
			$return_data = $this->db->query ( $sql )->result_array ();
		}
		echo json_encode ( $return_data );
		die ();
	}
	/**
	 * 10、获取商品评价条数 薛骥 2015-10-16
	 *
	 * @param unknown $array        	
	 */
	public function GetAssessCount($array) {
		$return_data = array (
				"COUNT" => "-1" 
		);
		$sql = " SELECT count(cart.id) BUYCOUNT FROM merassess cart";
		
		if (array_key_exists ( 'MERCHANDISEID', $array )) {
			$pid = $array ['MERCHANDISEID'];
			if (! empty ( $array ['MERCHANDISEID'] )) {
				$sql = $sql . " where cart.merchandiseid = '" . $pid . "'";
				$detail_result = $this->db->query ( $sql )->result_array ();
				$return_data ['COUNT'] = $detail_result [0] ['BUYCOUNT'];
			}
		}
		echo json_encode ( $return_data );
		die ();
	}
	/**
	 * 15、获取用户反馈tag 薛骥 2015-10-22
	 *
	 * @param unknown $array        	
	 */
	public function GetFeedbackTag($array) {
		$sql = "SELECT feedbacktag TAGNAME, COUNT(*) TAGCOUNT FROM feedbackTag WHERE STATUS=1 GROUP BY feedbacktag  order by TAGCOUNT desc LIMIT 0,10";
		$return_data = $this->db->query ( $sql )->result_array ();
		
		echo json_encode ( $return_data );
		die ();
	}
	
	/**
	 * 获取配置信息
	 */
	public function getAppConfig($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		$orgcode = trim ( $array ['ORGCODE'] ); // 组织机构信息
		$lasttime = trim ( $array ['LASTTIME'] ); // 更新日期
		$sql = "select LASTTIME from appconfiginfo where  LASTTIME >'" . $lasttime . "' order by LASTTIME desc limit 1 ";
		$data = $this->db->query ( $sql )->result_array ();
		if ($data && count ( $data ) > 0) {
			$dblattime = $data [0] ['LASTTIME'];
			if ($lasttime < $dblattime) {
				$sqlnew = "select `KEY`,`VALUE` from appconfiginfo where ORGCODE='" . $orgcode . "' and LASTTIME >='" . $dblattime . "'";
				$datanew = $this->db->query ( $sqlnew )->result_array ();
				$dbdata = array ();
				if ($datanew && count ( $datanew ) > 0) {
					for($i = 0; $i < count ( $datanew ); $i ++) {
						array_push ( $dbdata, array (
								$datanew [$i] ['KEY'] => $datanew [$i] ['VALUE'] 
						) );
						// array_fill(0,array($datanew[$i]['KEY']=>$datanew[$i]['VALUE']),$dbdata);
					}
				}
				$returndata = array (
						'LASTTIME' => $dblattime,
						'DATA' => $dbdata 
				);
				echo json_encode ( $returndata );
				die ();
			} else {
				$returndata = array (
						'LASTTIME' => $dblattime,
						'DATA' => array () 
				);
				echo json_encode ( $returndata );
				die ();
			}
		} else {
			echo json_encode ( array (
					'LASTTIME' => '',
					'DATA' => array () 
			) );
			die ();
		}
	}
}