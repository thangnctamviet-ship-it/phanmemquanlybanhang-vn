<?php 

class Model_products extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get the brand data */
	public function getProductData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM `products` where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM `products` ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getActiveProductData()
	{
		$sql = "SELECT * FROM `products` WHERE availability = ? ORDER BY id DESC";
		$query = $this->db->query($sql, array(1));
		return $query->result_array();
	}

	public function create($data)
	{
		if(!$data) return false;
		// Extract pivot data
		$brands     = $data['_brands']     ?? null; unset($data['_brands']);
		$categories = $data['_categories'] ?? null; unset($data['_categories']);
		$attrs      = $data['_attributes'] ?? null; unset($data['_attributes']);

		$ok = $this->db->insert('products', $data);
		if (!$ok) return false;
		$pid = $this->db->insert_id();
		$this->_syncPivot($pid, $brands, $categories, $attrs);
		return true;
	}

	public function update($data, $id)
	{
		if(!$data || !$id) return false;
		$brands     = $data['_brands']     ?? null; unset($data['_brands']);
		$categories = $data['_categories'] ?? null; unset($data['_categories']);
		$attrs      = $data['_attributes'] ?? null; unset($data['_attributes']);

		$this->db->where('id', $id)->update('products', $data);
		$this->_syncPivot($id, $brands, $categories, $attrs);
		return true;
	}

	/** Sync pivot tables (chỉ chạy nếu bảng pivot tồn tại — tương thích DB chưa migrate 004) */
	private function _syncPivot($product_id, $brands, $categories, $attrs)
	{
		if (!$this->db->table_exists('product_brands')) return;
		$product_id = (int)$product_id;

		if ($brands !== null) {
			$this->db->where('product_id', $product_id)->delete('product_brands');
			foreach ((array)$brands as $bid) {
				$bid = (int)$bid;
				if ($bid > 0) $this->db->insert('product_brands', array('product_id'=>$product_id,'brand_id'=>$bid));
			}
		}
		if ($categories !== null) {
			$this->db->where('product_id', $product_id)->delete('product_categories');
			foreach ((array)$categories as $cid) {
				$cid = (int)$cid;
				if ($cid > 0) $this->db->insert('product_categories', array('product_id'=>$product_id,'category_id'=>$cid));
			}
		}
		if ($attrs !== null) {
			$this->db->where('product_id', $product_id)->delete('product_attribute_values');
			foreach ((array)$attrs as $aid) {
				$aid = (int)$aid;
				if ($aid > 0) $this->db->insert('product_attribute_values', array('product_id'=>$product_id,'attribute_value_id'=>$aid));
			}
		}
	}

	/** Lấy IDs từ pivot, fallback JSON cũ trong products.brand_id/category_id/attribute_value_id */
	public function getRelatedIds($product_id, $kind)
	{
		$product_id = (int)$product_id;
		$map = array(
			'brands'     => array('table'=>'product_brands',            'col'=>'brand_id',           'legacy'=>'brand_id'),
			'categories' => array('table'=>'product_categories',        'col'=>'category_id',        'legacy'=>'category_id'),
			'attributes' => array('table'=>'product_attribute_values',  'col'=>'attribute_value_id', 'legacy'=>'attribute_value_id'),
		);
		if (!isset($map[$kind])) return array();
		$m = $map[$kind];

		if ($this->db->table_exists($m['table'])) {
			$rows = $this->db->query("SELECT `{$m['col']}` AS id FROM `{$m['table']}` WHERE product_id = ?", array($product_id))->result_array();
			if (!empty($rows)) return array_map(function($r){ return (int)$r['id']; }, $rows);
		}
		// Fallback: parse JSON từ cột TEXT cũ
		$r = $this->db->query("SELECT `{$m['legacy']}` AS v FROM `products` WHERE id = ?", array($product_id))->row_array();
		if (!$r || $r['v'] === null || $r['v'] === '') return array();
		$v = $r['v'];
		if (is_numeric($v)) return array((int)$v);
		$decoded = json_decode($v, true);
		if (!is_array($decoded)) return array();
		return array_map('intval', $decoded);
	}

	public function remove($id)
	{
		if($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('products');
			return ($delete == true) ? true : false;
		}
	}

	public function countTotalProducts()
	{
		$sql = "SELECT * FROM `products`";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

}