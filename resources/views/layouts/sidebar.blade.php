<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- User Profile-->
                <li>
                    <!-- User Profile-->
                    <div class="user-profile d-flex no-block mt-3">
                        <div class="user-pic">
                            <img src="{{ asset('public/assets/images/1.jpg') }}" alt="users" class="rounded-circle"
                                width="40" />
                        </div>
                        <div class="user-content hide-menu ms-2">
                            <a href="#" class="" id="Userdd" role="button" aria-haspopup="true"
                                aria-expanded="false">
                                <h5 class="mb-0 user-name font-medium">
                                    {{ session('full_name') }}
                                    <i data-feather="chevron-down" class="feather-sm"></i>
                                </h5>
                                <span class="op-5 user-email">{{ session('role') }}</span>
                            </a>
                        </div>
                    </div>
                    <!-- End User Profile-->
                </li>
              
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark {{ request()->routeIs('dashboard') ? 'active bg-body' : '' }}"
                            href="{{ route('dashboard') }}" aria-expanded="false"><i data-feather="home" class="feather-icon"></i><span
                                class="hide-menu">Dashboard </span></a>
                    </li>
                    @if (session('role') === 'admin')
                    {{-- Staff Sidebar Item --}}
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark {{ request()->routeIs('staff.*') ? 'active bg-body' : '' }}"
                            href="{{ route('staff.index') }}" aria-expanded="false">
                            <i data-feather="users" class="feather-icon"></i>
                            <span class="hide-menu">Staff</span>
                        </a>
                    </li>

                    {{-- Products --}}
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark {{ request()->routeIs('products.*') ? 'active bg-body' : '' }}"
                            href="{{ route('products.index') }}">
                            <i data-feather="package" class="feather-icon"></i>
                            <span class="hide-menu">Products</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('other-products.index') }}"
                            class="sidebar-link waves-effect waves-dark {{ request()->routeIs('other-products.*') ? 'active bg-body' : '' }}">
                            <i data-feather="layers" class="feather-icon"></i>
                            <span class="hide-menu">Other Products</span>
                        </a>
                    </li>

                    <li class="sidebar-item ">
                        <a href="{{ route('other-product-sales.index') }}"
                            class="sidebar-link {{ request()->routeIs('other-product-sales.*') ? 'active bg-body' : '' }} waves-effect waves-dark">
                            <i data-feather="trending-up" class="feather-icon"></i>
                            <span class="hide-menu">Other Product Sales</span>
                        </a>
                    </li>
                @endif
                {{-- Daily Balance Sidebar Item (works for both admin and staff) --}}
                {{-- Billing (admin + staff) --}}
                @if (session('role') === 'staff')
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark {{ request()->routeIs('billing.index') ? 'active bg-body' : '' }}"
                            href="{{ route('billing.index') }}">
                            <i data-feather="file-text" class="feather-icon"></i>
                            <span class="hide-menu">Billing</span>
                        </a>
                    </li>
                @endif
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark {{ request()->routeIs('daily-balance.*') ? 'active bg-body' : '' }}"
                        href="{{ route('daily-balance.index') }}" aria-expanded="false">
                        <i data-feather="credit-card" class="feather-icon"></i>
                        <span class="hide-menu">Daily Balance</span>
                    </a>
                </li>
                {{-- ── Invoice List ── --}}
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark {{ request()->routeIs('billing.invoice-list') ? 'active bg-body' : '' }}"
                        href="{{ route('billing.invoice-list') }}" aria-expanded="false">
                        <i data-feather="file-text" class="feather-icon"></i>
                        <span class="hide-menu">Invoices</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('logout') }}"
                        aria-expanded="false"><i data-feather="log-out" class="feather-icon"></i><span
                            class="hide-menu">Log Out</span></a>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
