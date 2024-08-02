<nav class="sidebar">
    <div class="sidebar-header">
        <a class="sidebar-brand">
            DT<span class="fw-bold">Analyzer</span>
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item nav-category">Home</li>
            <li class="nav-item {{ $title === 'Dashboard' ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item nav-category">Pages</li>
            <li class="nav-item {{ $title === 'Report Monthly' ? 'active' : '' }}">
                <a href="{{ route('report-monthly') }}" class="nav-link">
                    <i class="link-icon" data-feather="airplay"></i>
                    <span class="link-title">Report Monthly</span>
                </a>
            </li>
            <li class="nav-item {{ $title === 'Downtime' ? 'active' : '' }}">
                <a href="{{ route('downtime') }}" class="nav-link">
                    <i class="link-icon" data-feather="alert-triangle"></i>
                    <span class="link-title">Downtime</span>
                </a>
            </li>
            <li class="nav-item {{ $title === 'Effective' ? 'active' : '' }}">
                <a href="{{ route('effective') }}" class="nav-link">
                    <i class="link-icon" data-feather="clock"></i>
                    <span class="link-title">Effective</span>
                </a>
            </li>
            <li class="nav-item {{ $title === 'Target Downtime' ? 'active' : '' }}">
                <a href="{{ route('target-downtime') }}" class="nav-link">
                    <i class="link-icon" data-feather="activity"></i>
                    <span class="link-title">Target Downtime</span>
                </a>
            </li>
            <li class="nav-item {{ $title === 'Sub Golongan' ? 'active' : '' }}">
                <a href="{{ route('subgolongan') }}" class="nav-link">
                    <i class="link-icon" data-feather="git-commit"></i>
                    <span class="link-title">Sub Golongan</span>
                </a>
            </li>
            <li class="nav-item {{ $title === 'Golongan' ? 'active' : '' }}">
                <a href="{{ route('golongan') }}" class="nav-link">
                    <i class="link-icon" data-feather="git-pull-request"></i>
                    <span class="link-title">Golongan</span>
                </a>
            </li>
            <li class="nav-item {{ $title === 'Section' ? 'active' : '' }}">
                <a href="{{ route('section') }}" class="nav-link">
                    <i class="link-icon" data-feather="layers"></i>
                    <span class="link-title">Section</span>
                </a>
            </li>
            <li class="nav-item {{ $title === 'Downtime Code' ? 'active' : '' }}">
                <a href="{{ route('downtimecode') }}" class="nav-link">
                    <i class="link-icon" data-feather="info"></i>
                    <span class="link-title">Downtime Code</span>
                </a>
            </li>
            @if (auth()->user()->role == 'admin')
                <li class="nav-item {{ $title === 'Users' ? 'active' : '' }}">
                    <a href="{{ route('user') }}" class="nav-link">
                        <i class="link-icon" data-feather="users"></i>
                        <span class="link-title">Users</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>
