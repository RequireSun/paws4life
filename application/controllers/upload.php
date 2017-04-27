<?php
/**
 * Created by IntelliJ IDEA.
 * User: kelvinsun
 * Date: 2017/4/27
 * Time: 23:06
 */
class upload extends MY_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function submit() {
		$file = $_FILES['upfile'];
		$name = rand(0,500000).dechex(rand(0,10000)) . $_FILES['upfile']['name'];
		move_uploaded_file($file['tmp_name'], FCPATH . "static" . DIRECTORY_SEPARATOR . "upload" . DIRECTORY_SEPARATOR . $name);
		//调用iframe父窗口的js 函数
		echo "<script>parent.stopSend('$name')</script>";
	}
};
