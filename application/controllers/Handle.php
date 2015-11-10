<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

include_once (APPPATH . "core/Yjk_Controller.php");
class Handle extends Yjk_Controller {
	public function __construct() {
		parent::__apphandle ();
		$this->load->helper ( 'url' );
		$this->load->helper ( 'common_helper' );
	}
	/* 处理数据信息 */
	public function index() {
		$type = $_POST ['QueryType'];
		$params = $_POST ['Params'];
		$array = array ();
		if ($params) {
			$array = json_decode ( $params, true );
		}
		$this->$type ( $array );
	}
	
	// 获取用户收货地址信息
	public function GetUserAddressList($array) {
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
		if (array_key_exists ( 'USERGUID', $array )) {
			$userguid=$this->GetUserGuidinfo ();//20151031zhouzhongkui
// 			$sql = " SELECT a.ID ADDRESSID,a.memberId USERGUID,a.receiver RECEIVER,a.contactPhone CONTACTPHONE,a.district DISTRCTID," . " a.address ADDRESS,a.isDefault ISDEFAULT,a.addressType ADDRESSTYPE FROM recaddress a WHERE a.memberId = '" . $array ['USERGUID'] . "' ";//20151031zhouzhongkui
			//$sql = " SELECT a.ID ADDRESSID,a.memberId USERGUID,a.receiver RECEIVER,a.contactPhone CONTACTPHONE,a.district DISTRCTID," . " a.address ADDRESS,a.isDefault ISDEFAULT,a.addressType ADDRESSTYPE FROM recaddress a WHERE a.memberId = '" . $userguid. "' ";//20151031zhouzhongkui
//			$sql = " SELECT a.ID ADDRESSID,a.memberId USERGUID,a.receiver RECEIVER,a.contactPhone CONTACTPHONE,a.reciverprovince PROVINCE,a.recivercity CITY,a.reciverregion REGION,a.address ADDRESS,a.isDefault ISDEFAULT,a.addressType ADDRESSTYPE FROM recaddress a WHERE a.memberId = '" . $userguid. "' ";//20151031zhouzhongkui
			$sql = " SELECT a.ID ADDRESSID,a.memberId USERGUID,a.receiver RECEIVER,a.contactPhone CONTACTPHONE,ifnull(a.reciverprovince,'') PROVINCE,ifnull(b.NAME,'') PROVINCENAME, ifnull(c.NAME,'') CITYNAME,ifnull(a.reciverregion,'') REGION, ifnull(d.NAME,'') REGIONNAME,a.address ADDRESS,
					a.isDefault ISDEFAULT,a.addressType ADDRESSTYPE FROM recaddress a left join area b on a.reciverprovince=b.GUID left join area c on a.recivercity=c.GUID  left join area d on a.reciverregion=d.GUID  WHERE a.memberId = '" . $userguid. "' ";//20151031zhouzhongkui
			$default_flag = $array ['DEFAULTFLAG'];
			if ($default_flag == 1 || $default_flag == "1") { // 只选取默认地址
// 				$sql .= " AND a.isDefault = 1";//20151031zhouzhongkui
				$sql .="  order by a.isDefault desc limit 1";//20151031zhouzhongkui
			}
			$result = $this->db->query ( $sql )->result_array ();
			echo json_encode ( $result );
			die ();
		} else {
			echo json_encode ( $return_data );
			die ();
		}
		
		// if (! $array) {
		// ReturnUnlogin ( "非法参数" );
		// exit ();
		// }
		// $userguid = $this->GetUserGuidinfo (); // 用户guid
		// $data = array (
		// 'orgguid' => $this->GetUserOrgguid (),
		// 'userguid' => $userguid
		// );
		// $url = weburlweishop . 'API/GetUserAddressList';
		// $resul = $this->GetRemotingHandlerInfo ( $url, $data );
		// echo $resul;
		// die ();
	}
	
	/* 修改用户密码 */
	public function Changepwd($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		$phone = $array ['PHONE']; // 手机号
		$orgguid = $array ['ORGGUID']; // 组织机构
		$oldpwd = $array ['OLDPWD']; // 旧密码（未验证处理）
		$newpld = $array ['NEWPWD']; // 新密码（加密之后的密码）
		$username = $array ['USERNAME']; // 用户名（环信用户）
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

		$resulpwd = $this->Changepwdphone ($username, $newpld );
		$arrpost = json_decode ( $resulpwd, true );
		if (in_array ( 'error', $arrpost )) {
			echo json_encode ( '1000' );
			die ();
		}
		$resulta = $this->ChangUserPwd (  $orgguid,$username, $newpld );
		if ($resulta) {
			echo json_encode ( '' );
		} else {
			echo json_encode ( '密码修改失败' );
		}
	}
	
	/**
	 * Handle
	 * 获取用户购物车列表
	 * 2015-9-18 薛骥修改为本地数据库读取
	 */
	public function GetUserBuyCarList($array) {
		// if (! $array) {
		// ReturnUnlogin ( "非法参数" );
		// exit ();
		// }
		// $userguid = $this->GetUserGuidinfo (); // 用户guid
		// $data = array (
		// 'userguid' => $userguid,
		// 'orgguid' => $this->GetUserOrgguid ()
		// );
		// $url=weburlweishop.'API/GetBuyCarList';
		// $resul=$this->GetRemotingHandlerInfo($url,$data);
		// 2015-9-18 薛骥修改为本地数据库读取
		$return_data = array (
				"success" => "no",
				"errorCode" => "0001",
				"errorMsg" => "用户参数错误！",
				"otherMsg" => "" 
		);
		$sql = " SELECT cart.id GUID, cart.`merchCount` BUYCOUNT, mer.id GOODSGUID,mer.`price` GOODSPRICE, cart.`createDate` CREATETIME, " . "       mk.`medKindName` GOODSCLASSNAME, IFNULL(med.`picSmallURL`,'') GOODURL, med.`barCode` CODE,med.`medicineName` NAME," . "       med.picSmallURL GOODURL, med.`medDescription` DETAILDESCRIPTION,med.field SPEC, pha.id SHOPID, pha.`pharmacyName` SHOPNAME" . "       FROM medicine med, merchandise mer, pharmacy pha, shoppingCart cart, medkind mk";
		
		if (array_key_exists ( 'USERGUID', $array )) {
// 			$pid = $array ['USERGUID'];//20151031zhouzhongkui
			$pid = $this->GetUserGuidinfo ();//20151031zhouzhongkui
			if (! empty ( $array ['USERGUID'] )) {
				$sql = $sql . " where cart.memberId = '" . $pid . "' AND cart.merchandiseId=mer.id AND mer.medicineId=med.id AND mer.pharmacyId=pha.ID AND mk.`ID`=med.`medKindId`" . " ORDER BY CREATETIME DESC";
				// echo json_encode($sql);die;
				$return_data = $this->db->query ( $sql )->result_array ();
			}
		}
		echo json_encode ( $return_data );
		die ();
	}
	
