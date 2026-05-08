<nav class="app-header navbar navbar-expand bg-body">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="bi bi-house-door me-1"></i> Dashboard
                </a>
            </li>
        </ul>
        <!--end::Start Navbar Links-->

        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">
            <!--begin::Messages Dropdown Menu-->
            <li class="nav-item dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-chat-text"></i>
                    <span class="navbar-badge badge text-bg-danger">{{ $unreadMessagesCount ?? 0 }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <span class="dropdown-item dropdown-header">{{ $unreadMessagesCount ?? 0 }} Notifications</span>
                    
                    @if(isset($recentMessages) && count($recentMessages) > 0)
                        @foreach($recentMessages as $message)
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <img
                                            src="{{ $message['avatar'] ?? asset('assets/img/default-avatar.jpg') }}"
                                            alt="User Avatar"
                                            class="img-size-50 rounded-circle me-3" />
                                    </div>
                                    <div class="flex-grow-1">
                                        <h3 class="dropdown-item-title">
                                            {{ $message['name'] }}
                                            <span class="float-end fs-7 text-{{ $message['starred'] ? 'danger' : 'secondary' }}">
                                                <i class="bi bi-star-fill"></i>
                                            </span>
                                        </h3>
                                        <p class="fs-7">{{ $message['preview'] }}</p>
                                        <p class="fs-7 text-secondary">
                                            <i class="bi bi-clock-fill me-1"></i> {{ $message['time'] }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="dropdown-item text-center text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada pesan
                        </div>
                    @endif
                    
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">Lihat Semua Pesan</a>
                </div>
            </li>
            <!--end::Messages Dropdown Menu-->

            <!--begin::Notifications Dropdown Menu-->
            <li class="nav-item dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-bell-fill"></i>
                    <span class="navbar-badge badge text-bg-warning">{{ $unreadNotificationsCount ?? 0 }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <span class="dropdown-item dropdown-header">{{ $unreadNotificationsCount ?? 0 }} Notifications</span>
                    
                    @if(isset($recentNotifications) && count($recentNotifications) > 0)
                        @foreach($recentNotifications as $notification)
                            <div class="dropdown-divider"></div>
                            <a href="{{ $notification['link'] ?? '#' }}" class="dropdown-item">
                                <i class="bi {{ $notification['icon'] ?? 'bi-bell' }} me-2"></i> 
                                {{ $notification['message'] }}
                                <span class="float-end text-secondary fs-7">{{ $notification['time'] }}</span>
                            </a>
                        @endforeach
                    @else
                        <div class="dropdown-item text-center text-muted">
                            <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                            Tidak ada notifikasi
                        </div>
                    @endif
                    
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">Lihat Semua Notifikasi</a>
                </div>
            </li>
            <!--end::Notifications Dropdown Menu-->

            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                </a>
            </li>
            <!--end::Fullscreen Toggle-->

            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <img
                        src="{{ Auth::user()->avatar ?? asset('assets/img/default-avatar.jpg') }}"
                        class="user-image rounded-circle shadow"
                        alt="User Image" />
                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <!--begin::User Image-->
                    <li class="user-header text-bg-primary">
                        <img
                            src="{{ Auth::user()->avatar ?? asset('assets/img/default-avatar.jpg') }}"
                            class="rounded-circle shadow"
                            alt="User Image" />
                        <p>
                            {{ Auth::user()->name }}
                            <small>
                                {{ ucfirst(Auth::user()->roles->first()->name ?? 'User') }}
                                @if(Auth::user()->created_at)
                                    <br>Member since {{ Auth::user()->created_at->format('M. Y') }}
                                @endif
                            </small>
                        </p>
                    </li>
                    <!--end::User Image-->
                    <!--begin::Menu Body-->
                    <li class="user-body">
                        <div class="row">
                            <div class="col-6 text-center">
                                <a href="{{ route('user.edit', Auth::user()->id) }}">Profile</a>
                            </div>
                            <div class="col-6 text-center">
                                <a href="#">Settings</a>
                            </div>
                        </div>
                    </li>
                    <!--end::Menu Body-->
                    <!--begin::Menu Footer-->
                    <li class="user-footer">
                        <a href="{{ route('user.edit', Auth::user()->id) }}" class="btn btn-default btn-flat">
                            <i class="bi bi-person"></i> Profile
                        </a>
                        <a href="{{ route('logout') }}" class="btn btn-default btn-flat float-end" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Sign out
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="GET" style="display: none;">
                            @csrf
                        </form>
                    </li>
                    <!--end::Menu Footer-->
                </ul>
            </li>
            <!--end::User Menu Dropdown-->
        </ul>
        <!--end::End Navbar Links-->
    </div>
    <!--end::Container-->
</nav>