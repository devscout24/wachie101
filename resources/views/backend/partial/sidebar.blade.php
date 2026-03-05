<!--APP-SIDEBAR-->
<div class="sticky">
    <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
    <div class="app-sidebar">
        <div class="side-header">
            <a class="header-brand1" href="{{ route('dashboard') }}">
                <img src="{{ asset($systemSettings->system_logo ?? 'uploads/systems/logo/default-logo.png') }}"
                    class="header-brand-img" alt="logo" style="height: 50px; width: 200px">
            </a>
        </div>
        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg>
            </div>
            <ul class="side-menu">
                <li>
                    <h3>Menu</h3>
                </li>

                <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="{{ route('dashboard') }}">
                        <i class="side-menu__icon fa fa-home" style="color: #4F46E5;"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>

                <li>
                    <h3>Components</h3>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="#">
                        <i class="side-menu__icon fa fa-star-o" style="color: #F59E0B;"></i>
                        <span class="side-menu__label">Amenities</span>
                        <i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{ route('admin.amenity.create') }}" class="slide-item">Create Amenity</a></li>
                        <li><a href="{{ route('admin.amenity.index') }}" class="slide-item">Manage Amenity</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="#">
                        <i class="side-menu__icon fa fa-building" style="color: #6366F1;"></i>
                        <span class="side-menu__label">Properties</span>
                        <i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{ route('admin.property.create') }}" class="slide-item">Create Property</a></li>
                        <li><a href="{{ route('admin.property.index') }}" class="slide-item">Manage Property</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="#">
                        <i class="side-menu__icon fa fa-book" style="color: #f11010;"></i>
                        <span class="side-menu__label">Bookings</span>
                        <i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{ route('admin.booking.index') }}" class="slide-item">Manage Bookings</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="#">
                        <i class="side-menu__icon fa fa-users" style="color: #14B8A6;"></i>
                        <span class="side-menu__label">Team Members</span>
                        <i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{ route('admin.team.create') }}" class="slide-item">Create Team Member</a></li>
                        <li><a href="{{ route('admin.team.index') }}" class="slide-item">Manage Team Members</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="#">
                        <i class="side-menu__icon fa fa-file-text-o" style="color: #64748B;"></i>
                        <span class="side-menu__label">Dynamic Page</span>
                        <i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{ route('dynamicpage.create') }}" class="slide-item">Create Dynamic Page</a></li>
                        <li><a href="{{ route('dynamicpage.index') }}" class="slide-item">Manage Dynamic Page</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="#">
                        <i class="side-menu__icon fa fa-cogs" style="color: #475569;"></i>
                        <span class="side-menu__label">System Setting</span>
                        <i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{ route('profile.index') }}" class="slide-item">Profile Setting</a></li>
                        <li><a href="{{ route('system-settings.edit') }}" class="slide-item">System Setting</a></li>
                        {{-- <li><a href="{{ route('admin.settings.edit') }}" class="slide-item">Admin Setting</a></li> --}}
                        <li><a href="{{ route('settings.mail') }}" class="slide-item">Mail Setting</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="#">
                        <i class="side-menu__icon fa fa-user-circle" style="color: #6B7280;"></i>
                        <span class="side-menu__label">Users</span>
                        <i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{ route('users.create') }}" class="slide-item">Create User</a></li>
                        <li><a href="{{ route('users.index') }}" class="slide-item">Manage User</a></li>
                    </ul>
                </li>

            </ul>
            <div class="slide-right" id="slide-right">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg>
            </div>
        </div>
    </div>
</div>
<!--/APP-SIDEBAR-->