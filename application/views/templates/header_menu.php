<header class="main-header">
  <a href="<?php echo base_url('') ?>" class="logo">
    <span class="logo-mini"><b>QL</b></span>
    <span class="logo-lg"><b>QLBH</b> <small style="font-size:11px;opacity:0.7;font-weight:400;">Pro</small></span>
  </a>
  <nav class="navbar navbar-static-top">
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Bật/tắt điều hướng</span>
    </a>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:#64748b;">
            <i class="fa fa-user-circle-o" style="font-size:20px;"></i>
            <span class="hidden-xs" style="margin-left:6px;font-size:13px;">
              <?php
                $CI =& get_instance();
                $uname = $CI->session->userdata('username');
                echo htmlspecialchars($uname ?: 'Tài khoản');
              ?>
            </span>
            <i class="fa fa-caret-down" style="margin-left:4px;font-size:11px;"></i>
          </a>
          <ul class="dropdown-menu" style="min-width:200px;">
            <li><a href="<?php echo base_url('users/profile/') ?>"><i class="fa fa-user-o"></i> Hồ sơ</a></li>
            <li><a href="<?php echo base_url('users/setting/') ?>"><i class="fa fa-wrench"></i> Cài đặt</a></li>
            <li><a href="<?php echo base_url('account') ?>"><i class="fa fa-credit-card"></i> Tài khoản &amp; Gói</a></li>
            <li><a href="<?php echo base_url('profile') ?>"><i class="fa fa-key"></i> Đổi mật khẩu</a></li>
            <li class="divider"></li>
            <li><a href="<?php echo base_url('auth/logout') ?>" style="color:#dc2626;"><i class="fa fa-sign-out"></i> Đăng xuất</a></li>
          </ul>
        </li>
      </ul>
    </div>

  </nav>
</header>
