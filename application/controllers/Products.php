<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Sản phẩm';

		$this->load->model('model_products');
		$this->load->model('model_brands');
		$this->load->model('model_category');
		$this->load->model('model_stores');
		$this->load->model('model_attributes');
	}

    /*
    * It only redirects to the manage product page
    */
	public function index()
	{
        if(!in_array('viewProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->render_template('products/index', $this->data);
	}

	/* ============ NHẬP HÀNG LOẠT TỪ EXCEL ============ */

	/** Trang giao diện nhập sản phẩm từ Excel */
	public function import()
	{
		if (!in_array('createProduct', $this->permission)) { redirect('dashboard','refresh'); }
		$this->data['stores'] = $this->model_stores->getActiveStore();
		$this->render_template('products/import', $this->data);
	}

	/** Tải file mẫu .xls (cột bắt buộc đánh dấu *) */
	public function importTemplate()
	{
		if (!in_array('createProduct', $this->permission)) { redirect('dashboard','refresh'); }
		// mỗi cột: [nhãn, bắt buộc?]
		$cols = array(
			array('Tên sản phẩm *', true), array('SKU / Mã hàng *', true),
			array('Giá bán *', true), array('Số lượng tồn *', true),
			array('Mã vạch (barcode)', false), array('Đơn vị tính', false),
			array('Giá vốn', false), array('Giá sỉ', false),
			array('Danh mục', false), array('Thương hiệu', false),
			array('Tồn tối thiểu', false), array('Mô tả', false)
		);
		$samples = array(
			array('Cà phê sữa lon','CF001','12000','100','8930001','Lon','8000','10000','Đồ uống','Highlands','10','Cà phê sữa đá đóng lon'),
			array('Bánh mì ngọt','BM002','15000','50','8930002','Cái','9000','13000','Thực phẩm','ABC Bakery','5',''),
		);
		$this->_renderTemplateXls('mau-nhap-san-pham.xls', $cols, $samples,
			'Danh mục / Thương hiệu nhập theo TÊN, hệ thống tự tạo nếu chưa có.');
	}

	/** Nhận JSON rows từ client (SheetJS đã parse), insert hàng loạt */
	public function importBulk()
	{
		if (!in_array('createProduct', $this->permission)) { echo json_encode(array('ok'=>false,'error'=>'Không có quyền')); return; }
		$raw = file_get_contents('php://input');
		$payload = json_decode($raw, true);
		if (!is_array($payload) || empty($payload['rows'])) { echo json_encode(array('ok'=>false,'error'=>'Không có dữ liệu')); return; }
		$rows = $payload['rows'];
		$store_id = (int)($payload['store_id'] ?? 0);
		if (count($rows) > 2000) { echo json_encode(array('ok'=>false,'error'=>'Tối đa 2000 dòng mỗi lần. Vui lòng chia nhỏ file.')); return; }

		$added = 0; $skipped = 0; $errors = array();
		$seen_sku = array();
		foreach ($rows as $i => $r) {
			$line = $i + 2; // dòng Excel (1 header)
			$name = trim((string)($r['name'] ?? ''));
			$sku  = trim((string)($r['sku'] ?? ''));
			$price = $this->_num($r['price'] ?? '');
			$qty   = $this->_num($r['qty'] ?? '');
			if ($name === '' || $sku === '') { $errors[] = "Dòng $line: thiếu Tên hoặc SKU"; continue; }
			if ($price === null) { $errors[] = "Dòng $line: Giá bán không hợp lệ"; continue; }
			// dedupe trong file
			$skl = mb_strtolower($sku);
			if (isset($seen_sku[$skl])) { $skipped++; continue; }
			$seen_sku[$skl] = true;
			// trùng DB
			$dup = $this->db->get_where('products', array('sku'=>$sku, 'deleted_at'=>null))->row_array();
			if ($dup) { $skipped++; continue; }

			$cat_id   = $this->_findOrCreate('category', trim((string)($r['category'] ?? '')));
			$brand_id = $this->_findOrCreate('brand',    trim((string)($r['brand'] ?? '')));

			$data = array(
				'name' => $name, 'sku' => $sku,
				'price' => (string)$price, 'qty' => (string)($qty===null?0:$qty),
				'image' => '', 'description' => trim((string)($r['description'] ?? '')),
				'attribute_value_id' => json_encode(array()),
				'brand_id' => json_encode($brand_id ? array($brand_id) : array()),
				'category_id' => json_encode($cat_id ? array($cat_id) : array()),
				'store_id' => $store_id,
				'availability' => 1,
				'_brands'     => $brand_id ? array($brand_id) : array(),
				'_categories' => $cat_id ? array($cat_id) : array(),
				'_attributes' => array(),
			);
			foreach (array('barcode'=>'barcode','unit'=>'unit','cost_price'=>'cost_price','wholesale_price'=>'wholesale_price','min_stock'=>'min_stock') as $key=>$col) {
				if (!$this->db->field_exists($col, 'products')) continue;
				$v = $r[$key] ?? '';
				if ($key==='cost_price'||$key==='wholesale_price') { $vn=$this->_num($v); if($vn!==null) $data[$col]=$vn; }
				elseif ($key==='min_stock') { $vn=$this->_num($v); if($vn!==null) $data[$col]=(int)$vn; }
				else { if (trim((string)$v)!=='') $data[$col]=trim((string)$v); }
			}
			if ($this->model_products->create($data)) $added++;
			else $errors[] = "Dòng $line: lỗi khi lưu";
		}
		echo json_encode(array('ok'=>true,'added'=>$added,'skipped'=>$skipped,'errors'=>$errors));
	}

	/**
	 * Render file .xls mẫu dùng chung: cột bắt buộc tô chữ đỏ + 20 dòng trống
	 * để khách bôi đen kéo/dán thêm hàng dễ dàng.
	 * @param array $cols mỗi phần tử [nhãn, bool bắt buộc]
	 */
	private function _renderTemplateXls($filename, $cols, $samples, $extraNote = '')
	{
		header('Content-Type: application/vnd.ms-excel; charset=utf-8');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		echo "\xEF\xBB\xBF";
		$h = function($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
		echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="utf-8"></head><body>';
		echo '<table border="1" cellspacing="0" cellpadding="4">';
		// Hàng tiêu đề: cột bắt buộc chữ đỏ + nền vàng nhạt, cột thường chữ đen + nền xanh nhạt
		echo '<tr style="font-weight:bold;">';
		foreach ($cols as $c) {
			$req = !empty($c[1]);
			$bg  = $req ? '#ffe4e6' : '#dbeafe';
			$fg  = $req ? '#dd0000' : '#1e293b';
			echo '<td style="background:'.$bg.';color:'.$fg.';">'.$h($c[0]).'</td>';
		}
		echo '</tr>';
		// 2 dòng ví dụ (chữ xám nhạt để biết là mẫu)
		foreach ($samples as $row){
			echo '<tr style="color:#94a3b8;">';
			foreach ($row as $cell) echo '<td>'.$h($cell).'</td>';
			echo '</tr>';
		}
		// 20 dòng trống cho khách dán dữ liệu
		$ncol = count($cols);
		for ($i=0; $i<20; $i++){
			echo '<tr>';
			for ($j=0; $j<$ncol; $j++) echo '<td></td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '<p style="color:#dd0000;font-size:13px;font-weight:bold;">Cột chữ ĐỎ = bắt buộc phải điền.</p>';
		echo '<p style="color:#64748b;font-size:12px;">Xóa 2 dòng ví dụ (chữ xám) trước khi nhập thật. '.$h($extraNote).'</p>';
		echo '<p style="color:#64748b;font-size:12px;">Mẹo: đã có sẵn 20 dòng trống. Cần thêm thì bôi đen các dòng trống rồi kéo ô vuông nhỏ góc dưới-phải xuống để tạo thêm hàng.</p>';
		echo '</body></html>';
	}

	/** Chuẩn hoá số: bỏ dấu chấm/phẩy ngăn cách, trả float hoặc null nếu rỗng/sai */
	private function _num($v)
	{
		$v = trim((string)$v);
		if ($v === '') return 0.0;
		$v = preg_replace('/[^\d,.\-]/', '', $v);
		// nếu có cả . và , — coi . là ngăn cách nghìn (vd 1.200,50)
		if (strpos($v,',')!==false && strpos($v,'.')!==false) { $v = str_replace('.','',$v); $v = str_replace(',','.',$v); }
		else { $v = str_replace(',', strpos($v,',')!==false && substr_count($v,',')==1 ? '.' : '', $v); }
		if (!is_numeric($v)) return null;
		return (float)$v;
	}

	/** Tìm category/brand theo tên, tạo mới nếu chưa có. Trả id hoặc 0 */
	private function _findOrCreate($kind, $name)
	{
		$name = trim($name);
		if ($name === '') return 0;
		$table = $kind === 'category' ? 'categories' : 'brands';
		if (!$this->db->table_exists($table)) return 0;
		$row = $this->db->get_where($table, array('name'=>$name))->row_array();
		if ($row) return (int)$row['id'];
		$ins = array('name'=>$name);
		if ($this->db->field_exists('active', $table)) $ins['active'] = 1;
		$this->db->insert($table, $ins);
		return (int)$this->db->insert_id();
	}

    /*
    * It Fetches the products data from the product table
    * this function is called from the datatable ajax function
    */
	public function fetchProductData()
	{
		$result = array('data' => array());

		$data = $this->model_products->getProductData();

		foreach ($data as $key => $value) {

            $store_data = $this->model_stores->getStoresData($value['store_id']);
			// button
            $buttons = '';
            if(in_array('updateProduct', $this->permission)) {
			$buttons .= '<a href="'.base_url('products/update/'.$value['id']).'" class="btn btn-default"><i class="fa fa-pencil"></i></a>';
            }

            if(in_array('deleteProduct', $this->permission)) {
			$buttons .= ' <button type="button" class="btn btn-default" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
            }


			$img = '<img src="'.base_url($value['image']).'" alt="'.$value['name'].'" class="img-circle" width="50" height="50" />';

            $availability = ($value['availability'] == 1) ? '<span class="label label-success">Hoạt động</span>' : '<span class="label label-warning">Không hoạt động</span>';

            $qty_status = '';
            if($value['qty'] <= 10) {
                $qty_status = '<span class="label label-warning">Sắp hết !</span>';
            } else if($value['qty'] <= 0) {
                $qty_status = '<span class="label label-danger">Hết hàng !</span>';
            }


			$result['data'][$key] = array(
				$img,
				$value['sku'],
				$value['name'],
				$value['price'],
                $value['qty'] . ' ' . $qty_status,
                $store_data['name'],
				$availability,
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

    /*
    * If the validation is not valid, then it redirects to the create page.
    * If the validation for each input field is valid then it inserts the data into the database
    * and it stores the operation message into the session flashdata and display on the manage product page
    */
	public function create()
	{
		if(!in_array('createProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->form_validation->set_rules('product_name', 'Tên sản phẩm', 'trim|required');
		$this->form_validation->set_rules('sku', 'SKU', 'trim|required');
		$this->form_validation->set_rules('price', 'Giá', 'trim|required');
		$this->form_validation->set_rules('qty', 'SL', 'trim|required');
        $this->form_validation->set_rules('store', 'Cửa hàng', 'trim|required');
		$this->form_validation->set_rules('availability', 'Tình trạng', 'trim|required');


        if ($this->form_validation->run() == TRUE) {
            // true case
	$upload_image = $this->upload_image();

	$data = array(
		'name' => $this->input->post('product_name'),
		'sku' => $this->input->post('sku'),
		'price' => $this->input->post('price'),
		'qty' => $this->input->post('qty'),
		'image' => $upload_image,
		'description' => $this->input->post('description'),
		'attribute_value_id' => json_encode($this->input->post('attributes_value_id')),
		'brand_id' => json_encode($this->input->post('brands')),
		'category_id' => json_encode($this->input->post('category')),
                'store_id' => $this->input->post('store'),
		'availability' => $this->input->post('availability'),
		// Pass arrays cho pivot (model sẽ tách ra)
		'_brands'     => $this->input->post('brands'),
		'_categories' => $this->input->post('category'),
		'_attributes' => $this->input->post('attributes_value_id'),
	);

	// Trường nâng cao (chỉ thêm nếu cột tồn tại — tương thích DB chưa migrate)
	foreach (array('barcode','unit','cost_price','wholesale_price','min_stock','max_stock','weight','has_batches','has_variants') as $col) {
		if ($this->db->field_exists($col, 'products')) {
			$v = $this->input->post($col);
			if ($v !== null && $v !== '') $data[$col] = $v;
		}
	}

	// Pre-check duplicate SKU/barcode
	if (!empty($data['sku'])) {
		$dup = $this->db->get_where('products', array('sku'=>$data['sku'], 'deleted_at'=>null))->row_array();
		if ($dup) {
			$this->session->set_flashdata('errors', 'Mã SKU "'.htmlspecialchars($data['sku']).'" đã tồn tại (SP: '.htmlspecialchars($dup['name']).')');
			redirect('products/add', 'refresh'); return;
		}
	}
	if (!empty($data['barcode'])) {
		$dup = $this->db->get_where('products', array('barcode'=>$data['barcode'], 'deleted_at'=>null))->row_array();
		if ($dup) {
			$this->session->set_flashdata('errors', 'Mã vạch "'.htmlspecialchars($data['barcode']).'" đã tồn tại');
			redirect('products/add', 'refresh'); return;
		}
	}
	$create = $this->model_products->create($data);
	if($create == true) {
		// Audit log: create product
		$new_id = (int)$this->db->insert_id();
		if ($new_id <= 0) {
			$row = $this->db->order_by('id','DESC')->limit(1)->get_where('products', array('name'=>$data['name']))->row_array();
			$new_id = $row ? (int)$row['id'] : 0;
		}
		$audit_data = $data; unset($audit_data['_brands'],$audit_data['_categories'],$audit_data['_attributes']);
		$this->audit->log('create', 'products', $new_id, null, $audit_data);
		$this->session->set_flashdata('success', 'Tạo thành công');
		redirect('products/', 'refresh');
	}
	else {
		$this->session->set_flashdata('errors', 'Đã xảy ra lỗi!!');
		redirect('products/create', 'refresh');
	}
        }
        else {
            // false case

	// attributes
	$attribute_data = $this->model_attributes->getActiveAttributeData();

	$attributes_final_data = array();
	foreach ($attribute_data as $k => $v) {
		$attributes_final_data[$k]['attribute_data'] = $v;

		$value = $this->model_attributes->getAttributeValueData($v['id']);

		$attributes_final_data[$k]['attribute_value'] = $value;
	}

	$this->data['attributes'] = $attributes_final_data;
			$this->data['brands'] = $this->model_brands->getActiveBrands();
			$this->data['category'] = $this->model_category->getActiveCategroy();
			$this->data['stores'] = $this->model_stores->getActiveStore();

            $this->render_template('products/create', $this->data);
        }
	}

    /*
    * This function is invoked from another function to upload the image into the assets folder
    * and returns the image path
    */
	public function upload_image()
    {
	// assets/images/product_image
        $config['upload_path'] = 'assets/images/product_image';
        $config['file_name'] =  uniqid();
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '1000';

        // $config['max_width']  = '1024';s
        // $config['max_height']  = '768';

        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload('product_image'))
        {
            $error = $this->upload->display_errors();
            return $error;
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $type = explode('.', $_FILES['product_image']['name']);
            $type = $type[count($type) - 1];

            $path = $config['upload_path'].'/'.$config['file_name'].'.'.$type;
            return ($data == true) ? $path : false;
        }
    }

    /*
    * If the validation is not valid, then it redirects to the edit product page
    * If the validation is successfully then it updates the data into the database
    * and it stores the operation message into the session flashdata and display on the manage product page
    */
	public function update($product_id)
	{
        if(!in_array('updateProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        if(!$product_id) {
            redirect('dashboard', 'refresh');
        }

        $this->form_validation->set_rules('product_name', 'Tên sản phẩm', 'trim|required');
        $this->form_validation->set_rules('sku', 'SKU', 'trim|required');
        $this->form_validation->set_rules('price', 'Giá', 'trim|required');
        $this->form_validation->set_rules('qty', 'SL', 'trim|required');
        $this->form_validation->set_rules('store', 'Cửa hàng', 'trim|required');
        $this->form_validation->set_rules('availability', 'Tình trạng', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            // true case

            $data = array(
                'name' => $this->input->post('product_name'),
                'sku' => $this->input->post('sku'),
                'price' => $this->input->post('price'),
                'qty' => $this->input->post('qty'),
                'description' => $this->input->post('description'),
                'attribute_value_id' => json_encode($this->input->post('attributes_value_id')),
                'brand_id' => json_encode($this->input->post('brands')),
                'category_id' => json_encode($this->input->post('category')),
                'store_id' => $this->input->post('store'),
                'availability' => $this->input->post('availability'),
                '_brands'     => $this->input->post('brands'),
                '_categories' => $this->input->post('category'),
                '_attributes' => $this->input->post('attributes_value_id'),
            );

            // Trường nâng cao
            foreach (array('barcode','unit','cost_price','wholesale_price','min_stock','max_stock','weight','has_batches','has_variants') as $col) {
                if ($this->db->field_exists($col, 'products')) {
                    $v = $this->input->post($col);
                    if ($v !== null && $v !== '') $data[$col] = $v;
                }
            }

            if($_FILES['product_image']['size'] > 0) {
                $upload_image = $this->upload_image();
                $upload_image = array('image' => $upload_image);

                $this->model_products->update($upload_image, $product_id);
            }

            // Audit log: capture old data trước khi update
            $old_row = $this->db->get_where('products', array('id'=>$product_id))->row_array();
            $update = $this->model_products->update($data, $product_id);
            if($update == true) {
                $audit_new = $data; unset($audit_new['_brands'],$audit_new['_categories'],$audit_new['_attributes']);
                $this->audit->log('update', 'products', (int)$product_id, $old_row, $audit_new);
                $this->session->set_flashdata('success', 'Cập nhật thành công');
                redirect('products/', 'refresh');
            }
            else {
                $this->session->set_flashdata('errors', 'Đã xảy ra lỗi!!');
                redirect('products/update/'.$product_id, 'refresh');
            }
        }
        else {
            // attributes
            $attribute_data = $this->model_attributes->getActiveAttributeData();

            $attributes_final_data = array();
            foreach ($attribute_data as $k => $v) {
                $attributes_final_data[$k]['attribute_data'] = $v;

                $value = $this->model_attributes->getAttributeValueData($v['id']);

                $attributes_final_data[$k]['attribute_value'] = $value;
            }

            // false case
            $this->data['attributes'] = $attributes_final_data;
            $this->data['brands'] = $this->model_brands->getActiveBrands();
            $this->data['category'] = $this->model_category->getActiveCategroy();
            $this->data['stores'] = $this->model_stores->getActiveStore();

            $product_data = $this->model_products->getProductData($product_id);
            $this->data['product_data'] = $product_data;
            $this->render_template('products/edit', $this->data);
        }
	}

    /*
    * It removes the data from the database
    * and it returns the response into the json format
    */
	public function remove()
	{
        if(!in_array('deleteProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        $product_id = $this->input->post('product_id');

        $response = array();
        if($product_id) {
            $old_row = $this->db->get_where('products', array('id'=>$product_id))->row_array();
            $delete = $this->model_products->remove($product_id);
            if($delete == true) {
                $this->audit->log('delete', 'products', (int)$product_id, $old_row, null);
                $response['success'] = true;
                $response['messages'] = "Xoá thành công";
            }
            else {
                $response['success'] = false;
                $response['messages'] = "Lỗi cơ sở dữ liệu khi xoá thông tin sản phẩm";
            }
        }
        else {
            $response['success'] = false;
            $response['messages'] = "Refersh the page again!!";
        }

        echo json_encode($response);
	}

}