	/**
	 * 用户商品加入购物车
	 */
	public function InsertIntoCar($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		if (array_key_exists ( 'USERGUID', $array )&&array_key_exists ( 'GOODSGUID', $array )) {
// 			$user_guid = $array ['USERGUID'];// 用户guid//20151031zhouzhongkui
			$user_guid=$this->GetUserGuidinfo ();//20151031zhouzhongkui
// 			ReturnUnlogin ( $user_guid );
			$goods_guid = $array ['GOODSGUID']; // 商品GUID 查询购物车中的数据
// 			echo json_encode ("USERGUID=". $user_guid."GOODSGUID=".$goods_guid );
// 			die ();
			
			$sql = "SELECT * FROM shoppingcart a WHERE a.memberId = '" . $user_guid . "' AND a.merchandiseId ='" . $goods_guid . "'";
			$result = $this->db->query ( $sql )->result_array ();
			if (count ( $result ) > 0) { // 如果购物车存在该商品，数量加1
				$cartId = $result [0] ['ID'];
				$count = $result [0] ['merchCount']; // 原数量
				$update_sql = " UPDATE shoppingcart a SET a.merchCount = " . ($count + 1) . ",a.createDate =" . time () . " WHERE a.ID = '" . $cartId . "'";
				$this->db->query ( $update_sql );
			} else { // 购物车不存在该商品,添加进去
				$data = array (
						'ID' => uniqid (),
						'memberId' => $user_guid,
						'merchandiseId' => $goods_guid,
						'createDate' => time (),
						'status' => 1,
						'merchCount' => 1 
				); // 默认是1
				
				$this->db->insert ( 'shoppingcart', $data );
			}
			$return_data = array (
					"success" => "yes",
					"errorCode" => "0000",
					"errorMsg" => "",
					"otherMsg" => "" 
			);
			echo json_encode ( $return_data );
			die ();
		} else {
			$return_data = array (
					"success" => "no",
					"errorCode" => "0000",
					"errorMsg" => "数据格式不正确",
					"otherMsg" => "" 
			);
			echo json_encode ( $return_data );
			die ();
		}
		// $userguid = $this->GetUserGuidinfo (); // 用户guid
		// $goodguid = $array ['GOODGUID']; // 商品GUID
		// $buycount = $array ['BUYCOUNT']; // 购买数量
		// $price = $array ['PRICE']; // 购买数量
		// $data = array (
		// 'userguid' => $userguid,
		// 'goodguid' => $goodguid,
		// 'buycount' => $buycount,
		// 'price' => $price,
		// 'orgguid' => $this->GetUserOrgguid ()
		// );
		
		// $url = weburlweishop . 'API/InsertIntoCar';
		// $resul = $this->GetRemotingHandlerInfo ( $url, $data );
		// echo $resul;
		// die ();
	}
	/**
	 * 用户购物车商品总数
	 * 薛骥 2015-9-22
	 */
	public function GetBuyCarCount($array) {
		$return_data = array (
				"BUYCOUNT" => "-1" 
		);
		$sql = " SELECT IFNULL(SUM(cart.merchCount),0) BUYCOUNT     FROM shoppingCart cart";
		
		if (array_key_exists ( 'USERGUID', $array )) {
// 			$pid = $array ['USERGUID'];//20151031zhouzhongkui
			$pid=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			if (! empty ( $array ['USERGUID'] )) {
				$sql = $sql . " where cart.memberId = '" . $pid . "'";
				$detail_result = $this->db->query ( $sql )->result_array ();
				$return_data ['BUYCOUNT'] = $detail_result [0] ['BUYCOUNT'];
			}
		}
		echo json_encode ( $return_data );
		die ();
	}
	/**
	 * 获取用户订单信息
	 */
	public function GetUserOrderListInfo($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		$userguid = $this->GetUserGuidinfo (); // 用户guid
		$state = $array ['STATE'];
		$type = $array ['TYPE'];
		$page = $array ['PAGE'];
		$count = $array ['COUNT'];
		$data = array (
				'userguid' => $userguid,
				'orgguid' => $this->GetUserOrgguid (),
				'page' => $page,
				'count' => $count,
				'state' => $state,
				'type' => $type 
		);
		$url = weburlweishop . 'API/GetUserOrderListInfo';
		$resul = $this->GetRemotingHandlerInfo ( $url, $data );
		echo $resul;
		die ();
	}
	
	/**
	 * 保存购物车
	 *
	 * @param unknown $array        	
	 * @author 王烁 2015-09-18
	 */
	public function saveCart($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		$return_data = array (
				"success" => "yes",
				"errorCode" => "0000",
				"errorMsg" => "",
				"otherMsg" => "" 
		);
// 			ReturnUnlogin ( 'aa');
		if (array_key_exists ( 'USERGUID', $array )) {
			$user_guid = $array ['USERGUID'];//20151031zhouzhongkui
// 			$user_guid=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			$goods_list = $array ['JSONDATA'];
			$data_type = gettype ( $goods_list ); // 如果是字符转就不用decode
			if ($data_type == "string") {
				$goods_list = json_decode ( $goods_list, true );
			}
			
			$this->db->trans_start ();
			// 删除该用户下的购物车数据
			$this->db->where ( 'memberId', $user_guid );
			$this->db->delete ( 'shoppingcart' );
			// 循环插入数据
			for($i = 0; $i < count ( $goods_list ); $i ++) {
				$goods = $goods_list [$i];
				$data = array (
						'ID' => uniqid (),
						'memberId' => $user_guid,
						'merchandiseId' => $goods ['GOODSGUID'],
						'createDate' => time (),
						'status' => 1,
						'merchCount' => $goods ['BUYCOUNT'] 
				);
				$this->db->insert ( 'shoppingcart', $data );
			}
			$this->db->trans_complete ();
			// 返回数据
			$return_data = array (
					"success" => "yes",
					"errorCode" => "0000",
					"errorMsg" => "",
					"otherMsg" => "" 
			);
			// echo json_encode($return_data); die;
			echo json_encode ( $return_data );
			die ();
		} else {
			ReturnUnlogin ( "用户不存在" );
			exit ();
		}
	}
	
