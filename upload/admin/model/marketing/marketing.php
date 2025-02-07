<?php
/**
 * Class Marketing
 *
 * Can be called using $this->load->model('marketing/marketing');
 *
 * @package Admin\Model\Marketing
 */
class ModelMarketingMarketing extends Model {
	/**
	 * Add Marketing
	 *
	 * @param array<string, mixed> $data array of data
	 *
	 * @return int returns the primary key of the new coupon record
	 *
	 * @example
	 *
	 * $marketing_data = [
	 *     'name'        => 'Marketing Name',
	 *     'description' => 'Marketing Description',
	 *     'code'        => ''
	 * ];
	 *
	 * $this->load->model('marketing/marketing');
	 *
	 * $marketing_id = $this->model_marketing_marketing->addMarketing($marketing_data);
	 */
	public function addMarketing(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "marketing` SET `name` = '" . $this->db->escape($data['name']) . "', `description` = '" . $this->db->escape($data['description']) . "', `code` = '" . $this->db->escape($data['code']) . "', `date_added` = NOW()");

		return $this->db->getLastId();
	}

	/**
	 * Edit Marketing
	 *
	 * @param int                  $marketing_id primary key of the marketing record
	 * @param array<string, mixed> $data         array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $marketing_data = [
	 *     'name'        => 'Marketing Name',
	 *     'description' => 'Marketing Description',
	 *     'code'        => ''
	 * ];
	 *
	 * $this->load->model('marketing/marketing');
	 *
	 * $this->model_marketing_marketing->editMarketing($marketing_id, $marketing_data);
	 */
	public function editMarketing(int $marketing_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "marketing` SET `name` = '" . $this->db->escape($data['name']) . "', `description` = '" . $this->db->escape($data['description']) . "', `code` = '" . $this->db->escape($data['code']) . "' WHERE `marketing_id` = '" . (int)$marketing_id . "'");
	}

	/**
	 * Delete Marketing
	 *
	 * @param int $marketing_id primary key of the marketing record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('marketing/marketing');
	 *
	 * $this->model_marketing_marketing->deleteMarketing($marketing_id);
	 */
	public function deleteMarketing(int $marketing_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "marketing` WHERE `marketing_id` = '" . (int)$marketing_id . "'");
	}

	/**
	 * Get Marketing
	 *
	 * @param int $marketing_id primary key of the marketing record
	 *
	 * @return array<string, mixed> marketing record that has marketing ID
	 *
	 * @example
	 *
	 * $this->load->model('marketing/marketing');
	 *
	 * $marketing_info = $this->model_marketing_marketing->getMarketing($marketing_id);
	 */
	public function getMarketing(int $marketing_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "marketing` WHERE `marketing_id` = '" . (int)$marketing_id . "'");

		return $query->row;
	}

	/**
	 * Get Marketing By Code
	 *
	 * @param string $code
	 *
	 * @return array<string, mixed>
	 *
	 * @example
	 *
	 * $this->load->model('marketing/marketing');
	 *
	 * $marketing_info = $this->model_marketing_marketing->getMarketingByCode($code);
	 */
	public function getMarketingByCode(string $code): array {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "marketing` WHERE `code` = '" . $this->db->escape($code) . "'");

		return $query->row;
	}

	/**
	 * Get Marketing(s)
	 *
	 * @param array<string, mixed> $data array of filters
	 *
	 * @return array<int, array<string, mixed>> marketing records
	 *
	 * @example
	 *
	 * $filter_data = [
	 *     'filter_name'      => 'Marketing Name',
	 *     'filter_code'      => '',
	 *     'filter_date_from' => '2021-01-01',
	 *     'filter_date_to'   => '2021-01-31',
	 *     'sort'             => 'm.name',
	 *     'order'            => 'DESC',
	 *     'start'            => 0,
	 *     'limit'            => 10
	 * ];
	 *
	 * $this->load->model('marketing/marketing');
	 *
	 * $results = $this->model_marketing_marketing->getMarketings($filter_data);
	 */
	public function getMarketings(array $data = []): array {
		$implode = [];

		$order_statuses = (array)$this->config->get('config_complete_status');

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "`o`.`order_status_id` = '" . (int)$order_status_id . "'";
		}

		$sql = "SELECT *, (SELECT COUNT(*) FROM `" . DB_PREFIX . "order` `o` WHERE (" . implode(" OR ", $implode) . ") AND `o`.`marketing_id` = `m`.`marketing_id`) AS `orders` FROM `" . DB_PREFIX . "marketing` m";

		$implode = [];

		if (!empty($data['filter_name'])) {
			$implode[] = "`m`.`name` LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_code'])) {
			$implode[] = "`m`.`code` = '" . $this->db->escape($data['filter_code']) . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(`m`.`date_added`) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = [
			'm.name',
			'm.code',
			'm.date_added'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `m`.`name`";
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
	 * Get Total Marketing(s)
	 *
	 * @param array<string, mixed> $data array of filters
	 *
	 * @return int total number of marketing records
	 *
	 * @example
	 *
	 * $filter_data = [
	 *     'filter_name'      => 'Marketing Name',
	 *     'filter_code'      => '',
	 *     'filter_date_from' => '2021-01-01',
	 *     'filter_date_to'   => '2021-01-31',
	 *     'sort'             => 'm.name',
	 *     'order'            => 'DESC',
	 *     'start'            => 0,
	 *     'limit'            => 10
	 * ];
	 *
	 * $this->load->model('marketing/marketing');
	 *
	 * $marketing_total = $this->model_marketing_marketing->getTotalMarketings($filter_data);
	 */
	public function getTotalMarketings(array $data = []): int {
		$sql = "SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "marketing`";

		$implode = [];

		if (!empty($data['filter_name'])) {
			$implode[] = "`name` LIKE '" . $this->db->escape($data['filter_name']) . "'";
		}

		if (!empty($data['filter_code'])) {
			$implode[] = "`code` = '" . $this->db->escape($data['filter_code']) . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(`date_added`) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return (int)$query->row['total'];
	}
}
