<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Yjk_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		//response.headers("P3P") = "CP=\"IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT\"";
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
		//		if(!isset($this->session->userdata['account'])){
		//			$this->load->helper('url');
		//			//redirect('/auth/login');
		//			redirect('/sessionlose');
		//		}else{
		//		$this->getdatabase();
		//		}
	}
function savetext($data,$orgguid){
	date_default_timezone_set('PRC');
	$createtime=date('Y-m-d H:i:s',time());
	$sqluu=" insert into testvale (GUID,VA,ORGGUID,CREATETIME) values('".uniqid()."','".$data."','".$orgguid."','".$createtime."')";
	$this->db->query($sqluu);

}
	function returnnowtime(){
		date_default_timezone_set('PRC');
		$createtime=date('Y-m-d H:i:s',time());
		return $createtime;

	}
	function __imghandle(){
		parent::__construct();
		$this->load->helper('common_helper');
		date_default_timezone_set('PRC');

	}
	function __unhandle(){
		parent::__construct();
		$this->load->helper('common_helper');
		date_default_timezone_set('PRC');
//		ReturnUnlogin($_POST['UserGuid']);
//		$aa=$_POST;
		if(!$_POST['UserGuid']){
			$session= array(
				'orgguid'=>'2'
			);
			$this->session->set_userdata($session);
		}else {
			$userguid=$_POST['UserGuid'];
			$userguid = str_replace('-', '=', $userguid);
			$userguid = str_replace('_', '+', $userguid);
			$userguid = base64_decode($userguid);
			$array = explode('|@', $userguid);
			$orgguid = $array[0];
			$session = array(
			'orgguid'=>$orgguid
			);
			$this->session->set_userdata($session);
//			ReturnUnlogin($orgguid);
		}
	}

	public function GetUserOrgguid(){
		return $this->session->userdata['orgguid'];
	}
	public function GetUserGuidinfo(){
//		return 'oaP2suDZxx0y2D_H-VfVYXcsHMZc';
//		$orgguid=$this->session->userdata['orgguid'];
		return $this->session->userdata['account'];
	}
	function __apphandle(){
		parent::__construct();
		date_default_timezone_set('PRC');
		$this->load->helper('common_helper');
				$userguid=$_POST['UserGuid'];
//				ReturnUnlogin($userguid);
				if(!$userguid){
//					ReturnUnlogin("UNLOGIN");
					ReturnUnlogin("UNLOGIN");
				}else{
					$userguid=str_replace('-','=',$userguid);
					$userguid=str_replace('_','+',$userguid);
					$userguid=base64_decode($userguid);
					$array=explode('|@', $userguid);
					$usercode=$array[1];
					$pwd=$array[2];
					$orgguid=$array[0];
					//$this->GetOrgInfo($orgguid);
					//$this->getdatabase();
					$sql="select * from IMUser where name='".$usercode."' and orgguid='".$orgguid."'";

//					ReturnUnlogin($sql);
					$admin=$this->db->query($sql)->result_array();
					if($admin&&count($admin)>0){
						$salt=$admin[0]['salt'];
						$userpwd=$admin[0]['password'];
//						$md5pwd=md5($userpwd.$salt);
//						ReturnUnlogin($md5pwd.':'.$pwd);
//						ReturnUnlogin($pwd);
						$md5pwd=md5($userpwd);
						if($md5pwd!= $pwd){
							ReturnUnlogin("UNLOGIN");
						}else{
							$session = array(
							 'account' => $usercode
							,'guid'=>$admin[0]['id']
							,'orgguid'=>$orgguid
							);
							$this->session->set_userdata($session);

						}
					}else{
						ReturnUnlogin("UNLOGIN");
					}
				}
	}


	/*创建全局变量*/
	public function CreateWholeLog($type,$jsonvalue,$iserror,$note){
		$guid=uniqid().time();
		date_default_timezone_set('PRC');
		$createdate=date('Y-m-d H:m:s',time());
		$types=wholelogtype($type);
		//$sql="INSERT INTO wholelog (GUID,TYPE,JSONVALUE,ISERROR,NOTE,CREATEDATE)VALUES('"+$guid+"','"+$types+"','"+$jsonvalue+"','"+$iserror+"','"+$note+"','"+$createdate+"')";
		$sql="INSERT INTO wholelog (GUID,TYPE,JSONVALUE,ISERROR,NOTE,CREATEDATE)VALUES('".$guid."','".$types."','".$jsonvalue."','".$iserror."','".$note."','".$createdate."')";
		$resu=$this->db->query($sql);
		return $resu;
	}
	public function getdatainfo($data){
		$table=$this->db->query($data)->result_array();
		return  json_encode($table);
	}




	//获取缓存session
	public	function getsession($id){
		if(!isset($this->session->userdata[$id])){
			return $this->session->userdata[$id];
		}else{
			return '';
		}
	}
	//存缓存数据session
	public	function setsession($array){
		$this->session->set_userdata($array);
	}

	//取缓存数据cache
	public function getcache($id){
		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		if ($foo = $this->cache->get($id))
		{
			return $foo;
		}else{
			return '';
		}
	}

	//存缓存cache
	public	function setcache($id,$data,$time){
		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		$this->cache->save($id, $data, $time);
	}

	function GetRemotingHandlerInfo($ul,$data){//页面跳转post
		$ch = curl_init($ul);
		curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt ($ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER,true);
		$result = curl_exec($ch);

		$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close($ch);
		header("Content-Type:text/html;charset=UTF-8");
		if($status=='200'){
			return $result;
		}else{
			//			return json_encode(array('ERROR'=>'1000'));
			ReturnUnlogin("ERROR1000");
		}
		//		$array=array('Status'=>$status,'Result'=>$result);
		//		return json_encode($array);
	}
	public function format($str){
		$count_args=func_num_args();
		if($count_args==1){
			return $str;
		}
		for($i=0;$i<$count_args-1;$i++){
			$arg_value=func_get_arg($i+1);
			$str=str_replace("{{$i}}", $arg_value, $str);
		}
		return $str;
	}

	//创建用户
	public function CreateUser($username,$userpwd){
		$easemoburl=EasemobUrl;
		$appkey=$this->session->userdata['appkey'];//EntCode;
		$postUrl=$this-> format($easemoburl,$appkey,'users');
		$array=array(
				'username'=>$username,
				'password'=>$userpwd
		);
		$str=json_encode($array);
		$postResultStr= $this->ReqUrl($postUrl, $str);
		$arrpost=json_decode($postResultStr,true);
		if(in_array('error', $arrpost)){
			return 'error';
		}else {
			return '';
		}
	
	}
	/*修改数据库用户密码*/
	public function ChangUserPwd($orgguid,$name,$pwd){
		$data=array(
				'orgguid'=>$orgguid,
				'guid'=>$name,
				'pwd'=>$pwd
		);
		$sqla="update  imuser set password='".$pwd."' where name='".$name."' and orgguid='".$orgguid."'";
		$this->db->query($sqla);
		$url=weburlweishop.'API/ChangeUserInfoShow';
		$resul=$this->GetRemotingHandlerInfo($url,$data);
		return $resul;
	
	}
	
	/*修改环信用户密码*/
	public function Changepwdphone($phone,$pwd){
		$easemoburl=EasemobUrl;
		$entCode=$this->session->userdata['appkey'];//EntCode;
		$postUrl=$this-> format($easemoburl,$entCode,'users');
		$postUrl2=$postUrl.'/'.$phone.'/password';
		$array=array(
				'newpassword'=>$pwd);
		$_build=json_encode($array);
		$postResultStr=$this->ReqUrlPost($postUrl2,$_build,'PUT');
		return $postResultStr;
	
	}
	public function ReqUrl($url,$str){
		$ch = curl_init();curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$data = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		return $data;
	}
	
	//页面跳转
	
	public function ReqUrlPost($url,$data,$method){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$arr= array(
				'Content-Type: application/json',
				'Authorization: Bearer '.$this->getToken()
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$arr );
		$data = curl_exec($ch);
		$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		curl_close($ch);
		return $data;
	}

	/*获取token*/
	public function getToken(){
		$orgguid=$this->session->userdata['orgguid'];
		$tokenid='gettoken'.$orgguid;
		if($resul=$this->getcache($tokenid)){
			return $resul;
		}else{
			$token=$this->GetTokenApp();
			$this->setcache($tokenid, $token, '5104000');
		}
		return $token;
	}
	//获取token
	public function GetTokenApp($reGet=false)
	{
		$easemoburl=EasemobUrl;//'https://a1.easemob.com/romens/{0}/{1}';
		$entCode=$this->session->userdata['appkey'];
		$client_id=$this->session->userdata['client_id'];
		$client_secret=$this->session->userdata['client_secret'];
		$path=$this->format($easemoburl,$entCode,'token');
		$data = array(
				'grant_type' => 'client_credentials',
				'client_id' => $client_id,
				'client_secret' =>$client_secret
		);
		$data_string = json_encode($data);
		$ch = curl_init($path);
		curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt ($ch, CURLOPT_POSTFIELDS,$data_string);
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
		$result = curl_exec($ch);
		$result_arr = json_decode($result, true);
	
		if(isset($result_arr['error'])){
			echo $result;exit;
		}else{
			$this->token = $result_arr['access_token'];
		}
		return $this->token;
	}

	function GetHandlerInfoBYGET($ul){//页面跳转GET
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$ul);
		curl_setopt($ch,CURLOPT_TIMEOUT,200);
		curl_setopt($ch,CURLOPT_HEADER,FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,FALSE);

		$response=curl_exec($ch);
		$status=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close($ch);
		if(preg_match('/^\xEF\xBB\xBF/',$response))
		{
			$response = substr($response,3);//去除bom
		}
		if($status=='200'){
			return $response;
		}else{
			//			return json_encode(array('ERROR'=>'1000'));
			ReturnUnlogin("ERROR1001");
		}
		//		$array=array('Status'=>$status,'Result'=>$result);
		//		return json_encode($array);
	}

}


