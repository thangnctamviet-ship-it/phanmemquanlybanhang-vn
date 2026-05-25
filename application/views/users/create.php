

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Quản lý
        <small>Người dùng</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
        <li class="active">Người dùng</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12 col-xs-12">

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
              <h3 class="box-title">Thêm người dùng</h3>
            </div>
            <form role="form" action="<?php base_url('users/create') ?>" method="post">
              <div class="box-body">

                <?php echo validation_errors(); ?>

                <div class="form-group">
                  <label for="groups">Nhóm</label>
                  <select class="form-control" id="groups" name="groups">
                    <option value="">Chọn nhóm</option>
                    <?php foreach ($group_data as $k => $v): ?>
                      <option value="<?php echo $v['id'] ?>"><?php echo $v['group_name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="username">Tên đăng nhập</label>
                  <input type="text" class="form-control" id="username" name="username" placeholder="Tên đăng nhập" autocomplete="off">
                </div>

                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Email" autocomplete="off">
                </div>

                <div class="form-group">
                  <label for="password">Mật khẩu</label>
                  <input type="text" class="form-control" id="password" name="password" placeholder="Mật khẩu" autocomplete="off">
                </div>

                <div class="form-group">
                  <label for="cpassword">Xác nhận mật khẩu</label>
                  <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Xác nhận mật khẩu" autocomplete="off">
                </div>

                <div class="form-group">
                  <label for="fname">Tên</label>
                  <input type="text" class="form-control" id="fname" name="fname" placeholder="Tên" autocomplete="off">
                </div>

                <div class="form-group">
                  <label for="lname">Họ</label>
                  <input type="text" class="form-control" id="lname" name="lname" placeholder="Họ" autocomplete="off">
                </div>

                <div class="form-group">
                  <label for="phone">Điện thoại</label>
                  <input type="text" class="form-control" id="phone" name="phone" placeholder="Điện thoại" autocomplete="off">
                </div>

                <div class="form-group">
                  <label for="gender">Giới tính</label>
                  <div class="radio">
                    <label>
                      <input type="radio" name="gender" id="male" value="1">
                      Nam
                    </label>
                    <label>
                      <input type="radio" name="gender" id="female" value="2">
                      Nữ
                    </label>
                  </div>
                </div>

              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                <a href="<?php echo base_url('users/') ?>" class="btn btn-warning">Quay lại</a>
              </div>
            </form>
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
    $("#groups").select2();

    $("#mainUserNav").addClass('active');
    $("#createUserNav").addClass('active');

  });
</script>
