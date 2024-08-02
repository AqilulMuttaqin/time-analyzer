@php
    $userData = session('user');
@endphp
<nav class="navbar">
    <a href="#" class="sidebar-toggler">
        <i data-feather="menu"></i>
    </a>
    <div class="navbar-content">
        <div class="d-flex align-items-center pt-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">{{ $title === 'Dashboard' ? 'Home' : 'Pages' }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
        </div>
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <p>{{ ucwords($userData['nama']) }}</p>
                    <img class="wd-30 ht-30 rounded-circle" src="{{ asset('assets/images/profile.jpg') }}" alt="profile">
                </a>
                <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                    <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                        <div class="mb-3">
                            <img class="wd-80 ht-80 rounded-circle" src="{{ asset('assets/images/profile.jpg') }}" alt="">
                        </div>
                        <div class="text-center">
                            <p class="tx-16 fw-bolder">{{ ucwords($userData['nama']) }}</p>
                            <p class="tx-12 text-muted">( NIK: {{ ucwords($userData['nik']) }} )</p>
                            <p class="tx-12 text-muted">{{ ucwords($userData['role']) }} ({{ ucwords($userData['section']['nama']) }} Section)</p>
                        </div>
                    </div>
                    <ul class="list-unstyled p-1">
                        <li class="dropdown-item py-2" id="logout-link" style="cursor: pointer">
                            <a href="#" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="log-out"></i>
                                <span>Log Out</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>

<script>
    document.getElementById('logout-link').addEventListener('click', function(event) {
        event.preventDefault();

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('logout') }}';

        var csrfToken = document.createElement('input');
        csrfToken.setAttribute('type', 'hidden');
        csrfToken.setAttribute('name', '_token');
        csrfToken.setAttribute('value', '{{ csrf_token() }}');

        form.appendChild(csrfToken);

        document.body.appendChild(form);
        form.submit();
    });
</script>