	/**
	 * 保存收货地址
	 *
	 * @param unknown $array        	
	 */
	public function saveAddress($array) {
		$return_data = array (
				"success" => "yes",
				"errorCode" => "0000",
				"errorMsg" => "",
				"otherMsg" => "" 
		);
		$row = 0;
		//echo json_encode($array);die;
//		ReturnUnlogin ( json_encode($array));
		if (array_key_exists ( 'USERGUID', $array )) { // 用户ID必须
			$json_data = $array ['JSONDATA'];
			$data_type = gettype ( $json_data );
			if ($data_type == "string") { // 如果是字符转就不用decode
				$json_data = json_decode ( $json_data, true );
			}
			$addresguid="";
			if (! empty ( $json_data ['ADDRESSID'] )) { // 不为空则为修改
				$addresguid= $json_data ['ADDRESSID'];
				$data = array (
// 						'memberId' => $array ['USERGUID'],//20151031zhouzhongkui
						'memberId' =>$this->GetUserGuidinfo (),//20151031zhouzhongkui
						'receiver' => $json_data ['RECEIVER'],
						'contactPhone' => $json_data ['CONTACTPHONE'],
						'reciverprovince' => $json_data ['PROVINCE'],
						'recivercity' => $json_data ['CITY'],
						'reciverregion' => $json_data ['REGION'],
						'isDefault' => $json_data ['ISDEFAULT'],
						'address' => $json_data ['ADDRESS'] ,
						'addressType' => $json_data ['ADDRESSTYPE']
				);
				$this->db->where ( 'ID',$addresguid);
				$this->db->update ( 'recaddress', $data ); // 表名字 传入数组
				$row = $this->db->affected_rows ();
			} else { // 新增
				$addresguid=uniqid ();
				$data = array (
						'ID' =>$addresguid,
// 						'memberId' => $array ['USERGUID'],//20151031zhouzhongkui
						'memberId' =>$this->GetUserGuidinfo (),//20151031zhouzhongkui
						'receiver' => $json_data ['RECEIVER'],
						'contactPhone' => $json_data ['CONTACTPHONE'],
						'reciverprovince' => $json_data ['PROVINCE'],
						'recivercity' => $json_data ['CITY'],
						'reciverregion' => $json_data ['REGION'],
						'isDefault' => $json_data ['ISDEFAULT'],
						'addressType' => $json_data ['ADDRESSTYPE'],
						'address' => $json_data ['ADDRESS'] 
				);
				$this->db->insert ( 'recaddress', $data );
				$row = $this->db->affected_rows ();

			}
			//20151104zhouzhongkui add start
			$data=array(
				'orgguid'=>$this->GetUserOrgguid(),
				'userguid'=>$this->GetUserGuidinfo (),
				'id'=>$addresguid,
				'receiver'=> $json_data ['RECEIVER'],
				'userphone'=> $json_data ['CONTACTPHONE'],
				'province'=>$json_data ['PROVINCE'],
				'city'=> $json_data ['CITY'],
				'region'=>$json_data ['REGION'],
				'isdefault'=>$json_data ['ISDEFAULT'],
				'addresstype'=>$json_data ['ADDRESSTYPE'],
				'address'=>$json_data ['ADDRESS']
			);
			$url=weburlweishop.'API/SaveUserAddress';
			$resul=$this->GetRemotingHandlerInfo($url,$data);

			//20151104zhouzhongkui add end

//			echo json_encode(array('Error'=>json_encode($data)));die;
		} else {
			$row = - 1;
		}
		if ($row != 1) { // 判断受影响行数，为1则添加或修改成功
			$return_data ['success'] = "no";
			$return_data ['errorCode'] = "0000";
			$return_data ['errorMsg'] = "保存失败";
		}
		echo json_encode ( $return_data );
		die ();
	}
	
	/**
	 * 设为默认收货地址
	 *
	 * @param unknown $array        	
	 */
	public function setDefaultAddress($array) {
		$return_data = array (
				"success" => "no",
				"errorCode" => "0000",
				"errorMsg" => "参数错误",
				"otherMsg" => "" 
		);
		if (array_key_exists ( 'USERGUID', $array )) {
// 			$user_guid = $array ['USERGUID'];//20151031zhouzhongkui
			$user_guid=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			$address_id = $array ['ADDRESSID'];
			// 先把该用户下所有地址设为非默认
			$not_default = array (
					'isDefault' => 0 
			);
			$this->db->where ( 'memberId', $user_guid );
			$this->db->where_in ( 'isDefault', array (
					0,
					1 
			) );
			$this->db->update ( 'recaddress', $not_default ); // 表名字 传入数组
			                                                  
			// 把ADDRESSID代表的收货地址设为默认
			$default = array (
					'isDefault' => 1 
			);
			$this->db->where ( 'memberId', $user_guid );
			$this->db->where ( 'ID', $address_id );
			$this->db->update ( 'recaddress', $default ); // 表名字 传入数组
			$return_data ['success'] = "yes";
			$return_data ['errorMsg'] = "";
			echo json_encode ( $return_data );
			die ();
		} else {
			echo json_encode ( $return_data );
			die ();
		}
	}
	
	/**
	 * 删除地址信息
	 *
	 * @param unknown $array        	
	 */
	public function deleteAddress($array) {
		$return_data = array (
				"success" => "no",
				"errorCode" => "0000",
				"errorMsg" => "参数错误",
				"otherMsg" => "" 
		);
		if (array_key_exists ( 'ADDRESSID', $array )) {
			$address_id = $array ['ADDRESSID'];
			// 删除数据
			$this->db->where ( 'ID', $address_id );
			$this->db->delete ( 'recaddress' ); // 表名字 传入数组
			$return_data ['success'] = "yes";
			$return_data ['errorMsg'] = "";
			echo json_encode ( $return_data );
			die ();
		} else {
			echo json_encode ( $return_data );
			die ();
		}
	}
	
