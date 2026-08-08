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
            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                </a>
            </li>
            <!--end::Fullscreen Toggle-->

          @php use Illuminate\Support\Facades\Storage; @endphp

<!--begin::User Menu Dropdown-->
<li class="nav-item dropdown user-menu">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
        <img
            src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : asset('assets/img/logo-school.png') }}"
            class="user-image rounded-circle shadow"
            alt="User Image" />
        <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
        <!--begin::User Image-->
        <li class="user-header text-bg-primary">
            <img
                src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : asset('assets/img/logo-school.png') }}"
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
        <!--begin::Menu Footer-->
        <li class="user-footer">
            <a href="{{ route('user.profile') }}" class="btn btn-default btn-flat">
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