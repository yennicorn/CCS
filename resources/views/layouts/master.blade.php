<!doctype html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cabugbugan Community School' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/CCS_logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/ccs-ui.css') }}">
</head>
<body class="admin-portal">
@php
    $signedUser = auth()->user();
    $signedName = $signedUser?->displayName() ?? 'User';

    $signedRole = (string) ($signedUser->role ?? 'user');
    $signedRoleLabel = $signedUser?->roleLabel() ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $signedRole));

    $signedInitials = $signedUser?->initials() ?? 'U';
    $signedPhotoUrl = $signedUser?->profilePhotoUrl();

    $settingsRoute = match ($signedRole) {
        'super_admin' => route('super-admin.settings.index'),
        'admin' => route('admin.settings.index'),
        default => null,
    };
@endphp
<div class="app-shell">
    <aside class="sidebar" id="adminSidebar">
        <div class="sidebar-mobile-head">
            <strong>Navigation</strong>
            <button type="button" class="sidebar-close" data-sidebar-close aria-label="Close navigation">Close</button>
        </div>
        <div class="sidebar-brand">
            <img src="{{ asset('images/branding/CCS_logo.png') }}" alt="School logo">
            <div>
                <strong>Cabugbugan Community School</strong>
                <small>Information and Enrollment System</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            @yield('sidebar')
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                @if($signedPhotoUrl)
                    <div class="sidebar-avatar" aria-hidden="true">
                        <img src="{{ $signedPhotoUrl }}" alt="Profile photo">
                    </div>
                @else
                    <div class="sidebar-avatar sidebar-avatar--placeholder" aria-hidden="true">{{ $signedInitials ?: 'U' }}</div>
                @endif
                <div class="sidebar-user__meta">
                    <p class="muted">Signed in as:</p>
                    <p class="sidebar-user__name"><strong>{{ $signedName }}</strong></p>
                    <p class="sidebar-user__role">{{ $signedRoleLabel }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="js-logout-form">
                @csrf
                <button class="btn btn-logout w-full" type="submit">Logout</button>
            </form>
        </div>
    </aside>
    <button type="button" class="sidebar-backdrop" data-sidebar-close aria-label="Close navigation"></button>

    <div class="main-area">
        <header class="topbar">
            <div class="topbar-inner">
                <button type="button" class="sidebar-toggle" data-sidebar-open aria-label="Open navigation">
                    <span class="sidebar-toggle__icon" aria-hidden="true">
                        <span class="sidebar-toggle__bar"></span>
                        <span class="sidebar-toggle__bar"></span>
                        <span class="sidebar-toggle__bar"></span>
                    </span>
                </button>
                <div>
                    @hasSection('page_title_above')
                        <div class="page-title-above print-hide">
                            @yield('page_title_above')
                        </div>
                    @endif
                    <h1 class="page-title">@yield('page_title', 'Dashboard')</h1>
                    <p class="muted">@yield('page_subtitle', 'Cabugbugan Community School Management Portal')</p>
                </div>
                @if($settingsRoute)
                    <a class="topbar-user" href="{{ $settingsRoute }}" aria-label="Open account settings">
                        @if($signedPhotoUrl)
                            <span class="topbar-user__avatar" aria-hidden="true">
                                <img src="{{ $signedPhotoUrl }}" alt="Profile photo">
                            </span>
                        @else
                            <span class="topbar-user__avatar topbar-user__avatar--placeholder" aria-hidden="true">{{ $signedInitials ?: 'U' }}</span>
                        @endif
                        <span class="topbar-user__meta">
                            <span class="topbar-user__name">{{ $signedName }}</span>
                            <span class="topbar-user__role">{{ $signedRoleLabel }}</span>
                        </span>
                    </a>
                @endif
            </div>
        </header>

        <main class="container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
<div class="logout-modal" id="logoutConfirmModal" aria-hidden="true">
    <div class="logout-modal__backdrop" data-dismiss-logout></div>
    <div class="logout-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="logoutConfirmTitle" aria-describedby="logoutConfirmDesc">
        <div class="logout-modal__icon" aria-hidden="true">!</div>
        <h2 id="logoutConfirmTitle">Confirm Logout</h2>
        <p id="logoutConfirmDesc">Are you sure you want to log out from your account?</p>
        <div class="logout-modal__actions">
            <button type="button" class="btn btn-danger" data-dismiss-logout>Cancel</button>
            <button type="button" class="btn btn-logout" id="logoutConfirmBtn">Log Out</button>
        </div>
    </div>
</div>
<script>
(() => {
    const body = document.body;
    const sidebar = document.getElementById('adminSidebar');
    const sidebarOpenButtons = document.querySelectorAll('[data-sidebar-open]');
    const sidebarCloseButtons = document.querySelectorAll('[data-sidebar-close]');
    const mobileSidebarQuery = window.matchMedia('(max-width: 1000px)');
    const modal = document.getElementById('logoutConfirmModal');
    const confirmButton = document.getElementById('logoutConfirmBtn');

    const closeSidebar = () => {
        body.classList.remove('sidebar-open');
    };

    const openSidebar = () => {
        if (!mobileSidebarQuery.matches) {
            return;
        }
        body.classList.add('sidebar-open');
    };

    sidebarOpenButtons.forEach((button) => {
        button.addEventListener('click', openSidebar);
    });

    sidebarCloseButtons.forEach((button) => {
        button.addEventListener('click', closeSidebar);
    });

    if (sidebar) {
        sidebar.querySelectorAll('a.sidebar-link').forEach((link) => {
            link.addEventListener('click', () => {
                if (mobileSidebarQuery.matches) {
                    closeSidebar();
                }
            });
        });
    }

    mobileSidebarQuery.addEventListener('change', () => {
        if (!mobileSidebarQuery.matches) {
            closeSidebar();
        }
    });

    if (modal && confirmButton) {
        const dismissButtons = modal.querySelectorAll('[data-dismiss-logout]');
        const logoutForms = document.querySelectorAll('.js-logout-form');
        let pendingForm = null;

        const openModal = (form) => {
            pendingForm = form;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
            confirmButton.focus();
        };

        const closeModal = () => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
            pendingForm = null;
        };

        logoutForms.forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.skipConfirm === '1') {
                    return;
                }

                event.preventDefault();
                openModal(form);
            });
        });

        confirmButton.addEventListener('click', () => {
            if (!pendingForm) {
                return;
            }

            pendingForm.dataset.skipConfirm = '1';
            pendingForm.submit();
        });

        dismissButtons.forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }

            if (modal.classList.contains('is-open')) {
                closeModal();
                return;
            }

            if (body.classList.contains('sidebar-open')) {
                closeSidebar();
            }
        });
    } else {
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && body.classList.contains('sidebar-open')) {
                closeSidebar();
            }
        });
    }
})();
</script>
</body>
</html>
