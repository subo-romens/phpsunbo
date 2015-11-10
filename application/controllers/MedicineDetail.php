<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH."core/Yjk_Controller.php");
class MedicineDetail extends Yjk_Controller {
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
	public function index(){	
		$guid=$_GET['PZWH'];	
		//$url="http://ihealth.yiyao365.cn/index.php/Yp/getMedicineById/MedicineGuid/60476f1b2b2c48b3a7daf767e55a56bc";
		$url="http://ihealth.yiyao365.cn/index.php/GetZhunzi/GetValueFromZhunzi?PZWH=".$guid;
		$daa=$this->GetHandlerInfoBYGET($url);
		$data['da']=json_decode($daa,true);
// 		$da=json_decode($daa,true);
// 		print_r($da[0]['MedicineTitle']);
		$this->load->view('page/medicineDetail',$data);
	}
	
}