	/**
	 * 保存订单
	 *
	 * @param unknown $array        	
	 */
	public function saveOrder($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		$return_data = array (
				"success" => "no",
				"errorCode" => "0001",
				"errorMsg" => "",
				"msg1" => "",
				"msg2" => "" 
		);
//		$this->savetext(json_encode($array),$this->GetUserOrgguid());
		$createDate = time ();
		if (array_key_exists ( 'USERGUID', $array )) { // 用户ID必须
// 			$user_guid = $array ['USERGUID']; // 用户ID//20151031zhouzhongkui
			$user_guid=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			$json_data = $array ['JSONDATA'];
			$data_type = gettype ( $json_data );
			if ($data_type == "string") { // 如果是字符转就不用decode
				$json_data = json_decode ( $json_data, true );
			}
			$address_id = $json_data ['ADDRESSID']; // 收货地址ID
			$delivery_type = $json_data ['DELIVERYTYPE']; // 配送方式
			$goods_list = $json_data ['GOODSLIST']; // 订单所有商品
			
			$order_list = array (); // 用来存储订单
			$shop_list = array (); // 用来存储商店
			$order_str = "";

			// 根据收货地址查询地址详细信息
			$addr_sql = "SELECT * FROM recaddress a WHERE a.ID = '" . $address_id . "'";
			$addr_result = $this->db->query ( $addr_sql )->result_array ();
			
			// 事务开始
			$this->db->trans_start ();
			for($i = 0; $i < count ( $goods_list ); $i ++) { // 循环商品
				$goods = $goods_list [$i];
				$goods_price = $goods ['GOODSPRICE'] * 100; // 当前商品价格
				$order_price = $goods_price * $goods ['BUYCOUNT'];

				$order_id = uniqid (); // 生成订单ID，先存起来
				$merchandiseId='';
				$shop_id = $goods ['SHOPID'];
				$order_str='';
				if (! in_array ( $shop_id, $shop_list )) { // 数组中不存在药店ID，则需要新生成一个订单
					array_push ( $shop_list, $shop_id ); // SHOP数组总添加已有药店
					$order_list [$shop_id] = $order_id; // 订单数组中添加药店对应订单ID
// 					$order_str .= $order_id . ";";
					$order_str=date ( "YmdHis", time () ) . rand ( 100, 999 );
					// 初始化订单信息
					$order_info = array (
							'ID' => $order_id,
							'memberId' => $user_guid,
							'createDate' => $createDate,
							'orderStatus' => 0, // 订单生成默认待支付
							'address' => $addr_result [0] ['address'],
							'receiver' => $addr_result [0] ['receiver'],
							'district' => $addr_result [0] ['district'],
							'deliveryType' => $delivery_type,
							'orderno' => $order_str,
							'orderPrice' => $order_price ,
							'orgguid' => $this->GetUserOrgguid()
					);
					$this->db->insert ( 'medorder', $order_info ); // 保存订单信息

					// 初始化订单详情信息
					$order_detail = array (
							'ID' => uniqid (),
							'orderId' => $order_id,
							'merchandiseId' => $goods ['GOODSGUID'],
							'merchCount' => $goods ['BUYCOUNT'],
							'merchDiscount' => 1,
							'price' => $goods_price 
					);
					$this->db->insert ( 'orderdetail', $order_detail ); // 保存订单详细信息
				} else { // 如果存在，则不用生成新订单，只要在添加到原订单下就OK，同时修改订单价金额
				         // 修改原订单价格
					$order_id = $order_list [$shop_id];
					$update_sql = " UPDATE medorder a SET a.orderPrice = a.orderPrice+" . $order_price . " WHERE a.ID = '" . $order_id . "'";
					$this->db->query ( $update_sql );
					
					$merchandiseId = $goods ['GOODSGUID'];
					// 初始化订单详情信息
					$order_detail = array (
							'ID' => uniqid (),
							'orderId' => $order_id,
							'merchandiseId' => $merchandiseId,
							'merchCount' => $goods ['BUYCOUNT'],
							'merchDiscount' => 1,
							'price' => $goods_price 
					);
					$this->db->insert ( 'orderdetail', $order_detail ); // 保存订单详细信息
				}
				if($delivery_type=="1"){

				}
				//20151102zhouzhongkui add start

				$sqlgood="select medicineId from merchandise where id='". $goods ['GOODSGUID']."'";
				$resultgood=$this->db->query($sqlgood)->result_array();
				if($resultgood){
					$goodguidinfo=$resultgood[0]['medicineId'];
					$order_price=$order_price/100;
					$data=array(
						'orgguid'=>$this->GetUserOrgguid(),
						'userguid'=>$this->GetUserGuidinfo (),
						'orderid'=>$order_id,
						'merchandiseid'=> $goodguidinfo,
						'price'=>$goods_price/100,
						'buycount'=>$goods ['BUYCOUNT'],
						'orderprice'=>$order_price,
						'orderstatus'=>'2',
						'receiver'=>$addr_result [0] ['receiver'],
						'transportcompanyguid'=>'待支付',
						'delivertype'=>$delivery_type,
						'orderno'=>$order_str,
						'useraddressid'=>$address_id,
						'orderflag'=>'3',//订单来源,
						'paymentguid'=>''//支付方式现阶段传空
						,'transport'=>$json_data ['DELIVERYTYPE']//取药方式
					);
					$url=weburlweishop.'API/SaveOrder';
					//ReturnUnlogin ( $data );
//					$this->savetext(json_encode($data),$this->GetUserOrgguid());
					$resul=$this->GetRemotingHandlerInfo($url,$data);

				}
				//20151102zhouzhongkui add end
				
				// 卖出商品时需要做
				// UPDATE merchandise a SET a.storeCount=storeCount-1 WHERE id='1';
				// UPDATE merchandise a SET a.currSaledCount=currSaledCount+1 WHERE id='1';
				// UPDATE merchandise a SET a.totleSaledCount =totleSaledCount+1 WHERE id='1';
				$merchandiseId = $goods ['GOODSGUID'];
				$buyCount = $goods ['BUYCOUNT'];
				// 库存-1
				$update_sql = " UPDATE merchandise a SET a.storeCount=storeCount-$buyCount WHERE id='$merchandiseId'";
				$this->db->query ( $update_sql );
				
				// 销量+1
				$update_sql = " UPDATE merchandise a SET a.currSaledCount=currSaledCount+$buyCount WHERE id='$merchandiseId'";
				$this->db->query ( $update_sql );
				$update_sql = " UPDATE merchandise a SET a.totleSaledCount=totleSaledCount+$buyCount WHERE id='$merchandiseId'";
				$this->db->query ( $update_sql );
				
				// 删除当前商品在购物车中的信息
				$delete_sql = " DELETE FROM shoppingcart WHERE merchandiseId = '" . $goods ['GOODSGUID'] . "' AND memberId ='" . $user_guid . "'";
				$this->db->query ( $delete_sql );
			}
			$this->db->trans_complete (); // 事务结束
			
			$return_data ['success'] = "yes";
			$return_data ['errorCode'] = "0000";
			$return_data ['msg2'] = date ( "Y-m-d H:i:s", $createDate );
			$return_data ['msg1'] = $order_str;
		} else {
			$return_data ['success'] = "no";
			$return_data ['errorCode'] = "0000";
			$return_data ['errorMsg'] = "保存失败";
		}
		echo json_encode ( $return_data );
		die ();
	}
	
