<?php

/**
 * Created by IntelliJ IDEA.
 * User: kelvinsun
 * Date: 2017/3/16
 * Time: 0:28
 */
class MY_Controller extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this ->load ->helper('url');
		$this ->load ->helper('cookie');
		$this ->load ->helper('common');
	}
	/**
	 * 从 GET: query / POST: body 中取值
	 * @param string $k 表单项名称
	 * @return string 取出来的值, 默认空字符串
	 */
	protected function get_post_xss($k) {
		return $this->input->get_post($k) ? (removeXSS($this->input->get_post($k))) : "";
	}
	/**
	 * 将页面以json格式输出
	 * @param string $data 数据
	 * */
	protected function json ($data) {
		$this->output->set_header("Content-Type:application/json; charset=utf-8");

		$data['serverTime'] = time();
		$res = encodeJson($data);

		$this->output->append_output($res);
	}
	protected function success ($data) {
		$res = array("result" => 0, "data" => $data);
		$this -> json($res);
	}
	protected function error ($data, $error_code) {
		$res = array("result" => $error_code, "data" => $data);
		$this -> json($res);
	}
}
