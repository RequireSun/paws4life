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
			'pets.image AS image, pets.create_time AS create_time, ' .
			'orders.id AS orders_id, orders.create_time AS orders_create_time, ' .
			'u.id AS publisher_id, u.name AS publisher_name, u.description AS publisher_description, ' .
			'b.id AS buyer_id, b.name AS buyer_name, b.description AS buyer_description '
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

	public function insert ($name, $description = "", $image = "") {
		if (isset($name) && '' !== $name) {
			$data = array(
				"name" => $name,
				"description" => $description,
				"image" => $image,
				"create_time" => $now = date("Y-m-d H-i-s",time())
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
			$values = array("name", "description", "image");

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
