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

	/**
	 * 未登录 -1900
	 * 账户不存在 -1901
	 * @return int
	 */
	private function check_login_power () {
		$id = $this -> get_cookie_xss('uid');
		if (!isset($id) || empty($id) || '' === $id) {
			return -1900;
		}

		$data = $this -> users_model -> get_detail($id);

		if (empty($data)) {
			return -1901;
		} else {
			return $data['power'];
		}
	}

	public function get_detail () {
		$login = $this -> check_login_power();
		if (-1900 === $login) {
			$this -> error(array(
				"msg" => "no login",
			), -1900);
			return ;
		}
		if (-1901 === $login) {
			$this -> error(array(
				"msg" => "no account",
			), -1901);
			return ;
		}
		// 过滤掉了没有登录态的
		$id       = $this -> get_post_xss("id");
		// 过滤掉指定 id 获取但不是管理员的
		if ((isset($id) && !empty($id)) && 3 > $login) {
			$this -> error(array(
				"msg" => "no power",
			), -19);
			return ;
		}
		// 没有提供 id 的情况下就是获取自己的
		if (!isset($id) || empty($id)) {
			$id = $this -> get_cookie_xss('uid');
		}

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
		$login = $this -> check_login_power();
		if (-1900 === $login) {
			$this -> error(array(
				"msg" => "no login",
			), -1900);
			return ;
		}
		if (-1901 === $login) {
			$this -> error(array(
				"msg" => "no account",
			), -1901);
			return ;
		}
		if (3 > $login) {
			$this -> error(array(
				"msg" => "no power",
			), -19);
			return ;
		}
		$pageNumber = $this->get_post_xss('pageNumber');
		$pageSize   = $this->get_post_xss('pageSize');

		$id         = $this->get_post_xss('id');
		$power      = $this->get_post_xss('power');

		$search     = $this->get_post_xss('search');
		$where      = $this->get_post_xss('where') ?: '';
		$where      = json_decode($where, true);
		$where      = array_merge($where ?: array(), array('id' => $id, 'power' => $power));
		
		$data = $this->users_model->get_list(
			array('pageNumber' => $pageNumber, 'pageSize' => $pageSize),
			$where,
			$search
		);

		$this -> success($data);
	}

	public function login () {
		$account  = $this -> get_post_xss("account");
		$password = $this -> get_post_xss("password");

		$password = md5($password);

		$data = $this -> users_model -> check_account_password($account, $password);

		if (empty($data)) {
			$this -> error(array($account, $password), -1);
		} else {
			set_cookie("uid", $data['id'], 7200);
			$this -> success($data);
		}
	}

	public function logout () {
		delete_cookie("uid");
		$this -> success(array());
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

		$password = md5($password);
		
		$flag = $this -> users_model -> insert($account, $password, $name, $description);

		TRUE === $flag ? $this -> success(array()) : $this -> error(array(), $flag);
	}
	/**
	 * 1 普通人 (宠物: 看 / 领养 / 发布; 自己发布的宠物: 改 / 删)
	 * 2 商户 (宠物: 看 / 发布; 自己发布的宠物: 改 / 删)
	 * 3 管理员 (宠物: 看 / 删; 用户: 增 / 删 / 改 / 查)
	 */
	public function add () {
		$login = $this -> check_login_power();
		if (-1900 === $login) {
			$this -> error(array(
				"msg" => "no login",
			), -1900);
			return ;
		}
		if (-1901 === $login) {
			$this -> error(array(
				"msg" => "no account",
			), -1901);
			return ;
		}
		if (3 > $login) {
			$this -> error(array(
				"msg" => "no power",
			), -19);
			return ;
		}
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

		$password = md5($password);

		$flag = $this -> users_model -> insert(
			$account, $password, $name, $description,
			"", "", "", "",
			"", "", "", "",
			$power
		);

		TRUE === $flag ? $this -> success(array()) : $this -> error(array(), $flag);
	}
	/**
	 * -1066 是找不到想要修改的行
	 */
	public function modify () {
		$login = $this -> check_login_power();
		if (-1900 === $login) {
			$this -> error(array(
				"msg" => "no login",
			), -1900);
			return ;
		}
		if (-1901 === $login) {
			$this -> error(array(
				"msg" => "no account",
			), -1901);
			return ;
		}
		// 过滤掉了没有登录态的
		$id       = $this -> get_post_xss("id");
		// 过滤掉指定 id 修改但不是管理员的
		if ((isset($id) && !empty($id)) && 3 > $login) {
			$this -> error(array(
				"msg" => "no power",
			), -19);
			return ;
		}
		// 没有提供 id 的情况下就是改自己的
		if (!isset($id) || empty($id)) {
			$id = $this -> get_cookie_xss('uid');
		}

		$password    = $this -> get_post_xss("password");
		$name        = $this -> get_post_xss("name");
		$description = $this -> get_post_xss("description");
		$image       = $this -> get_post_xss("image");
		$phone       = $this -> get_post_xss("phone");
		$county      = $this -> get_post_xss("county");
		$city        = $this -> get_post_xss("city");
		$road        = $this -> get_post_xss("road");
		$address     = $this -> get_post_xss("address");
		$user_id     = $this -> get_post_xss("user_id");
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
		if ('' !== $power) {
			$data['$power'] = $power;
		}

		$data['name'] = $name;
		$data['description'] = $description;
		$data['image'] = $image;
		$data['phone'] = $phone;
		$data['county'] = $county;
		$data['city'] = $city;
		$data['road'] = $road;
		$data['address'] = $address;
		$data['user_id'] = $user_id;
		$data['postcode'] = $postcode;

		$data['password'] = md5($data['password']);

		$flag = $this -> users_model -> update($id, $data);

		TRUE === $flag ? $this -> success(array()) : $this -> error(array(), $flag);
	}

	public function delete () {
		$login = $this -> check_login_power();
		if (-1900 === $login) {
			$this -> error(array(
				"msg" => "no login",
			), -1900);
			return ;
		}
		if (-1901 === $login) {
			$this -> error(array(
				"msg" => "no account",
			), -1901);
			return ;
		}
		if (3 > $login) {
			$this -> error(array(
				"msg" => "no power",
			), -19);
			return ;
		}
		$id = $this -> get_post_xss("id");

		if ('' === $id) {
			$this -> error(array(), -20);
			return ;
		}

		$flag = $this -> users_model -> delete($id);

		TRUE === $flag ? $this -> success(array()) : $this -> error(array(), $flag);
	}
}