	/**
	 * 获取订单
	 *
	 * @param unknown $array        	
	 */
	public function getMyOrders($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		if (array_key_exists ( "USERGUID", $array )) {
// 			$user_guid = $array ['USERGUID'];//20151031zhouzhongkui
			$order_status = $array ['ORDERSTATUS'];
			$user_guid=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			// 将药品名和订单放在一行，每个订单有多条数据
		/*	$sql = "SELECT                               ";
			$sql .= "  cc.ID ORDERID,                     ";
			$sql .= "  cc.orderStatus ORDERSTATUS,        ";
			$sql .= "CASE                 				";
			$sql .= "    ORDERSTATUS      				";
			$sql .= "    WHEN - 1         				";
			$sql .= "    THEN '订单取消'  					";
			$sql .= "    WHEN 0           				";
			$sql .= "    THEN '待支付'    					";
			$sql .= "    WHEN 1           				";
			$sql .= "    THEN '待发货'    					";
			$sql .= "    WHEN 2          					";
			$sql .= "    THEN '待出库'    					";
			$sql .= "    WHEN 3           				";
			$sql .= "    THEN '待收货'    					";
			$sql .= "    WHEN 4           				";
			$sql .= "    THEN '待评价'    					";
			$sql .= "    WHEN 5           				";
			$sql .= "    THEN '评价完成'  					";
			$sql .= "    WHEN 11          				";
			$sql .= "    THEN '支付中'    					";
			$sql .= "    ELSE '未知'      					";
			$sql .= "  END ORDERSTATUSSTR,				";
			$sql .= "  cc.orderno ORDERNO,                ";
			$sql .= "  CONVERT(cc.orderPrice / 100, DECIMAL (15, 2)) ORDERPRICE,          ";
			$sql .= "  GROUP_CONCAT(d.medicineName 		";
			$sql .= "		SEPARATOR '+') MEDICINENAME,    ";
			$sql .= "  FROM_UNIXTIME(cc.createDate,       ";
			$sql .= "  '%Y-%m-%d %h:%i:%s') CREATEDATE    ";
			$sql .= "FROM                                 ";
			$sql .= "  (SELECT                            ";
			$sql .= "    bb.ID,                           ";
			$sql .= "    bb.orderStatus,                  ";
			$sql .= "    bb.orderno,                      ";
			$sql .= "    bb.orderPrice,                   ";
			$sql .= "    bb.createDate,                   ";
			$sql .= "    bb.merchandiseId,                ";
			$sql .= "    c.medicineId                     ";
			$sql .= "  FROM                               ";
			$sql .= "    (SELECT                          ";
			$sql .= "      aa.ID,                         ";
			$sql .= "      aa.orderStatus,                ";
			$sql .= "      aa.orderno,                    ";
			$sql .= "      aa.orderPrice,                 ";
			$sql .= "      aa.createDate,                 ";
			$sql .= "      b.merchandiseId                ";
			$sql .= "    FROM                             ";
			$sql .= "      (SELECT                        ";
			$sql .= "        a.ID,                        ";
			$sql .= "        a.orderStatus,               ";
			$sql .= "        a.orderno,                   ";
			$sql .= "        a.orderPrice,                ";
			$sql .= "        a.createDate                 ";
			$sql .= "      FROM                           ";
			$sql .= "        medorder a                   ";
			$sql .= "      WHERE a.memberId = '$user_guid'";
			// 根据状态添加条件
			if ($order_status == "2") { // 处理中
				$sql .= " AND a.orderStatus in(0,1,2,3,11) ";
			} else if ($order_status == "3") { // 已完成
				$sql .= " AND a.orderStatus in(4) ";
			} else if ($order_status == "4") { // 已评价
				$sql .= " AND a.orderStatus in(5) ";
			} else { // 1或者没有都查询全部
			}
			$sql .= " ) aa  								";
			$sql .= "      LEFT JOIN orderdetail b        ";
			$sql .= "        ON aa.id = b.orderId) bb     ";
			$sql .= "    LEFT JOIN merchandise c          ";
			$sql .= "      ON bb.merchandiseId = c.ID) cc ";
			$sql .= "  LEFT JOIN medicine d               ";
			$sql .= "    ON cc.medicineId = d.ID          ";
			$sql .= " GROUP BY ORDERID,ORDERSTATUS,		";
			$sql .= " ORDERSTATUSSTR,ORDERNO,ORDERPRICE,	";
			$sql .= "CREATEDATE  							";
			$sql .= " ORDER BY CREATEDATE DESC"; // 按照日期排序
			*/
			$sql="SELECT  d.picsmallurl PICSMALL,a.ID ORDERID, a.orderStatus ORDERSTATUS, CASE ORDERSTATUS WHEN - 1 THEN '订单取消' WHEN 0 THEN '待支付' WHEN 1 THEN '待发货'
 WHEN 2 THEN '待出库' WHEN 3 THEN '待收货' WHEN 4 THEN '待评价' WHEN 5 THEN '评价完成' WHEN 11 THEN '支付中' ELSE '未知' END ORDERSTATUSSTR,
 a.memberId, d.medicineName MEDICINENAME,a.orderno ORDERNO, CONVERT(a.orderPrice / 100, DECIMAL (15, 2)) ORDERPRICE, FROM_UNIXTIME(a.createDate, '%Y-%m-%d %h:%i:%s') CREATEDATE  FROM medorder a LEFT JOIN orderdetail b ON a.id = b.orderId
LEFT JOIN merchandise c ON b.merchandiseId = c.ID LEFT JOIN medicine d ON c.medicineId = d.ID  where a.memberId='".$user_guid."'	";
			$where =" ";
			$order='  ORDER BY CREATEDATE DESC';
				if ($order_status == "2") { // 处理中
				$where = " AND a.orderStatus in(0,1,2,3,11) ";
			} else if ($order_status == "3") { // 已完成
				$where = " AND a.orderStatus in(4) ";
			} else if ($order_status == "4") { // 已评价
				$where = " AND a.orderStatus in(5) ";
			}
			$order_result = $this->db->query ( $sql.$where. $order)->result_array ();
// 		ReturnUnlogin ( json_encode($order_result) );
			echo json_encode ( $order_result );
			die ();
		} else {
			$return_data = array (
					"success" => "no",
					"errorCode" => "0000",
					"errorMsg" => "数据格式不正确",
					"otherMsg" => "" 
			);
			echo json_encode ( $return_data );
			die ();
		}
	}
	
	/**
	 * 获取订单详情
	 *
	 * @param unknown $array        	
	 */
	public function getMyOrderDetail($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		if (array_key_exists ( "ORDERID", $array )) { // 先查询订单基本内容
			$order_id = $array ['ORDERID'];
			$order_sql = "SELECT a.ID ORDER_ID, a.orderno ORDERNO, FROM_UNIXTIME(a.createDate,'%Y-%m-%d %h:%i:%s') CREATETIME,         ";
			$order_sql .= "  CONVERT(a.orderPrice / 100, DECIMAL (15, 2)) ORDERPRICE, a.receiver RECEIVER, a.address ADDRESS,a.contactphone TELEPHONE, ";
			$order_sql .= "       CASE a.deliveryType WHEN 1 THEN '药店派送'                   ";
			$order_sql .= "       WHEN  2 THEN '到店自取' ELSE '未知' END DELIVERYTYPE, ";
			$order_sql .= " a.orderStatus ORDER_STATUS,CASE a.orderStatus WHEN - 1 THEN '订单取消'                   ";
			$order_sql .= "       WHEN  0 THEN '待支付' WHEN  1 THEN '待发货' WHEN  2 THEN '待出库' WHEN  3 THEN '待收货'                       ";
			$order_sql .= "       WHEN  4 THEN '待评价' WHEN  5 THEN '评价完成' WHEN  11 THEN '支付中' ELSE '未知' END ORDERSTATUSSTR           ";
			$order_sql .= "FROM medorder a WHERE a.ID = '" . $order_id . "'";
			
			$order_result = $this->db->query ( $order_sql )->result_array ();
			
			$result = $order_result [0]; // 取结果集第一条
			$detail_sql = " SELECT aa.GOODSGUID,aa.BUYCOUNT,aa.GOODSPRICE, c.medicineName NAME,c.barCode CODE,c.picSmallURL GOODURL, ";
			$detail_sql .= "  c.picBigURL GOODSBIGURL,c.description DETAILDESCRIPTION,  ";
			$detail_sql .= "  c.field SPEC,c.sortnumber GOODSSORTGUID,                  ";
			$detail_sql .= "  aa.SHOPID,d.pharmacyName SHOPNAME                         ";
			$detail_sql .= " FROM                                                        ";
			$detail_sql .= "  (SELECT                                                   ";
			$detail_sql .= "    a.merchandiseId GOODSGUID,a.merchCount BUYCOUNT,        ";
			$detail_sql .= "    CONVERT(a.price / 100, DECIMAL (15, 2)) GOODSPRICE,b.medicineId,b.pharmacyId SHOPID     ";
			$detail_sql .= "  FROM                                                      ";
			$detail_sql .= "    orderdetail a                                           ";
			$detail_sql .= "    LEFT JOIN merchandise b                                 ";
			$detail_sql .= "      ON a.merchandiseId = b.ID                             ";
			$detail_sql .= "  WHERE a.orderId = '" . $order_id . "') aa                 ";
			$detail_sql .= " LEFT JOIN medicine c ON aa.medicineId = c.ID                ";
			$detail_sql .= " LEFT JOIN pharmacy d ON aa.SHOPID = d.ID                    ";
			
			$detail_result = $this->db->query ( $detail_sql )->result_array ();
			$result ['GOODSLIST'] = $detail_result;
			echo json_encode ( $result );
			die ();
		} else {
			$return_data = array (
					"success" => "no",
					"errorCode" => "0000",
					"errorMsg" => "数据格式不正确",
					"otherMsg" => "" 
			);
			echo json_encode ( $return_data );
			die ();
		}
	}
	
