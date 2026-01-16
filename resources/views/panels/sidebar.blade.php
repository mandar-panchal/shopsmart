@php
$configData = Helper::applClasses();
@endphp
<div
  class="main-menu menu-fixed {{ $configData['theme'] === 'dark' || $configData['theme'] === 'semi-dark' ? 'menu-dark' : 'menu-light' }} menu-accordion menu-shadow"
  data-scroll-to-active="true">
  <div class="navbar-header">
    <ul class="nav navbar-nav flex-row">
      <li class="nav-item me-auto">
        <a class="navbar-brand" href="{{ url('/') }}">
          <span class="brand-logo">
            <img src="/images/logo/logo1.png" height="32"/>
          </span>
          <h2 class="brand-text">{{ config('app.name') }}</h2>
        </a>
      </li>
      <li class="nav-item nav-toggle">
        <a class="nav-link modern-nav-toggle pe-0" data-toggle="collapse">
          <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
          <i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-primary" data-feather="disc"
            data-ticon="disc"></i>
        </a>
      </li>
    </ul>
  </div>
  <div class="shadow-bottom"></div>
  <div class="main-menu-content">
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

      <!-- Dashboard -->
      <li class="nav-item {{ Route::currentRouteName() === 'dashboard-ecommerce' ? 'active' : '' }}" data-menu="dashboard-ecommerce">
        <a href="/" class="d-flex align-items-center">
          <i data-feather="home"></i>
          <span class="menu-title text-truncate">Dashboard</span>
        </a>
      </li>

      <li class="nav-item {{ Route::currentRouteName() === 'products.index' ? 'active' : '' }}" data-menu="products">
    <a href="{{ route('products.index') }}" class="d-flex align-items-center">
        <i data-feather="box"></i>
        <span class="menu-title text-truncate">Products</span>
    </a>
</li>
 
      @role('admin')
       <li class="nav-item has-sub">
        <a href="javascript:void(0)" class="d-flex align-items-center" target="_self">
          <i data-feather="user"></i>
        <span class="menu-title text-truncate">User Management</span>
       
        </a>
          <ul class="menu-content">
          <li class="nav-item {{ Request::is('users/create') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="/users/create">
          <i data-feather="user-plus"></i>
          <span class="menu-title text-truncate">Add new user</span>
        </a>
      </li>
            
            <li class="nav-item {{ Route::currentRouteName() === 'authorization-roles' ? 'active' : '' }}" data-menu="authorization-roles">
              <a href="/authorization/roles" class="d-flex align-items-center">
                <i data-feather="circle"></i>
                <span class="menu-title text-truncate">Roles</span>
              </a>
            </li>
            
            <li class="nav-item {{ Route::currentRouteName() === 'authorization-permission' ? 'active' : '' }}" data-menu="authorization-permission">
              <a href="/authorization/permission" class="d-flex align-items-center">
                <i data-feather="circle"></i>
                <span class="menu-title text-truncate">Manage Permissions</span>
              </a>
            </li>            
          </ul>
        </li>
        @endrole  
    </ul>    
  </div>
</div>
<!-- END: Main Menu-->
