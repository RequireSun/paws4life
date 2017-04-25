<?php

/**
 * Created by IntelliJ IDEA.
 * User: kelvinsun
 * Date: 2017/3/16
 * Time: 0:16
 */
class Users_model extends CI_Model {

	public function __construct () {
		$this->load->database();
	}

	public function get_detail ($id) {
		if (isset($id) && '' !== $id) {
			$this -> db -> select(
				'id, name, description, image, ' .
				'phone, country, city, road, ' .
				'address, user_id, postcode, power'
			);
			$query = $this -> db -> get_where('users', array('id' => $id, 'deleted' => 0), 1);
			return $query -> row_array();
		} else {
			return -20;
		}
	}

	public function get_list ($page, $where, $like) {
		if (isset($where) && !empty($where)) {
			$where = array_filter($where);
		}

		$this->db->select('id');

		$where['deleted'] = 0;
		$this->db->where($where);

		if (isset($like) && '' !== $like) {
			$this->db->like('name', $like, 'both');
			$this->db->or_like('description', $like, 'both');
		}

		$count = $this->db->count_all_results('users', TRUE);

		$this->db->select(
			'id, name, description, image, ' .
			'phone, country, city, road, ' .
			'address, user_id, postcode, power'
		);

		$where['deleted'] = 0;
		$this->db->where($where);

		if (isset($like) && '' !== $like) {
			$this->db->like('name', $like, 'both');
			$this->db->or_like('description', $like, 'both');
		}

		if (isset($page) && isset($page['pageNumber']) && isset($page['pageSize'])) {
			$this->db->limit($page['pageSize'], $page['pageNumber'] * $page['pageSize']);
		}
		$this -> db -> from('users');
		$query = $this->db->get();
		return array('list' => $query -> result_array(), 'total' => $count, 'query' => $this->db->last_query());
	}

	public function check_account_password ($account, $password) {
		if (isset($account) && isset($password) && '' !== $account && '' !== $password) {
			$this -> db -> select('id');
			$query = $this -> db -> get_where('users', array('account' => $account, 'password' => $password, 'deleted' => 0), 1);
			return $query -> row_array();
		} else {
			return array();
		}
	}

	public function insert (
		$account, $password, $name = "", $description = "",
		$image = "", $phone = "", $country = "", $city = "",
		$road = "", $address = "", $user_id = "", $postcode = "", $power = 1
	) {
		if (isset($account) && isset($password) && '' !== $account && '' !== $password) {
			$data = array(
				"account" => $account,
				"password" => $password,
				"name" => $name,
				"description" => $description,
				"image" => $image,
				"phone" => $phone,
				"country" => $country,
				"city" => $city,
				"road" => $road,
				"address" => $address,
				"user_id" => $user_id,
				"postcode" => $postcode,
				"power" => $power,
			);

			if (!$this -> db -> insert("users", $data)) {
				$error = $this->db->error();
				if (1062 == $error['code']) {
					return -1062;
				} else {
					return -1;
				}
			} else {
				return TRUE;
			}
		} else {
			return -20;
		}
	}

	public function update ($id, $changes) {
		if (isset($id) && '' !== $id && is_array($changes) && !empty($changes)) {
			$data = array();
			$values = array(
				"password", "name", "description", "image",
				"phone", "country", "city", "road", "address",
				"user_id", "postcode", "power"
			);

			foreach ($changes as $k => $v) {
				if (in_array($k, $values) && '' !== $v) {
					$data[$k] = $v;
				}
			}

			if (empty($data)) {
				return -20;
			}

			if (!$this -> db -> update("users", $data, array('id' => $id, 'deleted' => 0))) {
				$error = $this->db->error();
				return $error;
			} else if (0 < $this->db->affected_rows()) {
				return TRUE;
			} else {
				return -1066;
			}
		} else {
			return -20;
		}
	}

	public function delete ($id) {
		if (isset($id) && '' !== $id) {
			if (!$this -> db -> update("users", array("deleted" => 1), array("id" => $id))) {
				$error = $this->db->error();
				return $error;
			} else if (0 < $this->db->affected_rows()) {
				return TRUE;
			} else {
				return -1066;
			}
		} else {
			return -20;
		}
	}
}