	/**
	 * 取消订单
	 *
	 * @param unknown $array        	
	 */
	public function CancelOrder($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		if (array_key_exists ( "ORDERID", $array )) {
			$order_id = $array ['ORDERID'];
			$this->db->where ( 'ID', $order_id );
			$this->db->update ( 'medorder', array (
					'orderStatus' => - 1 
			) );
			
			// 取消订单时需要做恢复库存和销量
			// UPDATE merchandise a SET a.storeCount=storeCount-1 WHERE id='1';
			// UPDATE merchandise a SET a.currSaledCount=currSaledCount+1 WHERE id='1';
			// UPDATE merchandise a SET a.totleSaledCount =totleSaledCount+1 WHERE id='1';
			
			// 库存+
			$update_sql = " UPDATE merchandise a SET a.storeCount=storeCount+";
			$update_sql .= "  (SELECT SUM(merchCount) FROM orderdetail WHERE orderid='$order_id' AND merchandiseid=a.`ID`)";
			$update_sql .= " WHERE id in (select merchandiseid from orderdetail where orderid='$order_id') ";
			$this->db->query ( $update_sql );
			
			// 销量-
			$update_sql = " UPDATE merchandise a SET a.currSaledCount=currSaledCount-";
			$update_sql .= "  (SELECT SUM(merchCount) FROM orderdetail WHERE orderid='$order_id' AND merchandiseid=a.`ID`)";
			$update_sql .= " WHERE id in (select merchandiseid from orderdetail where orderid='$order_id') ";
			$this->db->query ( $update_sql );
			$update_sql = " UPDATE merchandise a SET a.totleSaledCount=totleSaledCount-";
			$update_sql .= "  (SELECT SUM(merchCount) FROM orderdetail WHERE orderid='$order_id' AND merchandiseid=a.`ID`)";
			$update_sql .= " WHERE id in (select merchandiseid from orderdetail where orderid='$order_id') ";
			$this->db->query ( $update_sql );
			
			$return_data = array (
					"success" => "yes",
					"errorCode" => "",
					"errorMsg" => "",
					"otherMsg" => "" 
			);
			echo json_encode ( $return_data );
			die ();
		} else {
			$return_data = array (
					"success" => "no",
					"errorCode" => "0000",
					"errorMsg" => "参数不正确",
					"otherMsg" => "" 
			);
			echo json_encode ( $return_data );
			die ();
		}
	}
	
	/**
	 * 1、删除购物车条目 薛骥 2015-10-16
	 *
	 * @param unknown $array        	
	 */
	public function DelCartItem($array) {
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
		
		if (array_key_exists ( "USERGUID", $array ) && array_key_exists ( "JSONDATA", $array )) {
// 			$user_guid = $array ['USERGUID'];//20151031zhouzhongkui
			$user_guid=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			$goods_list = $array ['JSONDATA'];
			$data_type = gettype ( $goods_list ); // 如果是字符转就不用decode
			
			if ($data_type == "string") {
				$goods_list = json_decode ( $goods_list, true );
			}
			
			$merIds = "";
			
			for($i = 0; $i < count ( $goods_list ); $i ++) {
				$merIds .= ("'" . $goods_list [$i] ['MERCHANDISEID'] . "',");
			}
			$merIds = substr ( $merIds, 0, - 1 );
			
			// 删除当前商品在购物车中的信息
			$delete_sql = " DELETE FROM shoppingcart WHERE merchandiseId in (" . $merIds . ") AND memberId ='" . $user_guid . "'";
			
			$this->db->query ( $delete_sql );
			
			$return_data ["success"] = "yes";
			$return_data ["errorCode"] = "0000";
			$return_data ["errorMsg"] = "";
		}
		echo json_encode ( $return_data );
		die ();
	}
	
