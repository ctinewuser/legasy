         <!-- Sidebar -->
         <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
         <!-- Sidebar - Brand -->
         <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo base_url('admin/dashboard'); ?>">
         <div class="sidebar-brand-text mx-3"><img src="<?php echo base_url(); ?>assets/logo.png" alt="..." class="img-fluid"></div>
         </a>
         <?php
         $session = $this->session->userdata('admin');
         $admin_id = $session['id'];
         $fullname = $session['full_name'];
         $image = $session['image'];
         $is_admin = $session['is_admin'];
         $siteUrlUri = $this->uri->segment('2');
         $siteSubUrlUri = $this->uri->segment('3');
         ?>
         <!-- Nav Item - Dashboard -->
         <li class="nav-item active">
         <a class="nav-link" href="<?=base_url('admin/dashboard'); ?>">
         <i class="fas fa-fw fa-tachometer-alt"></i>
         <span>Dashboard</span></a>
         </li>
         <!-- Heading -->
         <div class="sidebar-heading">
         MAIN NAVIGATION
         </div>
         <!-- Nav Item - Pages Collapse Menu -->
         <li class="nav-item">
         <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAdmin" aria-expanded="true" aria-controls="collapseTwo">
         <i class="fas fa-fw fa-user"></i>
         <span>Admin</span>
         </a>
         <div id="collapseAdmin"  class="collapse  <?php echo ($siteUrlUri == 'adminprofile') || ($siteUrlUri == 'admineditprofile') ? 'show' : ''; ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
         <div class="bg-white menu py-2 collapse-inner rounded">
         <a class="collapse-item" href="<?php echo base_url('admin/adminprofile/') . $admin_id; ?>">
         Admin Profile
         </a>
         </div>
         </div>
         </li>
         <li class="nav-item">
         <a class="nav-link collapsed  " href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
         <i class="fa fa-users teal-color " ></i>
         <!--  <i class="fas fa-fw fa-user"></i> -->
         <span>Users</span>
         </a>
         <div id="collapseTwo" class="collapse  <?php echo ($siteUrlUri == 'userList') || ($siteUrlUri == 'artistList') || ($siteUrlUri == 'profile') || ($siteUrlUri == 'edit_user') || ($siteUrlUri == 'edit_artist') ? 'show' : ''; ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
         <div class="bg-white menu py-2 collapse-inner rounded">
         <h6 class="collapse-header">Users</h6>
         <a class="collapse-item" href="<?=base_url('admin/userList'); ?>">Fans List</a>
         <a class="collapse-item" href="<?=base_url('admin/artistList'); ?>">Artist List</a>
         </div>
         </div>
         </li>
         <li class="nav-item">
         <a class="nav-link collapsed  " href="#" data-toggle="collapse" data-target="#collapsereport" aria-expanded="true" aria-controls="collapsereport">
         <i class="far fa-user-circle"></i>
         <span>Report Lists</span>
         </a>
         <div id="collapsereport" class="collapse  <?php echo ($siteUrlUri == 'post_report') || ($siteUrlUri == 'artist_report') || ($siteUrlUri == 'block_report') ? 'show' : ''; ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
         <div class="bg-white menu py-2 collapse-inner rounded">
         <h6 class="collapse-header">Report Lists</h6>
         <a class="collapse-item" href="<?=base_url('admin/post_report'); ?>">Post Report</a>
         <a class="collapse-item" href="<?=base_url('admin/artist_report'); ?>">Artist Report</a>
         <a class="collapse-item" href="<?=base_url('admin/block_report'); ?>">Block User</a>
         </div>
         </div>
         </li>
         <li class="nav-item">
         <a class="nav-link collapsed  " href="#" data-toggle="collapse" data-target="#collapseCat" aria-expanded="true" aria-controls="collapseCat">
         <i class="fa fa-gamepad"></i>
         <span>Genre</span>
         </a>
         <div id="collapseCat" class="collapse  <?php echo ($siteUrlUri == 'catList') || ($siteUrlUri == 'edit_genrecategory') ? 'show' : ''; ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
         <div class="bg-white menu py-2 collapse-inner rounded">
         <h6 class="collapse-header">Genre</h6>
         <a class="collapse-item" href="<?=base_url('admin/catList'); ?>">Genre List</a>
         </div>
         </div>
         </li>
         <!--FEED-->
         <li class="nav-item">
         <a class="nav-link collapsed  " href="#" data-toggle="collapse" data-target="#collapsePost" aria-expanded="true" aria-controls="collapsePost">
         <i class="fas fa-folder-open"></i>
         <span>News Feed</span>
         </a>
         <div id="collapsePost" class="collapse  <?php echo ($siteUrlUri == 'feedList') || ($siteUrlUri == 'showImage') || ($siteUrlUri == 'show_pinboard') || ($siteUrlUri == 'show_postlist') ? 'show' : ''; ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
         <div class="bg-white menu py-2 collapse-inner rounded">
         <h6 class="collapse-header">News Feed</h6>
         <a class="collapse-item" href="<?=base_url('admin/feedList'); ?>">News Feed List</a>
         <a class="collapse-item" href="<?=base_url('admin/show_pinboard'); ?>">Pinboard</a>
         </div>
         </div>
         </li>
         <!--Concert-->
         <li class="nav-item">
         <a class="nav-link collapsed  " href="#" data-toggle="collapse" data-target="#collapseConcert" aria-expanded="true" aria-controls="collapseConcert">
         <i class='fa fa-microphone'></i>
         <span>Concert</span>
         </a>
         <div id="collapseConcert" class="collapse  <?php echo ($siteUrlUri == 'show_concert') || ($siteUrlUri == 'edit_concert') ? 'show' : ''; ?>" aria-labelledby="headingThree" data-parent="#accordionSidebar">
         <div class="bg-white menu py-2 collapse-inner rounded">
         <h6 class="collapse-header">Concert</h6>
         <a class="collapse-item" href="<?=base_url('admin/show_concert'); ?>">Concert List</a>
         </div>
         </div>
         </li>
         <!--Goals-->
         <li class="nav-item">
         <a class="nav-link collapsed  " href="#" data-toggle="collapse" data-target="#collapseGoals" aria-expanded="true" aria-controls="collapseGoals">
         <i class="fa fa-table" aria-hidden="true"></i>
         <!--  <i class="fas fa-star"></i> -->
         <span>Goals</span>
         </a>
         <div id="collapseGoals" class="collapse  <?php echo ($siteUrlUri == 'goal_list') || ($siteUrlUri == 'checkout') ? 'show' : ''; ?>" aria-labelledby="headingfour" data-parent="#accordionSidebar">
         <div class="bg-white menu py-2 collapse-inner rounded">
         <h6 class="collapse-header">Goals</h6>
         <a class="collapse-item" href="<?=base_url('admin/goal_list'); ?>">Goals List</a>
         <a class="collapse-item" href="<?=base_url('admin/checkout'); ?>">Redeem Check Out</a>
         </div>
         </div>
         </li>
         <li class="nav-item">
         <a class="nav-link collapsed  " href="#" data-toggle="collapse" data-target="#collapseCredits" aria-expanded="true" aria-controls="collapseCredits">
         <i class="fas fa-star"></i>
         <span>Credits</span>
         </a>
         <div id="collapseCredits" class="collapse  <?php echo ($siteUrlUri == 'credit_list') || ($siteUrlUri == 'credit_amount') || ($siteUrlUri == 'edit_amount') ? 'show' : ''; ?>" aria-labelledby="headingfour" data-parent="#accordionSidebar">
         <div class="bg-white menu py-2 collapse-inner rounded">
         <h6 class="collapse-header">Credits</h6>
         <a class="collapse-item" href="<?=base_url('admin/credit_list'); ?>">Credit list</a>
         <a class="collapse-item" href="<?=base_url('admin/credit_amount'); ?>">Payable Credits</a>
         </div>
         </div>
         </li>

         <li class="nav-item">
         <a class="nav-link collapsed  " href="#" data-toggle="collapse" data-target="#collapsePack" aria-expanded="true" aria-controls="collapsePack">
         <i class='fa fa-cart-plus'></i>
         <span>Bit Pack</span>
         </a>
         <div id="collapsePack" class="collapse  <?php echo ($siteUrlUri == 'show_bitpack') || ($siteUrlUri == 'add_bitpack') || ($siteUrlUri == 'edit_bitpack') || ($siteUrlUri == 'bit_purchased_user') ? 'show' : ''; ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
         <div class="bg-white menu py-2 collapse-inner rounded">
         <h6 class="collapse-header">Bit Pack</h6>
         <a class="collapse-item" href="<?=base_url('admin/show_bitpack'); ?>">Bit Pack List</a>
         <a class="collapse-item" href="<?=base_url('admin/bit_purchased_user'); ?>">Bit Purchased User</a>
         </div>
         </div>
         <li class="nav-item">
         <a class="nav-link" href="<?=base_url('admin/show_donaterlist'); ?>">
         <i class="fa fa-list" aria-hidden="true"></i>
         <span>Bit Donater List</span>
         </a>
         </li>
         <li class="nav-item">
         <a class="nav-link" href="<?=base_url('admin/songsList'); ?>">
         <i class="fa fa-download" aria-hidden="true"></i>
         <span>Songs</span></a>
         </li>
         <li class="nav-item">
         <a class="nav-link" href="<?=base_url('admin/show_reels'); ?>">
         <!--   <i class='fa fa-cart-plus'></i> -->
         <i class='fa fa-id-badge'></i>
         <span>Reels</span>
         </a>
         </li>

         <li class="nav-item">
         <a class="nav-link" href="<?=base_url('admin/termServices'); ?>">
         <i class="fas fa-fw fa-cog"></i>
         <span>Terms and services</span></a>
         </li>
         <!-- Nav Item - Tables -->
         <li class="nav-item">
         <a class="nav-link" href="<?=base_url('admin/privacyPolicy'); ?>">
         <i class="fas fa-fw fa-cogs"></i>
         <span>Privacy Policy</span></a>
         </li>
         <li class="nav-item">
         <a class="nav-link" href="<?=base_url('admin/contactUs'); ?>">

         <i class="fa fa-phone" aria-hidden="true"></i>
         <span>Contact Us</span>
         </a>
         </li>
         <li class="nav-item">
         <a class="nav-link" href="<?=base_url('admin/aboutUs_page'); ?>">

         <i class="fa fa-columns" aria-hidden="true"></i>

         <span>About Us</span>
         </a>
         </li>
         <hr class="sidebar-divider d-none d-md-block">
         <!-- Sidebar Toggler (Sidebar) -->
         <div class="text-center d-none d-md-inline">
         <button class="rounded-circle border-0" id="sidebarToggle"></button>
         </div>
         </ul>
         <!-- End of Sidebar -->
         <!-- Content Wrapper -->
         <div id="content-wrapper" class="d-flex flex-column">
         <!-- Main Content -->
         <div id="content">
         <!-- Topbar -->
         <nav class="navbar navbar-expand navbar-light header-box bg-theme topbar mb-4 static-top shadow">
         <!-- Sidebar Toggle (Topbar) -->
         <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
         <i class="fa fa-bars"></i>
         </button>
         <ul class="navbar-nav ml-auto">
         <!-- Nav Item - Search Dropdown (Visible Only XS) -->
         <li class="nav-item dropdown no-arrow d-sm-none">
         <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
         <i class="fas fa-search fa-fw"></i>
         </a>
         <!-- Dropdown - Messages -->
         <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
         <form class="form-inline mr-auto w-100 navbar-search">
         <div class="input-group">
         <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
         <div class="input-group-append">
         <button class="btn btn-primary" type="button">
         <i class="fas fa-search fa-sm"></i>
         </button>
         </div>
         </div>
         </form>
         </div>
         </li>
         <div class="topbar-divider d-none d-sm-block"></div>
         <!-- Nav Item - User Information -->
         <li class="nav-item dropdown no-arrow">
         <a class="nav-link dropdown-toggle user-click-box" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
         <span class="mr-2 d-none d-lg-inline font-weight-800 small">Admin</span>
         <?php if (!empty($image)) { ?>
         <img class="img-profile rounded-circle" src="<?php echo base_url('/assets/userfile/profile/') . $image; ?>">
         <?php
         } else { ?>
         <img class="img-profile rounded-circle" src="<?php echo base_url('/assets/userfile/profile/2020-11-23-18-13-30dym.jpg'); ?>">
         <?php
         } ?>
         </a>
         <!-- Dropdown - User Information -->
         <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in menu" aria-labelledby="userDropdown">
         <!--  <a class="dropdown-item" href="<?php echo base_url('admin/adminprofile/') . $admin_id; ?>">
         <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>Profile
         </a> -->
         <div class="dropdown-divider"></div>
         <!-- <a class="dropdown-item" href="<?=base_url('admin/logout'); ?>" data-toggle="modal" data-target="#logoutModal">-->
         <a class="dropdown-item" href="<?=base_url('admin/logout'); ?>">
         <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
         Logout
         </a>
         </div>
         </li>
         </ul>
         </nav>
         <!-- End of Topbar -->