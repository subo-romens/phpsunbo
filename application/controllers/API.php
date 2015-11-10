<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

include_once (APPPATH . "core/Yjk_Controller.php");
class API extends Yjk_Controller {
	public function __construct() {
		parent::__imghandle ();
		$this->load->helper ( 'url' );
		$this->load->helper ( 'common_helper' );
	}
	
	// 登录首页，拆分登录信息，并访问真实访问信息
	public function index() {
		mysql_connect('115.28.244.190','root','yaojk0801');
		echo mysql_get_server_info();
//		$type = $_POST ['QueryType'];
//		$params = $_POST ['Params'];
//		$array = json_decode ( $params, true );
//		$this->$type ( $array );
	}
	/*传送数据*/
	public function GetAdInfoBy() {
		$orgguid ='4';
		$data = array (
			'orgguid' => $orgguid
		);
		$url =  'http://shop.yiyao365.cn/API/getGoodsSort';
		$resul = $this->GetRemotingHandlerInfo ( $url, $data );
		$jsonvalue=json_decode($resul,true);
		$count=count($jsonvalue);
		if($count>0){
			for($i=0;$i<$count;$i++){
				$sql="select * from medKind where ID='".$jsonvalue[$i]['ID']."' and orgguid='".$orgguid."'";
				$res=$this->db->query($sql)->result_array();
				if($res&&count($res)>0){
					$sqlu="update medKind set medKindName='".$jsonvalue[$i]['NAME']."',medKindSName='".$jsonvalue[$i]['NAME']."',sortnumber='".$jsonvalue[$i]['ORDERINDEX']."',upKindId='".$jsonvalue[$i]['PID']."' where  ID='".$jsonvalue[$i]['ID']."' and orgguid='".$orgguid."' ";
					$res=$this->db->query($sqlu);
				}else{
					$sqli="insert into medKind (ID,medKindName,medKindSName,sortnumber,status,upKindId,orgguid) values('".$jsonvalue[$i]['ID']."','".$jsonvalue[$i]['NAME']."','".$jsonvalue[$i]['NAME']."','".$jsonvalue[$i]['ORDERINDEX']."','1','".$jsonvalue[$i]['PID']."','".$orgguid."')";
					$res==$this->db->query($sqli);
				}
			}
			echo 'succ';die;
		}else{
			echo 'error';die;
		}
	}