	/**
	 * 3、收藏商品 薛骥 2015-10-16
	 *
	 * @param unknown $array        	
	 */
	public function AddMyFavour($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		if (array_key_exists ( 'USERGUID', $array ) && array_key_exists ( 'MERCHANDISEID', $array )) {
// 			$user_guid = $array ['USERGUID'];//20151031zhouzhongkui
			$user_guid=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			; // 用户guid
			$MERCHANDISEID = $array ['MERCHANDISEID']; // 商品GUID
			                                           // 查询收藏夹中的数据
			$sql = "SELECT * FROM favorites a WHERE a.memberId = '" . $user_guid . "' AND a.merchandiseId ='" . $MERCHANDISEID . "'";
			$result = $this->db->query ( $sql )->result_array ();
			if (count ( $result ) < 1) { // 收藏夹不存在该商品,添加进去
				$data = array (
						'ID' => uniqid (),
						'memberId' => $user_guid,
						'merchandiseId' => $MERCHANDISEID,
						'createDate' => date('Y-m-d H:i:s',time ()),
						'status' => 1 
				);
				$this->db->insert ( 'favorites', $data );
			}
			$return_data = array (
					"success" => "yes",
					"errorCode" => "0000",
					"errorMsg" => "",
					"otherMsg" => "" 
			);
			echo json_encode ( $return_data );
			die ();
		} else {
			$return_data = array (
					"success" => "no",
					"errorCode" => "0000",
					"errorMsg" => "数据格式不正确",
					"otherMsg" => "" 
			);
			echo json_encode ( $return_data );
			die ();
		}
	}
	/**
	 * 7、订单确认收货 薛骥 2015-10-16
	 *
	 * @param unknown $array        	
	 */
	public function ConfirmReceive($array) {
		if (! $array) {
			ReturnUnlogin ( "非法参数" );
			exit ();
		}
		if (array_key_exists ( "ORDERID", $array ) && array_key_exists ( "USERGUID", $array )) {
			$order_id = $array ['ORDERID'];
// 			$USERGUID = $array ['USERGUID'];//20151031zhouzhongkui
			$USERGUID=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			$this->db->where ( 'ID', $order_id );
			$this->db->where ( 'memberId', $USERGUID );
			$this->db->update ( 'medorder', array (
					'orderStatus' => 4 
			) ); // 待评价即为确认收货
			$return_data = array (
					"success" => "yes",
					"errorCode" => "",
					"errorMsg" => "",
					"otherMsg" => "" 
			);
			echo json_encode ( $return_data );
			die ();
		} else {
			$return_data = array (
					"success" => "no",
					"errorCode" => "0000",
					"errorMsg" => "参数不正确",
					"otherMsg" => "" 
			);
			echo json_encode ( $return_data );
			die ();
		}
	}
	/**
	 * 8、商品评价 薛骥 2015-10-16
	 *
	 * @param unknown $array        	
	 */
	public function AssessMerch($array) {

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

		if (array_key_exists ( "USERGUID", $array ) && array_key_exists ( "MERCHANDISEID", $array )) { // 判断评价人字段来判断格式是否正确
// 			$user_guid = $array ['USERGUID'];//20151031zhouzhongkui
			$user_guid=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			// 此处的id应该为商品id，但是因为前期时间紧张，在前端作了简化，直接传过来的订单id，然后对该订单下的全部商品进行评价
			// 到后期应该是对单个商品进行评价
			// 薛骥 2015-10-22
			$merchandise_id = $array ['MERCHANDISEID'];
			
			$dilevery_star = $array ['DILEVERYSTAR'];
			$quality_star = $array ['QUALITYSTAR'];
			$is_append = $array ['ISAPPEND'];
			$text = $array ['TEXT'];
			$sql = " SELECT distinct merchandiseid FROM orderdetail a WHERE a.orderid = '$merchandise_id'";
			
			$DB_data = $this->db->query ( $sql )->result_array ();
			for($i = 0; $i < count ( $DB_data ); $i ++) {
				$orderMerId = $DB_data [$i] ['merchandiseid'];
				
				$data = array (
						'ID' => uniqid (),
						'memberId' => $user_guid,
						'merchandiseId' => $orderMerId,
						'createDate' => date('Y-m-d H:i:s',time ()),
						'status' => 1,
						'isAppend' => $is_append,
						'assessTXT' => $text,
						'qualityLevel' => $quality_star,
						'dileveryLevel' => $dilevery_star 
				);
				$this->db->insert ( 'merassess', $data );
			}
			
			$update_sql = " UPDATE merchandise a SET a.assesscount=assesscount+1";
			$update_sql .= " WHERE id in (select merchandiseid from orderdetail where orderid='$merchandise_id') ";
			$this->db->query ( $update_sql );
			
			$update_sql = " UPDATE medorder set orderstatus=5 where id='$merchandise_id' ";
			$this->db->query ( $update_sql );
			
			$return_data ['success'] = 'yes';
			$return_data ['errorCode'] = '0000';
			$return_data ['errorMsg'] = '';
		}
		echo json_encode ( $return_data );
		die ();
	}
	/**
	 * 11、我的收藏 薛骥 2015-10-16
	 *
	 * @param unknown $array        	
	 */
	public function MyFavourite($array) {
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
		if (array_key_exists ( "USERGUID", $array )) {
// 			$USERGUID = $array ['USERGUID'];//20151031zhouzhongkui
			$USERGUID=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			$page = 0;
			$count = 10;
			if (array_key_exists ( 'PAGE', $array )) {
				$page = $array ['PAGE'];
			}
			
			if (array_key_exists ( 'COUNT', $array )) {
				$count = $array ['COUNT'];
			}
			$from = $page * $count; // 计算起始查询条数
			
			$sql = "SELECT ASSESSCOUNT,totleSaledCount AS SALECOUNT,";
			$sql .= " mer.id MERCHANDISEID,med.MEDICINENAME,med.field MEDICINESPEC,p.id SHOPID,p.pharmacyname SHOPNAME,p.pharmPic PICBIG,";
			$sql .= " p.pharmLogo PICSMALL,mer.PRICE,mer.PRICE MEMBERPRICE FROM favorites f, merchandise mer, medicine med, pharmacy p";
			$sql .= " WHERE f.memberid='$USERGUID' AND f.merchandiseid=mer.id AND mer.medicineid=med.id AND mer.pharmacyid=p.id AND f.status = 1 LIMIT $from,$count ";
			$return_data = $this->db->query ( $sql )->result_array ();
		}
		echo json_encode ( $return_data );
		die ();
	}
	/**
	 * 12、意见反馈 薛骥 2015-10-16
	 *
	 * @param unknown $array        	
	 */
	public function Feedback($array) {
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
		
		if (array_key_exists ( "USERGUID", $array ) && array_key_exists ( "JSONDATA", $array ) && array_key_exists ( "ADVICE", $array )) {
// 			$user_guid = $array ['USERGUID'];//20151031zhouzhongkui
			$user_guid=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			$text = $array ['ADVICE'];
			$TAG_list = $array ['JSONDATA'];
			$data_type = gettype ( $TAG_list ); // 如果是字符转就不用decode
			
			if ($data_type == "string") {
				$TAG_list = json_decode ( $TAG_list, true );
			}
			
			$feedbackId = uniqid ();
			$data = array (
					'ID' => $feedbackId,
					'memberId' => $user_guid,
					'createDate' => time (),
					'status' => 1,
					'feedbackTXT' => $text 
			);
			
			$this->db->trans_start ();
			$this->db->insert ( 'userFeedback', $data );
			
			for($i = 0; $i < count ( $TAG_list ); $i ++) {
				$feedbackTag = $TAG_list [$i] ['TAG'];
				
				$data = array (
						'ID' => uniqid (),
						'userFeedbackId' => $feedbackId,
						'createDate' => time (),
						'status' => 1,
						'feedbackTag' => $feedbackTag 
				);
				$this->db->insert ( 'feedbacktag', $data );
			}
			$this->db->trans_complete ();
			
			$return_data ["success"] = "yes";
			$return_data ["errorCode"] = "0000";
			$return_data ["errorMsg"] = "";
		}
		echo json_encode ( $return_data );
		die ();
	}
	/**
	 * 13、删除收藏商品 薛骥 2015-10-16
	 *
	 * @param unknown $array        	
	 */
	public function DelFavouriate($array) {
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
		
		if (array_key_exists ( "USERGUID", $array ) && array_key_exists ( "JSONDATA", $array )) {
// 			$user_guid = $array ['USERGUID'];//20151031zhouzhongkui
			$user_guid=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			$goods_list = $array ['JSONDATA'];
			$data_type = gettype ( $goods_list ); // 如果是字符转就不用decode
			
			if ($data_type == "string") {
				$goods_list = json_decode ( $goods_list, true );
			}
			
			$merIds = "";
			
			for($i = 0; $i < count ( $goods_list ); $i ++) {
				$merIds .= ("'" . $goods_list [$i] ['MERCHANDISEID'] . "',");
			}
			$merIds = substr ( $merIds, 0, - 1 );
			
			// 删除当前商品在购物车中的信息
			$delete_sql = " DELETE FROM favorites WHERE merchandiseId in (" . $merIds . ") AND memberId ='" . $user_guid . "'";
			
			$this->db->query ( $delete_sql );
			
			$return_data ["success"] = "yes";
			$return_data ["errorCode"] = "0000";
			$return_data ["errorMsg"] = "";
		}
		echo json_encode ( $return_data );
		die ();
	}
	
