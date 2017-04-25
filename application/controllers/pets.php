<?php

/**
 * Created by IntelliJ IDEA.
 * User: kelvinsun
 * Date: 2017/3/20
 * Time: 2:06
 */
class pets extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this -> load -> model("pets_model");
		$this -> load -> model("orders_model");
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

	public function get_list () {
		$pageNumber = $this->get_post_xss('pageNumber');
		$pageSize   = $this->get_post_xss('pageSize');

		$id         = $this->get_post_xss('id');
		$buyer      = $this->get_post_xss('buyer');
		$publisher  = $this->get_post_xss('publisher');

		$adopted    = $this->get_post_xss('adopted');
		$search     = $this->get_post_xss('search');

		$where      = htmlspecialchars_decode(urldecode($this->get_post_xss('where') ?: ''));
		$where      = json_decode($where, true);
		$where      = array_merge($where, array('id' => $id, 'buyer' => $buyer, 'publisher' => $publisher, 'adopted' => $adopted));

		$data = $this->pets_model->get_list(
			array('pageNumber' => $pageNumber, 'pageSize' => $pageSize),
			$where,
			$search
		);

		$this -> success($data);
	}
	
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
		$publisher   = $this -> get_cookie_xss('uid');

		if ('' === $publisher) {
			$this -> error(array(), -20);
			return ;
		}

		$name        = $this -> get_post_xss("name");
		$description = $this -> get_post_xss("description");
		$image       = $this -> get_post_xss("image");
		$type        = $this -> get_post_xss("type");
		$gender      = $this -> get_post_xss("gender");
		$birthday    = $this -> get_post_xss("birthday");

		$flag = $this -> pets_model -> insert($name, $description, $image, $type, $gender, $birthday);

		if (0 >= $flag) {
			$this -> error(array(), $flag);
			return ;
		}

		$flag = $this -> orders_model -> insert($flag, $publisher);

		0 < $flag ? $this -> success(array("id" => $flag)) : $this -> error(array(), $flag);
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
		$id       = $this -> get_post_xss("id");

		if ('' === $id) {
			$this -> error(array(), -20);
			return ;
		}

		$check_res = $this -> pets_model -> check_is_owner($this -> get_cookie_xss('uid'), $id);
		if (!$check_res['result'] && 3 > $login) {
			$this -> error(array(
				"msg" => "no power",
			), -19);
			return ;
		}

		$name        = $this -> get_post_xss("name");
		$description = $this -> get_post_xss("description");
		$image       = $this -> get_post_xss("image");
		$type        = $this -> get_post_xss("type");
		$gender      = $this -> get_post_xss("gender");
		$birthday    = $this -> get_post_xss("birthday");
		$data = array(
			"name" => $name,
			"description" => $description,
			"image" => $image,
			"type" => $type,
			"gender" => $gender,
			"birthday" => $birthday
		);

		$flag = $this -> pets_model -> update($id, $data);

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

		$id = $this -> get_post_xss("id");

		if ('' === $id) {
			$this -> error(array(), -20);
			return ;
		}

		$check_res = $this -> pets_model -> check_is_owner($this -> get_cookie_xss('uid'), $id);
		if (!$check_res['result'] && 3 > $login) {
			$this -> error(array(
				"msg" => "no power",
			), -19);
			return ;
		}

		$flag1 = $this -> pets_model -> delete($id);
		$flag2 = $this -> orders_model -> delete("", $id);

		TRUE === $flag1 && TRUE === $flag2 ? $this -> success(array()) : $this -> error(array(), $flag1 . $flag2);
	}
}
