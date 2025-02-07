<?php
/**
 * Class User
 *
 * Can be called using $this->load->model('user/user');
 *
 * @package Admin\Model\User
 */
class ModelUserUser extends Model {
	/**
	 * Add User
	 *
	 * @param array<string, mixed> $data array of data
	 *
	 * @return int returns the primary key of the new user record
	 *
	 * @example
	 *
	 * $user_data = [
	 *     'username'      => 'Username',
	 *     'user_group_id' => 1,
	 *     'password'      => '',
	 *     'firstname'     => 'John',
	 *     'lastname'      => 'Doe',
	 *     'email'         => 'demo@opencart.com',
	 *     'image'         => 'user_image',
	 *     'status'        => 0
	 * ];
	 *
	 * $this->load->model('user/user');
	 *
	 * $user_id = $this->model_user_user->addUser($user_data);
	 */
	public function addUser(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "user` SET `username` = '" . $this->db->escape((string)$data['username']) . "', `user_group_id` = '" . (int)$data['user_group_id'] . "', `password` = '" . $this->db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "', `firstname` = '" . $this->db->escape((string)$data['firstname']) . "', `lastname` = '" . $this->db->escape((string)$data['lastname']) . "', `email` = '" . $this->db->escape((string)$data['email']) . "', `image` = '" . $this->db->escape((string)$data['image']) . "', `status` = '" . (bool)($data['status'] ?? 0) . "', `date_added` = NOW()");

