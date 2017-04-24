<?php

/**
 * Created by IntelliJ IDEA.
 * User: kelvinsun
 * Date: 2017/3/16
 * Time: 0:23
 */
class Users extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this -> load -> model("users_model");
	}

	public function get_detail () {
		$id = $this -> get_post_xss("id");

		if ('' === $id) {
			$this -> error(array(), -20);
			return ;
		}

		$data = $this -> users_model -> get_detail($id);

		if (empty($data)) {
			$this -> error($data, -1);
		} else {
			$this -> success($data);
		}
	}

	public function get_list () {
		$pageNumber = $this->get_post_xss('pageNumber');
		$pageSize   = $this->get_post_xss('pageSize');

		$id         = $this->get_post_xss('id');
		$power      = $this->get_post_xss('power');

		$search     = $this->get_post_xss('search');
		
		$data = $this->users_model->get_list(
			array('pageNumber' => $pageNumber, 'pageSize' => $pageSize),
			array('id' => $id, 'power' => $power),
			$search
		);

		$this -> success($data);
	}
	//TODO
	public function login () {
		$account  = $this -> get_post_xss("account");
		$password = $this -> get_post_xss("password");

		$data = $this -> users_model -> check_account_password($account, $password);

		if (empty($data)) {
			$this -> error($data, -1);
		} else {
			$this -> success($data);
		}
	}
	/**
	 * -1062 是已存在用户名
	 * 剩下的是其他错误
	 */
	public function register () {
		$account     = $this -> get_post_xss("account");
		$password    = $this -> get_post_xss("password");

		if ('' === $account || '' === $password) {
			$this -> error(array(), -20);
			return ;
		}

		$name        = $this -> get_post_xss("name");
		$description = $this -> get_post_xss("description");
		
		$flag = $this -> users_model -> insert($account, $password, $name, $description);

		TRUE === $flag ? $this -> success(array()) : $this -> error(array(), $flag);
	}
	/**
	 * 1 普通人 (宠物: 看 / 领养 / 发布; 自己发布的宠物: 改 / 删)
	 * 2 商户 (宠物: 看 / 发布; 自己发布的宠物: 改 / 删)
	 * 3 管理员 (宠物: 看 / 删; 用户: 增 / 删 / 改 / 查)
	 */
	public function add () {
		$account     = $this -> get_post_xss("account");
		$password    = $this -> get_post_xss("password");

		if ('' === $account || '' === $password) {
			$this -> error(array(), -20);
			return ;
		}

		$name        = $this -> get_post_xss("name");
		$description = $this -> get_post_xss("description");
		$power       = $this -> get_post_xss("power");
		// 最高也只能加商户, 更高的改 db 添加
		if (3 <= $power) {
			$power = 2;
		} else if (0 >= $power) {
			$power = 1;
		}
		//TODO 登录态与权限
		$flag = $this -> users_model -> insert($account, $password, $name, $description, "", "", "", "", "", "", $power);

		TRUE === $flag ? $this -> success(array()) : $this -> error(array(), $flag);
	}
	/**
	 * -1066 是找不到想要修改的行
	 */
	public function modify () {
		$id       = $this -> get_post_xss("id");

		if ('' === $id) {
			$this -> error(array(), -20);
			return ;
		}

		$password    = $this -> get_post_xss("password");
		$name        = $this -> get_post_xss("name");
		$description = $this -> get_post_xss("description");
		$image       = $this -> get_post_xss("image");
		$phone       = $this -> get_post_xss("phone");
		$country     = $this -> get_post_xss("country");
		$user_id     = $this -> get_post_xss("user_id");
		$address     = $this -> get_post_xss("address");
		$postcode    = $this -> get_post_xss("postcode");
		$power       = $this -> get_post_xss("power");
		// 最高也只能加商户, 更高的改 db 添加
		if ('' !== $power && 3 <= $power) {
			$power = 2;
		} else if ('' !== $power && 0 >= $power) {
			$power = 1;
		}
		$data = array();
		if ('' !== $password) {
			$data['password'] = $password;
		}
//		if ('' !== $name) {
//			$data['$name'] = $name;
//		}
//		if ('' !== $description) {
//			$data['$description'] = $description;
//		}
		if ('' !== $power) {
			$data['$power'] = $power;
		}
		
		$data['name'] = $name;
		$data['description'] = $description;
		$data['image'] = $image;
		$data['phone'] = $phone;
		$data['country'] = $country;
		$data['user_id'] = $user_id;
		$data['address'] = $address;
		$data['postcode'] = $postcode;

		//TODO 登录态与权限
		$flag = $this -> users_model -> update($id, $data);

		TRUE === $flag ? $this -> success(array()) : $this -> error(array(), $flag);
	}

	public function delete () {
		$id = $this -> get_post_xss("id");

		if ('' === $id) {
			$this -> error(array(), -20);
			return ;
		}
		//TODO 登录态与权限
		$flag = $this -> users_model -> delete($id);

		TRUE === $flag ? $this -> success(array()) : $this -> error(array(), $flag);
	}
}