	public function GetGoodsListIn(){
		$orgguid ='4';
		$data = array (
			'orgguid' => $orgguid
		);
		$url =  'http://shop.yiyao365.cn/API/getGoodsAllList';
		$resul = $this->GetRemotingHandlerInfo ( $url, $data );
		$jsonvalue=json_decode($resul,true);
		$count=count($jsonvalue);
		if($count>0){
			for($i=0;$i<$count;$i++){
				//$sql="select ID,manufactureName  from manufacturer where manufactureName='".$jsonvalue[$i]['CD']."' and orgguid='".$orgguid."'";
				$sql="select ID,manufactureName  from manufacturer where manufactureName='".$jsonvalue[$i]['CD']."'";
				$res=$this->db->query($sql)->result_array();
				$manuid=uniqid().time();
				if($res&&count($res)>0){
					$manuid=$res[0]['ID'];
				}else{
					$sqli="insert into manufacturer (ID,manufactureName) values('".$manuid."','".$jsonvalue[$i]['CD']."')";
					$res=$this->db->query($sqli);
				}

				$sql2="select ID   from medicine where id='".$jsonvalue[$i]['GUID']."' and orgguid='".$orgguid."'";
				$res2=$this->db->query($sql2)->result_array();
				if($res2&&count($res2)>0){
					$sqlgoos=" update medicine  set medKindId='".$jsonvalue[$i]['GOODSSORTGUID']."',manufacturerId='".$manuid."',barCode='".$jsonvalue[$i]['BARCODE']."',medicineName='".$jsonvalue[$i]['NAME']."',advicePrice='".$jsonvalue[$i]['MARKETPRICE']."',
					status='1',description='".$jsonvalue[$i]['DESCRIPTION']."',field='".$jsonvalue[$i]['SPEC']."',pzwh='".$jsonvalue[$i]['PZWH']."',picBigURL='".$jsonvalue[$i]['HOST_NAME'].$jsonvalue[$i]['GENERALIMAGEINFO']."'
					,picSmallURL='".$jsonvalue[$i]['HOST_NAME'].$jsonvalue[$i]['GENERALIMAGEINFO']."' where ID='".$jsonvalue[$i]['GUID']."'";
					$res=$this->db->query($sqlgoos);
				}else{
					$sqlgoos="insert into medicine (ID,medKindId,manufacturerId,barCode,medicineName,advicePrice,status,description,field,pzwh,picBigURL,picSmallURL,orgguid) values
				('".$jsonvalue[$i]['GUID']."','".$jsonvalue[$i]['GOODSSORTGUID']."',
				'".$manuid."','".$jsonvalue[$i]['BARCODE']."','".$jsonvalue[$i]['NAME']."','".$jsonvalue[$i]['MARKETPRICE']."','1','".$jsonvalue[$i]['DESCRIPTION']."','".$jsonvalue[$i]['SPEC']."','".$jsonvalue[$i]['PZWH']."'
				,'".$jsonvalue[$i]['HOST_NAME'].$jsonvalue[$i]['GENERALIMAGEINFO']."','".$jsonvalue[$i]['HOST_NAME'].$jsonvalue[$i]['GENERALIMAGEINFO']."','".$orgguid."')";
					$res=$this->db->query($sqlgoos);
				}
				$sqlshop="select ID from merchandise where medicineId='".$jsonvalue[$i]['GUID']."'";
				$resshop=$this->db->query($sqlshop)->result_array();
				if($resshop&&count($resshop)>0){}
				else{
					$sqlshopi=" insert into merchandise (ID,medicineId,pharmacyId,merchDiscount,price,isTop,sortnumber,merchStatus,storeCount,totleSaledCount,currSaledCount,assessCount,orgguid)
 						values('".uniqid().time()."','".$jsonvalue[$i]['GUID']."','AF7B1497-6A4F-423D-A84C-A220FB90DF76','1','".$jsonvalue[$i]['MARKETPRICE']."','0','22'
 						,'1','181','0','0','0','2')";
					$reshopin=$this->db->query($sqlshopi);
				}

			}
			echo 'succ';die;
		}else{
			echo 'error';die;
		}

	}
	public function testa(){
		$data = array (
			'memberNAME' => '11',
			'GENDER' => '22',
			'worktype' => '33',
			'BIRTHDAY' =>'44',
			'HASSERIOUS' => '55',
			'HASINHERITED' => '66',
			'HASGUOMIN' =>'77',
			'FOODHOBBY' => '1',
			'SLEEPHOBBY' =>'2',
			'OTHER' => '3',
		);
		$data['ID']='11111111';
		$this->db->insert ( 'member', $data );
		$row = $this->db->affected_rows ();
		echo $row;die;

	}
//	/*传送数据*/
//	public function GetAdInfoBy($array) {
//		$orgguid ='4';
//		$data = array (
//			'orgguid' => $orgguid
//		);
//		$url =  'http://shop.yiyao365.cn/API/getGoodsSort';
//		$resul = $this->GetRemotingHandlerInfo ( $url, $data );
//		print_r(json_decode($resul)) ;die;
//	}

//保存配置信息
public function AddHomeConfig(){
	$orgguid='4';
	$guid=$_POST['guid'];
	$key=$_POST['key'];
	$value=$_POST['value'];
	$state=$_POST['state'];
	$type=$_POST['type'];
	$createuser=$_POST['createuser'];
	$createdate=date('Y-m-d H:i:s',time());
	$sortinde=$_POST['sortinde'];
	if($type=="2"){
		$value=substr(trim($value),1,strlen(trim($value))-2);
	}
	$sql="insert into homeconfig (GUID,`KEY`,`VALUE`,TYPE,STATE,CREATEDATE,CREATEUSER,SORTINDEX,ORGGUID) values('".$guid."','".$key."','".$value."','".$type."','".$state."','".$createdate."','".$createuser."','".$sortinde."','".$orgguid."')";
	$homeinfo=$this->db->query($sql);
	echo $homeinfo;die;
}

	public function ChangeOrderState(){
		$orgguid=$_POST['ORGGUID'];
		$orderguid=$_POST['ORDERGUID'];
		$orderstate=$_POST['ORDERSTAYE'];
		$sql=" update medorder set orderStatus='".$orderstate."' where ID='".$orderguid."' ";
		$orderinfo=$this->db->query($sql);
		echo $orderinfo;die;
	}
}