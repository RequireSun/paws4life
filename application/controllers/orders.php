<?php

/**
 * Created by IntelliJ IDEA.
 * User: kelvinsun
 * Date: 2017/3/20
 * Time: 1:35
 */
class orders extends MY_Controller {

	public function __construct() {
		parent::__construct();
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
		$pet        = $this->get_post_xss('pet');
		$buyer      = $this->get_post_xss('buyer');
		$publisher  = $this->get_post_xss('publisher');
		$adopted    = $this->get_post_xss('adopted');

		$searchPet       = $this->get_post_xss('searchPet');
		$searchPublisher = $this->get_post_xss('searchPublisher');
		$searchBuyer     = $this->get_post_xss('searchBuyer');

		$where = $this->get_post_xss('where') ?: '';
		$where = json_decode($where, true);
		$where = array_merge($where ?: array(), array('id' => $id, 'pet' => $pet, 'buyer' => $buyer, 'publisher' => $publisher, 'adopted' => $adopted));

		$data = $this->orders_model->get_list(
			array('pageNumber' => $pageNumber, 'pageSize' => $pageSize),
			$where,
			array('pet' => $searchPet, 'publisher' => $searchPublisher, 'buyer' => $searchBuyer)
		);

		$this -> success($data);
	}

	public function adopt () {
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
		$id    = $this->get_post_xss('id');
		$buyer = $this->get_cookie_xss('uid');

		if (isset($id) && is_numeric($id) && isset($buyer) && is_numeric($buyer)) {
			$flag = $this->orders_model->update($id, $buyer);
			TRUE === $flag ? $this -> success(array()) : $this -> error(array(), $flag);
		} else {
			$this->error(array(), -20);
		}
	}
//	public function getDetail () {
//		$id = $this -> get_post_xss("id");
//
//		$data = $this -> orders_model -> get_detail($id);
//
//		if (empty($data)) {
//			$this -> error($data, -1);
//		} else {
//			$this -> success($data);
//		}
//	}
//	/**
//	 * 获取用户列表, 后面肯定要改的
//	 */
//	public function getListByUser () {
//		$id = $this -> get_post_xss("id");
//
//		$data = $this -> orders_model -> get_list_by_user($id);
//
//		if (empty($data)) {
//			$this -> success(array());
//		} else {
//			$this -> success($data);
//		}
//	}
}
