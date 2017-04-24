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
	}

	public function get_list () {
		$pageNumber = $this->get_post_xss('pageNumber');
		$pageSize   = $this->get_post_xss('pageSize');

		$id         = $this->get_post_xss('id');
		$buyer      = $this->get_post_xss('buyer');
		$publisher  = $this->get_post_xss('publisher');

		$adopted    = $this->get_post_xss('adopted');
		$search     = $this->get_post_xss('search');

		$data = $this->pets_model->get_list(
			array('pageNumber' => $pageNumber, 'pageSize' => $pageSize),
			array('id' => $id, 'buyer' => $buyer, 'publisher' => $publisher, 'adopted' => $adopted),
			$search
		);

		$this -> success($data);
	}
	
	public function add () {
		$publisher   = $this -> get_post_xss('publisher');

		if ('' === $publisher) {
			$this -> error(array(), -20);
			return ;
		}

		$name        = $this -> get_post_xss("name");
		$description = $this -> get_post_xss("description");
		$image       = $this -> get_post_xss("image");

		//TODO 登录态与权限
		$flag = $this -> pets_model -> insert($name, $description, $image);

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
		$id       = $this -> get_post_xss("id");

		if ('' === $id) {
			$this -> error(array(), -20);
			return ;
		}

		$name        = $this -> get_post_xss("name");
		$description = $this -> get_post_xss("description");
		$image       = $this -> get_post_xss("image");
		$data = array();
		if ('' !== $name) {
			$data['$name'] = $name;
		}
		if ('' !== $description) {
			$data['$description'] = $description;
		}
		if ('' !== $image) {
			$data['image'] = $image;
		}

		//TODO 登录态与权限
		$flag = $this -> pets_model -> update($id, $data);

		TRUE === $flag ? $this -> success(array()) : $this -> error(array(), $flag);
	}

	public function delete () {
		$id = $this -> get_post_xss("id");

		if ('' === $id) {
			$this -> error(array(), -20);
			return ;
		}
		//TODO 登录态与权限
		$flag1 = $this -> pets_model -> delete($id);
		$flag2 = $this -> orders_model -> delete("", $id);

		TRUE === $flag1 && TRUE === $flag2 ? $this -> success(array()) : $this -> error(array(), $flag1 . $flag2);
	}
}
