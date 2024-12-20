<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">Stisla</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">St</a>
        </div>
        <ul class="sidebar-menu">
            @if (auth()->user()->canAny([
                        'main_dashboards_views',
                        'transactions_dashboards_views',
                        'capsters_dashboards_views',
                        'products_dashboards_views',
                        'customers_dashboards_views',
                    ]))
                <li class="menu-header">Dashboards</li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-pie-chart"></i> <span>Dashboards</span></a>
                    <ul class="dropdown-menu">
                        @can('main_dashboards_views')
                            <li class="{{ Request::is('main_dashboard') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('dashboards/main/') }}">Main Dashboard</a>
                            </li>
                        @endcan
                        @can('transactions_dashboards_views')
                            <li class="{{ Request::is('transactions_dashboard') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('dashboards/transactions/') }}">Transactions Dashboard</a>
                            </li>
                        @endcan
                        @can('capsters_dashboards_views')
                            <li class="{{ Request::is('capsters_dashboard') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('dashboards/capsters/') }}">Capsters Dashboard</a>
                            </li>
                        @endcan
                        @can('products_dashboards_views')
                            <li class="{{ Request::is('products_dashboard') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('dashboards/products/') }}">Products Dashboard</a>
                            </li>
                        @endcan
                        @can('customers_dashboards_views')
                            <li class="{{ Request::is('customers_dashboard') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('dashboards/customers/') }}">Customers Dashboard</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            @if (auth()->user()->can('appointments_view'))
                <li class="menu-header">Appointments</li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-calendar"></i> <span>Appointments</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('appointments') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('appointments/') }}">Appointments</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (auth()->user()->canAny(['transactions_view', 'transactions_table_view']))
                <li class="menu-header">Transactions</li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-shopping-bag"></i> <span>Transactions</span></a>
                    <ul class="dropdown-menu">
                        @can('transactions_view')
                            <li class="{{ Request::is('transactions') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('transactions/') }}">POS</a>
                            </li>
                        @endcan
                        @can('transactions_view')
                            <li class="{{ Request::is('transactions_table') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('transactions_table/') }}">Transactions</a>
                            </li>
                        @endcan
                        @can('summary_payment_dashboards_views')
                            <li class="{{ Request::is('summary_payment_dashboard') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('dashboards/summary_payment/') }}">Summary Payment</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            @if (auth()->user()->can('products_view'))
                <li class="menu-header">Products</li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-archive"></i> <span>Products</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('products') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('products/') }}">Products</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (auth()->user()->can('customers_view'))
                <li class="menu-header">Customers</li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-address-card"></i> <span>Customers</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('customers') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('customers/') }}">Customers</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (auth()->user()->can('promos_view'))
                <li class="menu-header">Promos</li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fa-solid fa-tags"></i> <span>Promos</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('promos') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('promos/') }}">Promos</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (auth()->user()->can('attendances_view'))
                <li class="menu-header">User Attendance</li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fa-solid fa-clipboard-user"></i> <span>User Attendance</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('attendances') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('attendances/') }}">User Attendance</a>
                        </li>
                    </ul>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('attendances/index_history') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('attendances/index_history/') }}">User Attendance History</a>
                        </li>
                    </ul>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('attendances/index_approval') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('attendances/approval') }}">User Attendance Approval</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (auth()->user()->canAny(['roles_view', 'users_view', 'capsters_view']))
                <li class="menu-header">Setting</li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-cogs"></i>
                        <span>Setting</span></a>
                    <ul class="dropdown-menu">
                        @can('roles_view')
                            <li class="{{ Request::is('roles') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('roles/') }}">Roles</a>
                            </li>
                        @endcan
                        @can('users_view')
                            <li class="{{ Request::is('users') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('users/') }}">Users</a>
                            </li>
                        @endcan
                        @can('capsters_view')
                            <li class="{{ Request::is('capsters') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('capsters/') }}">Capsters</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif




            {{-- <li class="menu-header">Dashboard</li>
            <li class="nav-item dropdown {{ $type_menu === 'dashboard' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-fire"></i><span>Dashboard</span></a>
                <ul class="dropdown-menu">
                    <li class='{{ Request::is('dashboard-general-dashboard') ? 'active' : '' }}'>
                        <a class="nav-link"
                            href="{{ url('dashboard-general-dashboard') }}">General Dashboard</a>
                    </li>
                    <li class="{{ Request::is('dashboard-ecommerce-dashboard') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('dashboard-ecommerce-dashboard') }}">Ecommerce Dashboard</a>
                    </li>
                </ul>
            </li>
            <li class="menu-header">Starter</li>
            <li class="nav-item dropdown {{ $type_menu === 'layout' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"
                    data-toggle="dropdown"><i class="fas fa-columns"></i> <span>Layout</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('layout-default-layout') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('layout-default-layout') }}">Default Layout</a>
                    </li>
                    <li class="{{ Request::is('transparent-sidebar') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('transparent-sidebar') }}">Transparent Sidebar</a>
                    </li>
                    <li class="{{ Request::is('layout-top-navigation') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('layout-top-navigation') }}">Top Navigation</a>
                    </li>
                </ul>
            </li>
            <li class="{{ Request::is('blank-page') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('blank-page') }}"><i class="far fa-square"></i> <span>Blank Page</span></a>
            </li>
            <li class="nav-item dropdown {{ $type_menu === 'bootstrap' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-th"></i> <span>Bootstrap</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('bootstrap-alert') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-alert') }}">Alert</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-badge') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-badge') }}">Badge</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-breadcrumb') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-breadcrumb') }}">Breadcrumb</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-buttons') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-buttons') }}">Buttons</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-card') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-card') }}">Card</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-carousel') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-carousel') }}">Carousel</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-collapse') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-collapse') }}">Collapse</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-dropdown') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-dropdown') }}">Dropdown</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-form') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-form') }}">Form</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-list-group') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-list-group') }}">List Group</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-media-object') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-media-object') }}">Media Object</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-modal') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-modal') }}">Modal</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-nav') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-nav') }}">Nav</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-navbar') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-navbar') }}">Navbar</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-pagination') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-pagination') }}">Pagination</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-popover') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-popover') }}">Popover</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-progress') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-progress') }}">Progress</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-table') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-table') }}">Table</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-tooltip') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-tooltip') }}">Tooltip</a>
                    </li>
                    <li class="{{ Request::is('bootstrap-typography') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('bootstrap-typography') }}">Typography</a>
                    </li>
                </ul>
            </li>
            <li class="menu-header">Stisla</li>
            <li class="nav-item dropdown {{ $type_menu === 'components' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-th-large"></i>
                    <span>Components</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('components-article') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('components-article') }}">Article</a>
                    </li>
                    <li class="{{ Request::is('components-avatar') ? 'active' : '' }}">
                        <a class="nav-link beep beep-sidebar"
                            href="{{ url('components-avatar') }}">Avatar</a>
                    </li>
                    <li class="{{ Request::is('components-chat-box') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('components-chat-box') }}">Chat Box</a>
                    </li>
                    <li class="{{ Request::is('components-empty-state') ? 'active' : '' }}">
                        <a class="nav-link beep beep-sidebar"
                            href="{{ url('components-empty-state') }}">Empty State</a>
                    </li>
                    <li class="{{ Request::is('components-gallery') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('components-gallery') }}">Gallery</a>
                    </li>
                    <li class="{{ Request::is('components-hero') ? 'active' : '' }}">
                        <a class="nav-link beep beep-sidebar"
                            href="{{ url('components-hero') }}">Hero</a>
                    </li>
                    <li class="{{ Request::is('components-multiple-upload') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('components-multiple-upload') }}">Multiple Upload</a>
                    </li>
                    <li class="{{ Request::is('components-pricing') ? 'active' : '' }}">
                        <a class="nav-link beep beep-sidebar"
                            href="{{ url('components-pricing') }}">Pricing</a>
                    </li>
                    <li class="{{ Request::is('components-statistic') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('components-statistic') }}">Statistic</a>
                    </li>
                    <li class="{{ Request::is('components-tab') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('components-tab') }}">Tab</a>
                    </li>
                    <li class="{{ Request::is('components-table') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('components-table') }}">Table</a>
                    </li>
                    <li class="{{ Request::is('components-user') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('components-user') }}">User</a>
                    </li>
                    <li class="{{ Request::is('components-wizard') ? 'active' : '' }}">
                        <a class="nav-link beep beep-sidebar"
                            href="{{ url('components-wizard') }}">Wizard</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ $type_menu === 'forms' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="far fa-file-alt"></i> <span>Forms</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('forms-advanced-form') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('forms-advanced-form') }}">Advanced Form</a>
                    </li>
                    <li class="{{ Request::is('forms-editor') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('forms-editor') }}">Editor</a>
                    </li>
                    <li class="{{ Request::is('forms-validation') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('forms-validation') }}">Validation</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ $type_menu === 'modules' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-plug"></i> <span>Modules</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('modules-calendar') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('modules-calendar') }}">Calendar</a>
                    </li>
                    <li class="{{ Request::is('modules-chartjs') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('modules-chartjs') }}">ChartJS</a>
                    </li>
                    <li class="{{ Request::is('modules-datatables') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('modules-datatables') }}">DataTables</a>
                    </li>
                    <li class="{{ Request::is('modules-flag') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('modules-flag') }}">Flag</a>
                    </li>
                    <li class="{{ Request::is('modules-font-awesome') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('modules-font-awesome') }}">Font Awesome</a>
                    </li>
                    <li class="{{ Request::is('modules-ion-icons') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('modules-ion-icons') }}">Ion Icons</a>
                    </li>
                    <li class="{{ Request::is('modules-owl-carousel') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('modules-owl-carousel') }}">Owl Carousel</a>
                    </li>
                    <li class="{{ Request::is('modules-sparkline') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('modules-sparkline') }}">Sparkline</a>
                    </li>
                    <li class="{{ Request::is('modules-sweet-alert') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('modules-sweet-alert') }}">Sweet Alert</a>
                    </li>
                    <li class="{{ Request::is('modules-toastr') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('modules-toastr') }}">Toastr</a>
                    </li>
                    <li class="{{ Request::is('modules-vector-map') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('modules-vector-map') }}">Vector Map</a>
                    </li>
                    <li class="{{ Request::is('modules-weather-icon') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('modules-weather-icon') }}">Weather Icon</a>
                    </li>
                </ul>
            </li>
            <li class="menu-header">Pages</li>
            <li class="nav-item dropdown {{ $type_menu === 'auth' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="far fa-user"></i> <span>Auth</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('auth-forgot-password') ? 'active' : '' }}">
                        <a href="{{ url('auth-forgot-password') }}">Forgot Password</a>
                    </li>
                    <li class="{{ Request::is('auth-login') ? 'active' : '' }}">
                        <a href="{{ url('auth-login') }}">Login</a>
                    </li>
                    <li class="{{ Request::is('auth-login2') ? 'active' : '' }}">
                        <a class="beep beep-sidebar"
                            href="{{ url('auth-login2') }}">Login 2</a>
                    </li>
                    <li class="{{ Request::is('auth-register') ? 'active' : '' }}">
                        <a href="{{ url('auth-register') }}">Register</a>
                    </li>
                    <li class="{{ Request::is('auth-reset-password') ? 'active' : '' }}">
                        <a href="{{ url('auth-reset-password') }}">Reset Password</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ $type_menu === 'error' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-exclamation"></i>
                    <span>Errors</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('error-403') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('error-403') }}">403</a>
                    </li>
                    <li class="{{ Request::is('error-404') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('error-404') }}">404</a>
                    </li>
                    <li class="{{ Request::is('error-500') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('error-500') }}">500</a>
                    </li>
                    <li class="{{ Request::is('error-503') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('error-503') }}">503</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ $type_menu === 'features' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-bicycle"></i> <span>Features</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('features-activities') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('features-activities') }}">Activities</a>
                    </li>
                    <li class="{{ Request::is('features-post-create') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('features-post-create') }}">Post Create</a>
                    </li>
                    <li class="{{ Request::is('features-post') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('features-post') }}">Posts</a>
                    </li>
                    <li class="{{ Request::is('features-profile') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('features-profile') }}">Profile</a>
                    </li>
                    <li class="{{ Request::is('features-settings') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('features-settings') }}">Settings</a>
                    </li>
                    <li class="{{ Request::is('features-setting-detail') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('features-setting-detail') }}">Setting Detail</a>
                    </li>
                    <li class="{{ Request::is('features-tickets') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('features-tickets') }}">Tickets</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ $type_menu === 'utilities' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-ellipsis-h"></i>
                    <span>Utilities</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('utilities-contact') ? 'active' : '' }}">
                        <a href="{{ url('utilities-contact') }}">Contact</a>
                    </li>
                    <li class="{{ Request::is('utilities-invoice') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('utilities-invoice') }}">Invoice</a>
                    </li>
                    <li class="{{ Request::is('utilities-subscribe') ? 'active' : '' }}">
                        <a href="{{ url('utilities-subscribe') }}">Subscribe</a>
                    </li>
                </ul>
            </li>
            <li class="{{ Request::is('credits') ? 'active' : '' }}">
                <a class="nav-link"
                    href="{{ url('credits') }}"><i class="fas fa-pencil-ruler">
                    </i> <span>Credits</span>
                </a>
            </li> --}}
        </ul>
    </aside>
</div>
