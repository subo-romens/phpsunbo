<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH."core/Yjk_Controller.php");
class Returndata extends Yjk_Controller {
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
	public function index()
	{
			$type=$_POST['dataa'];//$_SERVER['REQUEST_METHOD'];
			echo json_encode($type);die;
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