		return $this->db->getLastId();
	}

	/**
	 * Edit User
	 *
	 * @param int                  $user_id primary key of the user record
	 * @param array<string, mixed> $data    array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $user_data = [
	 *     'username'      => 'Username',
	 *     'user_group_id' => 1,
	 *     'password'      => '',
	 *     'firstname'     => 'John',
	 *     'lastname'      => 'Doe',
	 *     'email'         => 'demo@opencart.com',
	 *     'image'         => 'user_image',
	 *     'status'        => 1
	 * ];
	 *
	 * $this->load->model('user/user');
	 *
	 * $user_id = $this->model_user_user->editUser($user_data);
	 */
	public function editUser(int $user_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "user` SET `username` = '" . $this->db->escape((string)$data['username']) . "', `user_group_id` = '" . (int)$data['user_group_id'] . "', `firstname` = '" . $this->db->escape((string)$data['firstname']) . "', `lastname` = '" . $this->db->escape((string)$data['lastname']) . "', `email` = '" . $this->db->escape((string)$data['email']) . "', `image` = '" . $this->db->escape((string)$data['image']) . "', `status` = '" . (bool)($data['status'] ?? 0) . "' WHERE `user_id` = '" . (int)$user_id . "'");

		if ($data['password']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "user` SET `password` = '" . $this->db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "' WHERE `user_id` = '" . (int)$user_id . "'");
		}
	}

	/**
	 * Edit Password
	 *
	 * @param int    $user_id  primary key of the user record
	 * @param string $password
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $this->model_user_user->editPassword($user_id, $password);
	 */
	public function editPassword(int $user_id, $password): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "user` SET `password` = '" . $this->db->escape(password_hash(html_entity_decode($password, ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "', `code` = '' WHERE `user_id` = '" . (int)$user_id . "'");
	}

	/**
	 * Edit Code
	 *
	 * @param string $email
	 * @param string $code
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $this->model_user_user->editCode($email, $code);
	 */
	public function editCode(string $email, string $code): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "user` SET `code` = '" . $this->db->escape($code) . "' WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "'");
	}

	/**
	 * Delete User
	 *
	 * @param int $user_id primary key of the user record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $this->model_user_user->deleteUser($user_id);
	 */
	public function deleteUser(int $user_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "user` WHERE `user_id` = '" . (int)$user_id . "'");
	}

	/**
	 * Get User
	 *
	 * @param int $user_id primary key of the user record
	 *
	 * @return array<string, mixed> user record that has user ID
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $user_info = $this->model_user_user->getUser($user_id);
	 */
	public function getUser(int $user_id): array {
		$query = $this->db->query("SELECT *, (SELECT `ug`.`name` FROM `" . DB_PREFIX . "user_group` `ug` WHERE `ug`.`user_group_id` = `u`.`user_group_id`) AS `user_group` FROM `" . DB_PREFIX . "user` `u` WHERE `u`.`user_id` = '" . (int)$user_id . "'");

		return $query->row;
	}

	/**
	 * Get User By Username
	 *
	 * @param string $username
	 *
	 * @return array<string, mixed>
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $user_info = $this->model_user_user->getUserByUsername($username);
	 */
	public function getUserByUsername(string $username): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE `username` = '" . $this->db->escape($username) . "'");

		return $query->row;
	}

	/**
	 * Get User By Email
	 *
	 * @param string $email
	 *
	 * @return array<string, mixed>
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $user_info = $this->model_user_user->getUserByEmail($email);
	 */
	public function getUserByEmail(string $email): array {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "user` WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "'");

		return $query->row;
	}

	/**
	 * Get User By Code
	 *
	 * @param string $code
	 *
	 * @return array<string, mixed>
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $user_info = $this->model_user_user->getUserByCode($code);
	 */
	public function getUserByCode(string $code): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE `code` = '" . $this->db->escape($code) . "' AND `code` != ''");

		return $query->row;
	}

	/**
	 * Get Users
	 *
	 * @param array<string, mixed> $data array of filters
	 *
	 * @return array<int, array<string, mixed>> user records
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $results = $this->model_user_user->getUsers();
	 */
	public function getUsers(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "user`";

		$sort_data = [
			'username',
			'status',
			'date_added'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `username`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * Get Total Users
	 *
	 * @return int total number of user records
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $user_total = $this->model_user_user->getTotalUsers();
	 */
	public function getTotalUsers(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "user`");

		return (int)$query->row['total'];
	}

	/**
	 * Get Total Users By Group ID
	 *
	 * @param int $user_group_id primary key of the user group record
	 *
	 * @return int total number of user records that have user group ID
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $user_total = $this->model_user_user->getTotalUsersByGroupId($user_group_id);
	 */
	public function getTotalUsersByGroupId(int $user_group_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "user` WHERE `user_group_id` = '" . (int)$user_group_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Get Total Users By Email
	 *
	 * @param string $email
	 *
	 * @return int
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $user_total = $this->model_user_user->getTotalUsersByEmail($email);
	 */
	public function getTotalUsersByEmail(string $email): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "user` WHERE LCASE(`email`) = '" . $this->db->escape(oc_strtolower($email)) . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Add Login
	 *
	 * @param int                  $user_id primary key of the user record
	 * @param array<string, mixed> $data    array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $this->model_user_user->addLogin($user_id, $data);
	 */
	public function addLogin(int $user_id, array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "user_login` SET `user_id` = '" . (int)$user_id . "', `ip` = '" . $this->db->escape($data['ip']) . "', `user_agent` = '" . $this->db->escape($data['user_agent']) . "', `date_added` = NOW()");
	}

	/**
	 * Get Logins
	 *
	 * @param int $user_id primary key of the user record
	 * @param int $start
	 * @param int $limit
	 *
	 * @return array<int, array<string, mixed>> login records that have user ID
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $results = $this->model_user_user->getLogins($user_id, $start, $limit);
	 */
	public function getLogins(int $user_id, int $start = 0, int $limit = 10): array {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user_login` WHERE `user_id` = '" . (int)$user_id . "' LIMIT " . (int)$start . "," . (int)$limit);

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}

	/**
	 * Get Total Logins
	 *
	 * @param int $user_id primary key of the user record
	 *
	 * @return int total number of login records that have user ID
	 *
	 * @example
	 *
	 * $this->load->model('user/user');
	 *
	 * $login_total = $this->model_user_user->getTotalLogins($user_id);
	 */
	public function getTotalLogins(int $user_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "user_login` WHERE `user_id` = '" . (int)$user_id . "'");

		if ($query->num_rows) {
			return (int)$query->row['total'];
		} else {
			return 0;
		}
	}
}
