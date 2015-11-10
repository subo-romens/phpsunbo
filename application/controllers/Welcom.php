<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH."core/Yjk_Controller.php");
class Welcom extends Yjk_Controller {
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
		
		$this->load->driver('cache',array( 'adapter'=>'file', 'backup'=>'memcached'));
	 	 if($this->cache->memcached->is_supported()){
        echo "supported memcached";
        }else{
        echo "not supported memcached";
        }
        die;
		
	}
	
}
