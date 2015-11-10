<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH."core/Yjk_Controller.php");
class Welcome extends Yjk_Controller {
	public function __construct()
	{
		parent::__imghandle();
		$this->load->helper('common_helper');
	}
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function Savesearchlog($key,$id){
		date_default_timezone_set('PRC');
		$createtime=date('Y-m-d H:i:s',time());
		$guid=uniqid().time();
		$params = array(
			'ID'=>$guid,
			'memberId'=>$id,
			'keyword'=>$key,
			'createDate'=>$createtime
		);
		$this->load->model('Searchlog_model');
		$result = $this->Searchlog_model->insert($params);
	}
	
	public function index()
	{
		$sql="select * from medKind";
		$resul=$this->db->query($sql)->result_array();
		var_dump($resul);die;
		$this->Savesearchlog('感冒值', '');
		die;
		$page='0';//第几页 第一页为0
			$count='10';//多少条
			$data=array(
				'page'=>$page,
				'count'=>$count, 
				'orgguid'=>weborgguidweishop,
				'key'=>'感冒'
			);
			$url=weburlweishop.'API/GetSearchDrug';
			$resul=$this->GetRemotingHandlerInfo($url,$data);
			echo $resul;die;
				//		$valu='[{"GUID":"6ae09c349e1d4defa51e705b9c6e84d0","MEDICINETITLE":"\u4e09\u4e5d\u80c3\u6cf0\u9897\u7c92","PZWH":"\u56fd\u836f\u51c6\u5b57Z44020705","ENGLISHNAME":"","PRODUCTNAME":"","TYPE":"\u4e2d\u836f","OLDPZWH":"","APPLYDATE":"2010-06-07","BWM":"86900474000066","MEMO":"86900474000066[\u6bcf\u888b\u88c52.5g]\uff1b86900474000011[\u6bcf\u888b\u88c520g]","FACTORYNAME":"\u534e\u6da6\u4e09\u4e5d\u533b\u836f\u80a1\u4efd\u6709\u9650\u516c\u53f8","FACTORYADDRESS":"\u6df1\u5733\u5e02\u5b9d\u5b89\u533a\u89c2\u6f9c\u9ad8\u65b0\u6280\u672f\u4ea7\u4e1a\u56ed\u533a","JX":"\u9897\u7c92\u5242","GG":"\u6bcf\u888b\u88c52.5g\uff1b20g","CLASSID":"501e36b4-69d5-4763-8583-d7c21d01a62a","ZZ":"\u6e05\u70ed\u71e5\u6e7f\uff0c\u884c\u6c14\u6d3b\u8840\uff0c\u67d4\u809d\u6b62\u75db\u3002\u7528\u4e8e\u6e7f\u70ed\u5185\u8574\u3001\u6c14\u6ede\u8840\u7600\u6240\u81f4\u7684\u80c3\u75db\uff0c\u75c7\u89c1\u8118\u8179\u9690\u75db\u3001\u9971\u80c0\u53cd\u9178\u3001\u6076\u5fc3\u5455\u5410\u3001\u5608\u6742\u7eb3\u51cf\uff1b\u6d45\u8868\u6027\u80c3\u708e\u89c1\u4e0a\u8ff0\u8bc1\u5019\u8005\u3002","JJ":"","XHZY":"\u5982\u4e0e\u5176\u4ed6\u836f\u7269\u540c\u65f6\u4f7f\u7528\u53ef\u80fd\u4f1a\u53d1\u751f\u836f\u7269\u76f8\u4e92\u4f5c\u7528\uff0c\u8be6\u60c5\u8bf7\u54a8\u8be2\u533b\u5e08\u6216\u836f\u5e08\u3002","YLDL":"","BLFY":"","MEMO2":"1.\u5fcc\u98df\u8f9b\u8fa3\u523a\u6fc0\u6027\u98df\u7269\u30022.\u5fcc\u60c5\u7eea\u6fc0\u52a8\u6216\u751f\u95f7\u6c14\u30023.\u6d45\u8868\u6027\u3001\u7cdc\u70c2\u6027\u3001\u840e\u7f29\u6027\u7b49\u6162\u6027\u80c3\u708e\u5e94\u5728\u533b\u5e08\u6307\u5bfc\u4e0b\u670d\u7528\u30024.\u5b55\u5987\u5e94\u5728\u533b\u5e08\u6307\u5bfc\u4e0b\u670d\u7528\u30025.\u6162\u6027\u80c3\u708e\u60a3\u8005\u670d\u836f2\u5468\u75c7\u72b6\u65e0\u6539\u5584\uff0c\u5e94\u7acb\u5373\u505c\u836f\u5e76\u53bb\u533b\u9662\u5c31\u8bca\u30026.\u6309\u7167\u7528\u6cd5\u7528\u91cf\u670d\u7528\uff0c\u5c0f\u513f\u3001\u5e74\u8001\u4f53\u5f31\u8005\u5e94\u5728\u533b\u5e08\u6307\u5bfc\u4e0b\u670d\u7528\u30027.\u5bf9\u672c\u54c1\u8fc7\u654f\u8005\u7981\u7528\uff0c\u8fc7\u654f\u4f53\u8d28\u8005\u614e\u7528\u30028.\u672c\u54c1\u6027\u72b6\u53d1\u751f\u6539\u53d8\u65f6\u7981\u6b62\u4f7f\u7528\u30029.\u513f\u7ae5\u5fc5\u987b\u5728\u6210\u4eba\u76d1\u62a4\u4e0b\u4f7f\u7528\u300210.\u8bf7\u5c06\u672c\u54c1\u653e\u5728\u513f\u7ae5\u4e0d\u80fd\u63a5\u89e6\u7684\u5730\u65b9\u300211.\u5982\u6b63\u5728\u4f7f\u7528\u5176\u4ed6\u836f\u54c1\uff0c\u4f7f\u7528\u672c\u54c1\u524d\u8bf7\u54a8\u8be2\u533b\u5e08\u6216\u836f\u5e08\u3002","YFYL":"\u7528\u5f00\u6c34\u51b2\u670d\u3002\u4e00\u6b211\u888b\uff0c\u4e00\u65e52\u6b21\u3002","ZCFF":"\u5bc6\u5c01\u3002","PRICE":"10~9.9"}]';
				//		print_r(json_decode($valu));die;
				$gyzz='Z44020705';
				$url=webyaopinkuurl.$gyzz;
				$result=$this->GetHandlerInfoBYGET($url);
				var_dump($result);die;
				$res=json_decode($result);

				print_r($res);die;

				//		$time=time();
				//		$sql="select *,'".$time."' created,'".$time."' updated,'0' state from area ";
				//		$data=$this->db->query($sql)->result_array();
				//		echo json_encode($data);die;
				//		$key="感冒";
				//		$id="";
				//		$this->Savesearchlog($key, $id);
				//		die;
				//
				//		$urlall="http://wx.yiyao365.cn/index.php?g=Wap&m=Interface&a=GetSendMsgInfo&token=bqwyrl1435734590&wecha_id=123&time=1436514695";
				//		$result=$this->GetHandlerInfoBYGET($urlall);
				//		echo $result;die;
				$guid='感冒';//搜索关键字
				$sql=" select ID,diseaseName DISEASENAME,sortnumber SORTNUMBER,status STATUS,description DESCRIPTION,ifnull(created,0) CREATED,ifnull(updated,0) UPDATED from disease where diseaseName like '%".$guid."%'";
				$data=$this->db->query($sql)->result_array();

				echo json_encode($data);die;
				$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
				if ( ! $foo = $this->cache->get('foo'))
				{
					echo 'Saving to the cache!<br />';
					$foo = 'foobarbaz!';

					// Save into the cache for 5 minutes
					$this->cache->save('foo', $foo, 300);
				}

				echo $foo;die;
				$result=$this->getcache('foo');
				if($result){
					echo $result;
					die;
				}else{
					echo 'Saving to the cache!<br />';
					$result='foobarbaz';
					$this->setcache('foo', $result, cachetimecontinue);
					echo $result;
				}
				die;
				$data=array(
		'orgguid'=>weborgguidweishop
				);
				$url=weburlweishop.'API/getGoodsSort';
				$resul=$this->GetRemotingHandlerInfo($url,$data);
				$resul2=str_replace('null', '""',$resul);
				echo $resul2;die;
				$sql="select * from imuser";
				$table=$this->db->query($sql)->result_array();
				echo json_encode($table);die;
				$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
				if ( ! $foo = $this->cache->get('foo'))
				{
					echo 'Saving to the cache!<br />';
					$foo = 'foobarbaz!';

					// Save into the cache for 5 minutes
					$this->cache->save('foo', $foo, 300);
				}

				echo $foo;die;

				$data=array(
		'orgguid'=>weborgguidweishop
				);
				$url=weburlweishop.'API/getGoodsSort';
				$resul=$this->GetRemotingHandlerInfo($url,$data);
				echo $resul;die;
				//		echo json_encode(json_encode('asda'));die;

				//		$sql="select * from imuser";
				//		$table=$this->db->query($sql)->result_array();
				//		echo json_encode($table);die;
				//		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
				//		if ( ! $foo = $this->cache->get('foo'))
				//		{
				//			echo 'Saving to the cache!<br />';
					//			$foo = 'foobarbaz!';
					//
				//			// Save into the cache for 5 minutes
				//			$this->cache->save('foo', $foo, 300);
				//		}
				//
				//		echo $foo;

				//		$this->load->database();
				//		$sql="SELECT *  from imuser";
				//		$result=$this->db->query($sql)->result_array();
				//		echo json_encode($result);die;
				//		$newdata = array(
				//                   'username'  => 'johndoe',
				//                   'email'     => 'johndoe@some-site.com',
				//                   'logged_in' => TRUE
				//		);
				////
				//		$this->session->set_userdata($newdata);
				//		if(!isset($this->session->userdata['email'])){
				//			echo 'asd';die;
				//
				//		}else{
				//
				//			$orgguid=$this->session->userdata['email'];
				//			echo $orgguid;die;
				//		}
				//$this->load->view('welcome_message');
}
}
