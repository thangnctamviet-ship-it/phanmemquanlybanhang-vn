<aside class="main-sidebar">
  <section class="sidebar">
    <ul class="sidebar-menu" data-widget="tree">

      <li id="dashboardMainMenu">
        <a href="<?php echo base_url('dashboard') ?>">
          <i class="fa fa-dashboard"></i> <span>Bảng điều khiển</span>
        </a>
      </li>

      <?php if ($user_permission): ?>

        <?php
          $can_product = in_array('createProduct', $user_permission) || in_array('updateProduct', $user_permission)
                      || in_array('viewProduct', $user_permission)   || in_array('deleteProduct', $user_permission);
          $can_order   = in_array('createOrder', $user_permission)   || in_array('updateOrder', $user_permission)
                      || in_array('viewOrder', $user_permission)     || in_array('deleteOrder', $user_permission);
          $can_store   = in_array('createStore', $user_permission)   || in_array('updateStore', $user_permission)
                      || in_array('viewStore', $user_permission)     || in_array('deleteStore', $user_permission);
          $can_brand   = in_array('createBrand', $user_permission)   || in_array('updateBrand', $user_permission)
                      || in_array('viewBrand', $user_permission)     || in_array('deleteBrand', $user_permission);
          $can_cat     = in_array('createCategory', $user_permission)|| in_array('updateCategory', $user_permission)
                      || in_array('viewCategory', $user_permission)  || in_array('deleteCategory', $user_permission);
          $can_attr    = in_array('createAttribute', $user_permission)|| in_array('updateAttribute', $user_permission)
                      || in_array('viewAttribute', $user_permission) || in_array('deleteAttribute', $user_permission);
          $can_user    = in_array('createUser', $user_permission)    || in_array('updateUser', $user_permission)
                      || in_array('viewUser', $user_permission)      || in_array('deleteUser', $user_permission);
          $can_group   = in_array('createGroup', $user_permission)   || in_array('updateGroup', $user_permission)
                      || in_array('viewGroup', $user_permission)     || in_array('deleteGroup', $user_permission);
          $can_report  = in_array('viewReports', $user_permission);
          $can_company = in_array('updateCompany', $user_permission);
          $can_config  = $can_brand || $can_cat || $can_attr;

          // Tenant feature flags (đọc từ Admin_Controller::_load_tenant_settings)
          $TS = isset($tenant_settings) ? $tenant_settings : array();
          $feat = function($k) use ($TS) { return !empty($TS[$k]) && $TS[$k] != '0'; };
        ?>

        <li class="header" style="color:#94a3b8;font-size:11px;letter-spacing:.5px;">KINH DOANH</li>

        <?php if (in_array('createOrder', $user_permission)): ?>
          <li id="posNav">
            <a href="<?php echo base_url('pos') ?>" target="_blank" style="background:linear-gradient(90deg,#4f46e5,#7c3aed);color:#fff;border-radius:6px;margin:4px 8px;">
              <i class="fa fa-bolt"></i> <span>Bán hàng (POS)</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if ($can_product): ?>
          <li class="treeview" id="mainProductNav">
            <a href="#">
              <i class="fa fa-cube"></i> <span>Sản phẩm</span>
              <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
              <?php if (in_array('createProduct', $user_permission)): ?>
                <li id="addProductNav"><a href="<?php echo base_url('products/create') ?>"><i class="fa fa-circle-o"></i> Thêm sản phẩm</a></li>
              <?php endif; ?>
              <li id="manageProductNav"><a href="<?php echo base_url('products') ?>"><i class="fa fa-circle-o"></i> Quản lý sản phẩm</a></li>
            </ul>
          </li>
        <?php endif; ?>

        <?php if ($can_order): ?>
          <li class="treeview" id="mainOrdersNav">
            <a href="#">
              <i class="fa fa-dollar"></i> <span>Đơn hàng</span>
              <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
              <?php if (in_array('createOrder', $user_permission)): ?>
                <li id="addOrderNav"><a href="<?php echo base_url('orders/create') ?>"><i class="fa fa-circle-o"></i> Thêm đơn hàng</a></li>
              <?php endif; ?>
              <li id="manageOrdersNav"><a href="<?php echo base_url('orders') ?>"><i class="fa fa-circle-o"></i> Quản lý đơn hàng</a></li>
            </ul>
          </li>
        <?php endif; ?>

        <?php if ($can_order): ?>
          <li id="purchasesNav">
            <a href="<?php echo base_url('purchases') ?>">
              <i class="fa fa-truck"></i> <span>Nhập hàng</span>
            </a>
          </li>
          <li id="transfersNav">
            <a href="<?php echo base_url('transfers') ?>">
              <i class="fa fa-exchange"></i> <span>Chuyển kho</span>
            </a>
          </li>
          <?php if ($feat('enable_loyalty')): ?>
            <li class="treeview" id="customersNav">
              <a href="#">
                <i class="fa fa-id-card-o"></i> <span>Khách hàng</span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?php echo base_url('customers') ?>"><i class="fa fa-list"></i> Danh sách KH</a></li>
                <li><a href="<?php echo base_url('customers/loyalty') ?>"><i class="fa fa-star"></i> KH thân thiết &amp; Sinh nhật</a></li>
              </ul>
            </li>
          <?php else: ?>
            <li id="customersNav">
              <a href="<?php echo base_url('customers') ?>">
                <i class="fa fa-id-card-o"></i> <span>Khách hàng</span>
              </a>
            </li>
          <?php endif; ?>
          <li id="suppliersNav">
            <a href="<?php echo base_url('suppliers') ?>">
              <i class="fa fa-handshake-o"></i> <span>Nhà cung cấp</span>
            </a>
          </li>
          <li id="debtsNav">
            <a href="<?php echo base_url('debts') ?>">
              <i class="fa fa-credit-card"></i> <span>Công nợ &amp; Thu chi</span>
            </a>
          </li>

          <?php /* Các module nâng cao — hiện khi bật feature, ẩn mặc định */ ?>
          <?php if ($feat('enable_returns')): ?>
            <li id="returnsNav" class="text-muted" title="Module sẽ hoàn thiện ở phase 4">
              <a href="#" onclick="alert('Module Trả hàng đang phát triển — DB đã sẵn sàng, UI sẽ ra ở phase tiếp.');return false;">
                <i class="fa fa-undo"></i> <span>Trả hàng</span> <small style="opacity:.5;">(sắp ra)</small>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($feat('enable_batches')): ?>
            <li id="batchesNav" class="text-muted">
              <a href="#" onclick="alert('Module Lô hàng/HSD đang phát triển.');return false;">
                <i class="fa fa-flask"></i> <span>Lô hàng &amp; HSD</span> <small style="opacity:.5;">(sắp ra)</small>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($feat('enable_promotions')): ?>
            <li id="promosNav" class="text-muted">
              <a href="#" onclick="alert('Module Khuyến mãi/Voucher đang phát triển.');return false;">
                <i class="fa fa-gift"></i> <span>Khuyến mãi</span> <small style="opacity:.5;">(sắp ra)</small>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($feat('enable_employee_shift')): ?>
            <li id="shiftsNav" class="text-muted">
              <a href="#" onclick="alert('Module Ca làm nhân viên đang phát triển.');return false;">
                <i class="fa fa-clock-o"></i> <span>Ca làm việc</span> <small style="opacity:.5;">(sắp ra)</small>
              </a>
            </li>
          <?php endif; ?>

        <?php endif; ?>

        <?php if ($can_store): ?>
          <li id="storeNav">
            <a href="<?php echo base_url('stores/') ?>">
              <i class="fa fa-building-o"></i> <span>Cửa hàng</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if ($can_report): ?>
          <li class="treeview" id="reportNav">
            <a href="#">
              <i class="fa fa-bar-chart"></i> <span>Báo cáo</span>
              <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
              <li><a href="<?php echo base_url('reports/advanced') ?>"><i class="fa fa-pie-chart"></i> Phân tích nâng cao</a></li>
              <li><a href="<?php echo base_url('reports') ?>"><i class="fa fa-line-chart"></i> Doanh thu theo năm</a></li>
            </ul>
          </li>
        <?php endif; ?>

        <?php if ($can_user || $can_group || $can_config || $can_company): ?>
          <li class="header" style="color:#94a3b8;font-size:11px;letter-spacing:.5px;margin-top:10px;">QUẢN TRỊ</li>
        <?php endif; ?>

        <?php if ($can_user): ?>
          <li class="treeview" id="mainUserNav">
            <a href="#">
              <i class="fa fa-users"></i> <span>Người dùng</span>
              <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
              <?php if (in_array('createUser', $user_permission)): ?>
                <li id="createUserNav"><a href="<?php echo base_url('users/create') ?>"><i class="fa fa-circle-o"></i> Thêm người dùng</a></li>
              <?php endif; ?>
              <li id="manageUserNav"><a href="<?php echo base_url('users') ?>"><i class="fa fa-circle-o"></i> Quản lý người dùng</a></li>
            </ul>
          </li>
        <?php endif; ?>

        <?php if ($can_group): ?>
          <li class="treeview" id="mainGroupNav">
            <a href="#">
              <i class="fa fa-user-secret"></i> <span>Nhóm quyền</span>
              <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
              <?php if (in_array('createGroup', $user_permission)): ?>
                <li id="addGroupNav"><a href="<?php echo base_url('groups/create') ?>"><i class="fa fa-circle-o"></i> Thêm nhóm</a></li>
              <?php endif; ?>
              <li id="manageGroupNav"><a href="<?php echo base_url('groups') ?>"><i class="fa fa-circle-o"></i> Quản lý nhóm</a></li>
            </ul>
          </li>
        <?php endif; ?>

        <?php if ($can_config): ?>
          <li class="treeview" id="mainConfigNav">
            <a href="#">
              <i class="fa fa-cogs"></i> <span>Cấu hình</span>
              <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
              <?php if ($can_brand): ?>
                <li id="brandNav"><a href="<?php echo base_url('brands/') ?>"><i class="fa fa-tag"></i> Thương hiệu</a></li>
              <?php endif; ?>
              <?php if ($can_cat): ?>
                <li id="categoryNav"><a href="<?php echo base_url('category/') ?>"><i class="fa fa-folder-o"></i> Danh mục</a></li>
              <?php endif; ?>
              <?php if ($can_attr): ?>
                <li id="attributeNav"><a href="<?php echo base_url('attributes/') ?>"><i class="fa fa-list"></i> Thuộc tính</a></li>
              <?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php if ($can_company): ?>
          <li id="companyNav">
            <a href="<?php echo base_url('company/') ?>"><i class="fa fa-building"></i> <span>Thông tin công ty</span></a>
          </li>
        <?php endif; ?>

      <?php endif; ?>

    </ul>
  </section>
</aside>