	/**
	 * 14、获取单个用户的单个商品评价信息 薛骥 2015-10-21
	 *
	 * @param unknown $array        	
	 */
	public function GetUserAssessment($array) {
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
		
		if (array_key_exists ( "MERCHANDISEID", $array ) && array_key_exists ( "USERGUID", $array )) {
			$return_data = array (
					"ADVICE" => "",
					"STARTLEVEL" => "",
					"ISAPPEND" => "",
					"ASSESSDATE" => "" 
			);
			
			$merchandise_id = $array ['MERCHANDISEID'];
// 			$USERGUID = $array ['USERGUID'];//20151031zhouzhongkui
			$USERGUID=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			$page = 0;
			$count = 10;
			if (array_key_exists ( 'PAGE', $array )) {
				$page = $array ['PAGE'];
			}
			
			if (array_key_exists ( 'COUNT', $array )) {
				$count = $array ['COUNT'];
			}
			$from = $page * $count; // 计算起始查询条数
			$sql = " SELECT CREATEDATE CREATEDATE,ISAPPEND,ASSESSTXT,QUALITYLEVEL,";
			$sql .= " DILEVERYLEVEL FROM merassess a WHERE a.`merchandiseId` = '$merchandise_id'and a.memberId = '$USERGUID' AND a.status = 1 LIMIT 0,1";
			
			$DB_data = $this->db->query ( $sql )->result_array ();
			
			if ($DB_data && count ( $DB_data ) > 0) {
				$return_data ["ADVICE"] = $DB_data [0] ["ASSESSTXT"];
				$return_data ["QUALITYLEVEL"] = $DB_data [0] ["QUALITYLEVEL"];
				$return_data ["DILEVERYLEVEL"] = $DB_data [0] ["DILEVERYLEVEL"];
				$return_data ["ISAPPEND"] = $DB_data [0] ["ISAPPEND"];
				$return_data ["ASSESSDATE"] = $DB_data [0] ["CREATEDATE"];
			}
		}
		echo json_encode ( $return_data );
		die ();
	}
	
	/**
	 * 16、保存用户信息 薛骥 2015-10-22
	 *
	 * @param unknown $array        	
	 */
	public function SaveUserInfo($array) {
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
		
		if (array_key_exists ( "USERGUID", $array ) && array_key_exists ( "JSONDATA", $array )) { // 判断评价人字段来判断格式是否正确
// 			$user_guid = $array ['USERGUID'];//20151031zhouzhongkui
			$user_guid=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			$JSONDATA = $array ['JSONDATA'];
			$data_type = gettype ( $JSONDATA );
			if ($data_type == "string") { // 如果是字符转就不用decode
				$JSONDATA = json_decode ( $JSONDATA, true );
			}
			$data = array (
					'memberNAME' => $JSONDATA ['PERSONNAME'],
					'GENDER' => $JSONDATA ['GENDER'],
					'worktype' => $JSONDATA ['JOB'],
					'BIRTHDAY' => $JSONDATA ['BIRTHDAY'],
					'HASSERIOUS' => $JSONDATA ['HASSERIOUS'],
					'HASINHERITED' => $JSONDATA ['HASINHERITED'],
					'HASGUOMIN' => $JSONDATA ['HASGUOMIN'],
					'FOODHOBBY' => $JSONDATA ['FOODHOBBY'],
					'SLEEPHOBBY' => $JSONDATA ['SLEEPHOBBY'],
					'OTHER' => $JSONDATA ['OTHER'] 
			);
			
			// echo json_encode ( $data );die;
			$sql="select ID from member where ID='".$user_guid."'";
			$result=$this->db->query($sql)->result_array();
			if($result&&count($result)>0){
				$this->db->where ( 'ID', $user_guid );
				$this->db->update ( 'member', $data );
	// 			echo json_encode ( $JSONDATA ['GENDER'] );
	// 			die ();
			}else{
				$data['ID']=$user_guid;
				$this->db->insert ( 'member', $data );
			}
				$row = $this->db->affected_rows ();
				if ($row == 1) {
					$return_data ['success'] = 'yes';
					$return_data ['errorCode'] = '0000';
					$return_data ['errorMsg'] = '';
				}

		}
		echo json_encode ( $return_data );
		die ();
	}
	/**
	 * 17、获取用户信息 薛骥 2015-10-21
	 *
	 * @param unknown $array        	
	 */
	public function GetUserInfo($array) {
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
		
		if (array_key_exists ( "USERGUID", $array )) {
// 			$USERGUID = $array ['USERGUID'];//20151031zhouzhongkui
			$USERGUID=$this->GetUserGuidinfo ();//20151031zhouzhongkui
			
			$return_data = array (
					"PERSONNAME" => "",
					"GENDER" => "",
					"JOB" => "",
					"BIRTHDAY" => "",
					"HASINHERITED" => "",
					"HASSERIOUS" => "",
					"HASGUOMIN" => "",
					"FOODHOBBY" => "",
					"SLEEPHOBBY" => "",
					"OTHER" => "" 
			);

//			$sql = " SELECT memberNAME,GENDER,worktype,BIRTHDAY,HASINHERITED,HASSERIOUS,HASGUOMIN,FOODHOBBY,SLEEPHOBBY,OTHER FROM member a WHERE a.Id = '$USERGUID' AND a.memberStatus = 1 LIMIT 0,1";
			$sql = " SELECT memberNAME,GENDER,worktype,BIRTHDAY,HASINHERITED,HASSERIOUS,HASGUOMIN,FOODHOBBY,SLEEPHOBBY,OTHER FROM member a WHERE a.Id = '".$USERGUID."'  LIMIT 1";
			$DB_data = $this->db->query ( $sql )->result_array ();
			if ($DB_data && count ( $DB_data ) > 0) {
				$return_data ["PERSONNAME"] = $DB_data [0] ["memberNAME"];
				$return_data ["GENDER"] = $DB_data [0] ["GENDER"];
				$return_data ["JOB"] = $DB_data [0] ["worktype"];
				$return_data ["BIRTHDAY"] = $DB_data [0] ["BIRTHDAY"];
				$return_data ["HASINHERITED"] = $DB_data [0] ["HASINHERITED"];
				$return_data ["HASSERIOUS"] = $DB_data [0] ["HASSERIOUS"];
				$return_data ["HASGUOMIN"] = $DB_data [0] ["HASGUOMIN"];
				$return_data ["FOODHOBBY"] = $DB_data [0] ["FOODHOBBY"];
				$return_data ["SLEEPHOBBY"] = $DB_data [0] ["SLEEPHOBBY"];
				$return_data ["OTHER"] = $DB_data [0] ["OTHER"];
			}
		}
		echo json_encode ( $return_data );
		die ();
	}
}