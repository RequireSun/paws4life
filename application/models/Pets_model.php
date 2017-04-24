<?php

/**
 * Created by IntelliJ IDEA.
 * User: kelvinsun
 * Date: 2017/3/20
 * Time: 1:52
 */
class Pets_model extends CI_Model {
	public function __construct () {
		$this->load->database();
	}

	public function check_is_owner ($uid, $id) {
		if (isset($uid) && '' !== $uid && isset($id) && '' !== $id) {
			$this->db->select('orders.id');
			$this->db->from('orders');
			$this->db->join('users', 'users.id = orders.users_id');
			$this->db->join('pets', 'pets.id = orders.pets_id');
			$this->db->where(array(
				'users.id' => $uid,
				'pets.id' => $id,
			));
			$query = $this->db->get();
			return array('result' => len($query -> result_array()) > 0, 'query' => $this->db->last_query());
		} else {
			return array('result' => false);
		}
	}

	public function get_list ($page, $where, $like) {
		if (isset($where) && !empty($where)) {
			$where = array_filter($where);
		}

		$this->db->select('pets.id');
		$this->db->join('orders', 'pets.id = orders.pets_id', 'left');
		$this->db->join('users u', 'u.id = orders.users_id', 'left');
		$this->db->join('users b', 'b.id = orders.buyers_id', 'left');

		$w = array('pets.deleted' => 0, 'orders.deleted' => 0);
		if (isset($where) && !empty($where)) {
			if (isset($where['id']) && '' !== $where['id']) {
				$w['pets.id'] = $where['id'];
			}
			if (isset($where['buyer']) && '' !== $where['buyer']) {
				$w['b.id'] = $where['buyer'];
			}
			if (isset($where['publisher']) && '' !== $where['publisher']) {
				$w['u.id'] = $where['publisher'];
			}
			if (isset($where['adopted'])) {
				if (1 == $where['adopted']) {
					$w['orders.buyers_id !='] = NULL;
				} else if (0 == $where['adopted']) {
					$w['orders.buyers_id'] = NULL;
				}
			}
		}
		$this->db->where($w);

		if (isset($like) && '' !== $like) {
			$this->db->like('pets.name', $like, 'both');
			$this->db->or_like('pets.description', $like, 'both');
		}

		$count = $this->db->count_all_results('pets', TRUE);

		$this->db->select(
			'pets.id AS id, pets.name AS name, pets.description AS description, ' .
			'pets.image AS image, pets.type AS type, pets.gender AS gender, ' .
			'pets.birthday AS birthday, pets.create_time AS create_time, ' .
			'orders.id AS orders_id, orders.create_time AS orders_create_time, ' .
			'u.id AS publisher_id, u.name AS publisher_name, u.description AS publisher_description, ' .
			'u.image AS publisher_image, u.phone AS publisher_phone, u.country AS publisher_country, ' .
			'u.user_id AS publisher_user_id, u.address AS publisher_address, u.postcode AS publisher_postcode, ' .
			'b.id AS buyer_id, b.name AS buyer_name, b.description AS buyer_description, ' .
			'b.image AS buyer_image, b.phone AS buyer_phone, b.country AS buyer_country, ' .
			'b.user_id AS buyer_user_id, b.address AS buyer_address, b.postcode AS buyer_postcode '

		);
		$this->db->from('pets');
		$this->db->join('orders', 'pets.id = orders.pets_id', 'left');
		$this->db->join('users u', 'u.id = orders.users_id', 'left');
		$this->db->join('users b', 'b.id = orders.buyers_id', 'left');

		$this->db->where($w);

		if (isset($like) && '' !== $like) {
			$this->db->like('pets.name', $like, 'both');
			$this->db->or_like('pets.description', $like, 'both');
		}

		if (isset($page) && isset($page['pageNumber']) && isset($page['pageSize'])) {
			$this->db->limit($page['pageSize'], $page['pageNumber'] * $page['pageSize']);
		}

		$query = $this->db->get();
		return array('list' => $query -> result_array(), 'total' => $count, 'query' => $this->db->last_query());
	}

	public function insert ($name, $description = "", $image = "", $type = "", $gender = "", $birthday = "") {
		if (isset($name) && '' !== $name) {
			$data = array(
				"name" => $name,
				"description" => $description,
				"image" => $image,
				"type" => $type,
				"gender" => $gender,
				"birthday" => $birthday,
				"create_time" => $now = date("Y-m-d H-i-s", time())
			);

			if (!$this -> db -> insert("pets", $data)) {
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

	public function update ($id, $changes) {
		if (isset($id) && '' !== $id && is_array($changes) && !empty($changes)) {
			$data = array();
			$values = array("name", "description", "image", "type", "gender", "birthday");

			foreach ($changes as $k => $v) {
				if (in_array($k, $values) && '' !== $v) {
					$data[$k] = $v;
				}
			}

			if (empty($data)) {
				return -20;
			}

			if (!$this -> db -> update("pets", $data, array('id' => $id, 'deleted' => 0))) {
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
			if (!$this -> db -> update("pets", array("deleted" => 1), array("id" => $id))) {
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

	public function get_pets_not_adopted () {
		$sql = <<<SQL
SELECT p.id AS id, p.name AS name, p.description AS description, p.image AS image, p.create_time AS create_time
FROM pets p
LEFT JOIN (SELECT DISTINCT pets_id FROM orders) o
ON p.id = o.pets_id
WHERE o.pets_id IS NULL;
SQL;
		$query = $this -> db -> query($sql);
		return $query -> result_array();
	}
}
