<?php

/**
 * Created by IntelliJ IDEA.
 * User: kelvinsun
 * Date: 2017/3/20
 * Time: 1:23
 */
class Orders_model extends CI_Model {
	public function __construct () {
		$this->load->database();
	}

	public function insert ($pet, $publisher) {
		if (isset($pet) && is_numeric($pet) && isset($publisher) && is_numeric($publisher)) {
			$data = array(
				"pets_id" => $pet,
				"users_id" => $publisher,
				"create_time" => $now = date("Y-m-d H-i-s",time())
			);

			if (!$this -> db -> insert("orders", $data)) {
				$error = $this->db->error();
				if (1062 == $error['code']) {
					return -1062;
				} else {
					return -1;
				}
			} else {
				return $this->db->insert_id();
			}
		} else {
			return -20;
		}
	}

	public function update ($id, $buyer) {
		if (isset($id) && is_numeric($id) && isset($buyer) && is_numeric($buyer)) {
			if (!$this -> db -> update("orders", array("buyers_id" => $buyer), array('id' => $id, 'deleted' => 0))) {
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

	public function delete ($id = "", $pet = "", $publisher = "", $buyer = "") {
		$where = array();
		if (isset($id) && '' !== $id) {
			$where['id'] = $id;
		} else if (isset($pet) && '' !== $pet) {
			$where['pets_id'] = $pet;
		} else if (isset($publisher) && '' !== $publisher) {
			$where['users_id'] = $publisher;
		} else if (isset($buyer) && '' !== $buyer) {
			$where['buyers_id'] = $buyer;
		} else {
			return -20;
		}
		if (!$this -> db -> update("orders", array("deleted" => 1), $where)) {
			$error = $this->db->error();
			return $error;
		} else if (0 < $this->db->affected_rows()) {
			return TRUE;
		} else {
			return -1066;
		}
	}

	public function get_list ($page, $where, $like) {
		if (isset($where) && !empty($where)) {
			$where = array_filter($where);
		}

		$this->db->select('orders.id');
		$this->db->join('pets', 'pets.id = orders.pets_id', 'left');
		$this->db->join('users u', 'u.id = orders.users_id', 'left');
		$this->db->join('users b', 'b.id = orders.buyers_id', 'left');

		$w = array('orders.deleted' => 0, 'pets.deleted' => 0);
		if (isset($where) && !empty($where)) {
			if (isset($where['id']) && '' !== $where['id']) {
				$w['orders.id'] = $where['id'];
				unset($where['id']);
			}
			if (isset($where['pet']) && '' !== $where['pet']) {
				$w['pets.id'] = $where['pet'];
				unset($where['pet']);
			}
			if (isset($where['buyer']) && '' !== $where['buyer']) {
				$w['b.id'] = $where['buyer'];
				unset($where['buyer']);
			}
			if (isset($where['publisher']) && '' !== $where['publisher']) {
				$w['u.id'] = $where['publisher'];
				unset($where['buyer']);
			}
			if (isset($where['adopted'])) {
				if (1 == $where['adopted']) {
					$w['orders.buyers_id !='] = NULL;
				} else if (0 == $where['adopted']) {
					$w['orders.buyers_id'] = NULL;
				}
				unset($where['adopted']);
			}
			$w = array_merge($w, $where);
		}
		$this->db->where($w);

		if (isset($like) && !empty($like)) {
			if (isset($like['pet']) && '' !== $like['pet']) {
				$this->db->like('pets.name', $like['pet'], 'both');
				$this->db->or_like('pets.description', $like['pet'], 'both');
			}
			if (isset($like['publisher']) && '' !== $like['publisher']) {
				$this->db->like('u.name', $like['publisher'], 'both');
				$this->db->or_like('u.description', $like['publisher'], 'both');
			}
			if (isset($like['buyer']) && '' !== $like['buyer']) {
				$this->db->like('b.name', $like['buyer'], 'both');
				$this->db->or_like('b.description', $like['buyer'], 'both');
			}
		}

		$count = $this->db->count_all_results('orders', TRUE);

		$this->db->select(
			'orders.id AS id, orders.create_time AS create_time, ' .
			'pets.id AS pets_id, pets.name AS pets_name, pets.description AS pets_description, ' .
			'pets.image AS pets_image, pets.type AS pets_type, pets.gender AS pets_gender, ' .
			'pets.birthday AS pets_birthday, pets.create_time AS pets_create_time, ' .
			'u.id AS publisher_id, u.name AS publisher_name, u.description AS publisher_description, ' .
			'u.image AS publisher_image, u.phone AS publisher_phone, u.county AS publisher_county, ' .
			'u.city AS publisher_city, u.road AS publisher_road, u.address AS publisher_address, ' .
			'u.user_id AS publisher_user_id, u.postcode AS publisher_postcode, ' .
			'b.id AS buyer_id, b.name AS buyer_name, b.description AS buyer_description, ' .
			'b.image AS buyer_image, b.phone AS buyer_phone, b.county AS buyer_county, ' .
			'b.city AS buyer_city, b.road AS buyer_road, b.address AS buyer_address, ' .
			'b.user_id AS buyer_user_id, b.postcode AS buyer_postcode '
		);
		$this->db->from('orders');
		$this->db->join('pets', 'pets.id = orders.pets_id', 'left');
		$this->db->join('users u', 'u.id = orders.users_id', 'left');
		$this->db->join('users b', 'b.id = orders.buyers_id', 'left');

		$this->db->where($w);

		if (isset($like) && !empty($like)) {
			if (isset($like['pet']) && '' !== $like['pet']) {
				$this->db->like('pets.name', $like['pet'], 'both');
				$this->db->or_like('pets.description', $like['pet'], 'both');
			}
			if (isset($like['publisher']) && '' !== $like['publisher']) {
				$this->db->like('u.name', $like['publisher'], 'both');
				$this->db->or_like('u.description', $like['publisher'], 'both');
			}
			if (isset($like['buyer']) && '' !== $like['buyer']) {
				$this->db->like('b.name', $like['buyer'], 'both');
				$this->db->or_like('b.description', $like['buyer'], 'both');
			}
		}

		if (isset($page) && isset($page['pageNumber']) && isset($page['pageSize'])) {
			$this->db->limit($page['pageSize'], $page['pageNumber'] * $page['pageSize']);
		}

		$query = $this->db->get();
		return array('list' => $query -> result_array(), 'total' => $count, 'query' => $this->db->last_query());
	}

	public function get_detail($id) {
		if (isset($id) && '' !== $id) {
			$this -> db -> select(
				'orders.id AS id, ' .
				'orders.create_time AS create_time, ' .
				'pets.id AS pets_id, ' .
				'pets.name AS pets_name, ' .
				'pets.description AS pets_description, ' .
				'pets.image AS pets_image, ' .
				'pets.type AS pets_type, ' .
				'pets.gender AS pets_gender, ' .
				'pets.birthday AS pets_birthday, ' .
				'pets.create_time AS pets_create_time, ' .
				'users.id AS users_id, ' .
				'users.name AS users_name, ' .
				'users.description AS users_description, ' .
				'u.image AS users_image, ' .
				'u.phone AS users_phone, ' .
				'u.county AS users_county, ' .
				'u.city AS users_city, ' .
				'u.road AS users_road, ' .
				'u.address AS users_address, ' .
				'u.user_id AS users_user_id, ' .
				'u.postcode AS users_postcode '
			);
			$this -> db -> from('orders');
			$this -> db -> join('users', 'users.id = orders.users_id');
			$this -> db -> join('pets', 'pets.id = orders.pets_id');
			$this -> db -> where('orders.id', $id);
			$this -> db -> limit(1);
			$query = $this -> db -> get();
			return $query -> row_array();
		} else {
			return array();
		}
	}

	public function get_list_by_user ($uid) {
		if (isset($uid) && '' !== $uid) {
			$this -> db -> select(
				'orders.id AS id, ' .
				'orders.create_time AS create_time, ' .
				'pets.id AS pets_id, ' .
				'pets.name AS pets_name, ' .
				'pets.description AS pets_description, ' .
				'pets.image AS pets_image, ' .
				'pets.type AS pets_type, ' .
				'pets.gender AS pets_gender, ' .
				'pets.birthday AS pets_birthday, ' .
				'pets.create_time AS pets_create_time, ' .
				'users.id AS users_id, ' .
				'users.name AS users_name, ' .
				'users.description AS users_description, ' .
				'u.image AS users_image, ' .
				'u.phone AS users_phone, ' .
				'u.county AS users_county, ' .
				'u.city AS users_city, ' .
				'u.road AS users_road, ' .
				'u.address AS users_address, ' .
				'u.user_id AS users_user_id, ' .
				'u.postcode AS users_postcode '
			);
			$this -> db -> from('orders');
			$this -> db -> join('users', 'users.id = orders.users_id');
			$this -> db -> join('pets', 'pets.id = orders.pets_id');
			$this -> db -> where('users.id', $uid);
			$query = $this -> db -> get();
//			echo $this->db->last_query();
			return $query -> result_array();
		} else {
			return array();
		}
	}
}
