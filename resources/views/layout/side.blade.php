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
            <li class="nav-item">
                <a href="#analisis" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="analisis">
                    <i class="link-icon" data-feather="activity"></i>
                    <span class="link-title">Analisis</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="analisis">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('report-monthly') }}" class="nav-link {{ $title === 'Report Monthly' ? 'active' : '' }}">Report Monthly</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('target-downtime') }}" class="nav-link {{ $title === 'Target Downtime' ? 'active' : '' }}">Target Downtime</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a href="#area" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="area">
                    <i class="link-icon" data-feather="map-pin"></i>
                    <span class="link-title">Area</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="area">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('golongan') }}" class="nav-link {{ $title === 'Golongan' ? 'active' : '' }}">Golongan</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('subgolongan') }}" class="nav-link {{ $title === 'Sub Golongan' ? 'active' : '' }}">Sub Golongan</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a href="#code" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="code">
                    <i class="link-icon" data-feather="code"></i>
                    <span class="link-title">Code</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="code">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('downtimecode') }}" class="nav-link {{ $title === 'Downtime Code' ? 'active' : '' }}">Downtime Code</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('section') }}" class="nav-link {{ $title === 'Section' ? 'active' : '' }}">Section</a>
                        </li>
                    </ul>
                </div>
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
