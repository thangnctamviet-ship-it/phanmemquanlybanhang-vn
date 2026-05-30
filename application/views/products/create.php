

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Quản lý
      <small>Sản phẩm</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
      <li class="active">Sản phẩm</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div id="messages"></div>

        <?php if($this->session->flashdata('success')): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('success'); ?>
          </div>
        <?php elseif($this->session->flashdata('error')): ?>
          <div class="alert alert-error alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>


        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Thêm sản phẩm</h3>
          </div>
          <!-- /.box-header -->
          <form role="form" action="<?php base_url('users/create') ?>" method="post" enctype="multipart/form-data">
              <div class="box-body">

                <?php echo validation_errors(); ?>

                <div class="form-group">

                  <label for="product_image">Hình ảnh</label>
                  <div class="kv-avatar">
                      <div class="file-loading">
                          <input id="product_image" name="product_image" type="file">
                      </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="product_name">Tên sản phẩm</label>
                  <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Nhập tên sản phẩm" autocomplete="off"/>
                </div>

                <div class="form-group">
                  <label for="sku">SKU</label>
                  <input type="text" class="form-control" id="sku" name="sku" placeholder="Nhập SKU" autocomplete="off" />
                </div>

                <div class="form-group">
                  <label for="price">Giá</label>
                  <input type="text" class="form-control" id="price" name="price" placeholder="Nhập giá" autocomplete="off" />
                </div>

                <div class="form-group">
                  <label for="qty">SL</label>
                  <input type="text" class="form-control" id="qty" name="qty" placeholder="Nhập số lượng" autocomplete="off" />
                </div>

                <div class="form-group">
                  <label for="description">Mô tả</label>
                  <textarea type="text" class="form-control" id="description" name="description" placeholder="Nhập mô tả" autocomplete="off">
                  </textarea>
                </div>

                <?php if($attributes): ?>
                  <?php foreach ($attributes as $k => $v): ?>
                    <div class="form-group">
                      <label for="groups"><?php echo $v['attribute_data']['name'] ?></label>
                      <select class="form-control select_group" id="attributes_value_id" name="attributes_value_id[]" multiple="multiple">
                        <?php foreach ($v['attribute_value'] as $k2 => $v2): ?>
                          <option value="<?php echo $v2['id'] ?>"><?php echo $v2['value'] ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>
                  <?php endforeach ?>
                <?php endif; ?>

                <div class="form-group">
                  <label for="brands">Thương hiệu</label>
                  <select class="form-control select_group" id="brands" name="brands[]" multiple="multiple">
                    <?php foreach ($brands as $k => $v): ?>
                      <option value="<?php echo $v['id'] ?>"><?php echo $v['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="category">Danh mục</label>
                  <select class="form-control select_group" id="category" name="category[]" multiple="multiple">
                    <?php foreach ($category as $k => $v): ?>
                      <option value="<?php echo $v['id'] ?>"><?php echo $v['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="store">Cửa hàng</label>
                  <select class="form-control select_group" id="store" name="store">
                    <?php foreach ($stores as $k => $v): ?>
                      <option value="<?php echo $v['id'] ?>"><?php echo $v['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="store">Tình trạng</label>
                  <select class="form-control" id="availability" name="availability">
                    <option value="1">Có</option>
                    <option value="2">Không</option>
                  </select>
                </div>

                <?php
                  $TS = isset($tenant_settings) ? $tenant_settings : array();
                  $feat = function($k) use ($TS) { return !empty($TS[$k]) && $TS[$k] != '0'; };
                ?>

                <!-- ===== Trường nâng cao (hiện theo industry preset / feature flag) ===== -->
                <hr>
                <h4 style="color:#64748b;font-size:14px;margin:14px 0 10px;"><i class="fa fa-cogs"></i> Trường nâng cao (tùy chọn)</h4>

                <div class="row">
                  <div class="col-md-6 form-group">
                    <label>Mã vạch (Barcode)</label>
                    <input type="text" name="barcode" class="form-control" placeholder="Để trống = tự sinh">
                    <small class="text-muted">Khác SKU. Có thể quét trực tiếp khi bán hàng.</small>
                  </div>
                  <div class="col-md-6 form-group">
                    <label>Đơn vị tính</label>
                    <input type="text" name="unit" class="form-control" placeholder="vd: cái, hộp, lon, kg">
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 form-group">
                    <label>Giá vốn (giá nhập)</label>
                    <input type="number" name="cost_price" class="form-control" value="0" min="0">
                  </div>
                  <?php if ($feat('enable_wholesale')): ?>
                  <div class="col-md-6 form-group">
                    <label>Giá bán sỉ</label>
                    <input type="number" name="wholesale_price" class="form-control" value="0" min="0">
                  </div>
                  <?php endif; ?>
                </div>

                <div class="row">
                  <div class="col-md-4 form-group">
                    <label>Tồn tối thiểu (cảnh báo)</label>
                    <input type="number" name="min_stock" class="form-control" value="<?= htmlspecialchars($TS['low_stock_threshold'] ?? 5) ?>" min="0">
                  </div>
                  <div class="col-md-4 form-group">
                    <label>Tồn tối đa</label>
                    <input type="number" name="max_stock" class="form-control" value="0" min="0">
                  </div>
                  <?php if (in_array($TS['industry_preset'] ?? '', array('food','pharmacy','fashion'))): ?>
                  <div class="col-md-4 form-group">
                    <label>Cân nặng (kg)</label>
                    <input type="number" step="0.001" name="weight" class="form-control" value="0" min="0">
                  </div>
                  <?php endif; ?>
                </div>

                <?php if ($feat('enable_batches')): ?>
                <div class="checkbox">
                  <label><input type="checkbox" name="has_batches" value="1"> Theo dõi lô hàng &amp; hạn sử dụng cho sản phẩm này</label>
                </div>
                <?php endif; ?>
                <?php if ($feat('enable_variants')): ?>
                <div class="checkbox">
                  <label><input type="checkbox" name="has_variants" value="1"> Có nhiều biến thể (size/màu/mẫu)</label>
                </div>
                <?php endif; ?>

              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                <a href="<?php echo base_url('products/') ?>" class="btn btn-warning">Quay lại</a>
              </div>
            </form>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- col-md-12 -->
    </div>
    <!-- /.row -->


  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script type="text/javascript">
  $(document).ready(function() {
    $(".select_group").select2();
    $("#description").wysihtml5();

    $("#mainProductNav").addClass('active');
    $("#addProductNav").addClass('active');

    var btnCust = '<button type="button" class="btn btn-secondary" title="Thêm thẻ hình ảnh" ' +
        'onclick="alert(\'Call your custom code here.\')">' +
        '<i class="glyphicon glyphicon-tag"></i>' +
        '</button>';
    $("#product_image").fileinput({
        overwriteInitial: true,
        maxFileSize: 1500,
        showĐóng: false,
        showCaption: false,
        browseLabel: '',
        removeLabel: '',
        browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
        removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
        removeTitle: 'Huỷ hoặc đặt lại thay đổi',
        elErrorContainer: '#kv-avatar-errors-1',
        msgErrorClass: 'alert alert-block alert-danger',
        // defaultPreviewContent: '<img src="/uploads/default_avatar_male.jpg" alt="Your Avatar">',
        layoutTemplates: {main2: '{preview} ' +  btnCust + ' {remove} {browse}'},
        allowedFileExtensions: ["jpg", "png", "gif"]
    });

  });
</script>