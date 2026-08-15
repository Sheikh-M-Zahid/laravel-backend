<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Agri-Advisory Platform')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Source+Sans+3:wght@400;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.7/axios.min.js"></script>
</head>
<body>
    <div class="season-band"><span class="kharif-1"></span><span class="kharif-2"></span><span class="rabi"></span></div>
    <nav class="navbar">
        <a href="{{ url('/') }}" class="navbar-brand">🌾 Agri-Advisory</a>
        <div class="navbar-links">
            <a href="{{ route('predictions') }}">What can it predict?</a>
            @auth
                <a href="{{ route('hub') }}">Roles</a>
                @if (auth()->user()->is_admin || auth()->user()->is_super_admin)
                    <a href="{{ route('admin.dashboard') }}">Admin</a>
                    <a href="{{ route('admins.directory') }}">Admins</a>
                @endif
                @if (auth()->user()->is_super_admin)
                    <a href="{{ route('super-admin.dashboard') }}">Super Admin</a>
                @endif

                @php $unread = auth()->user()->appNotifications()->whereNull('read_at')->latest()->take(10)->get(); @endphp
                <div class="notif-bell-wrap">
                    <button type="button" class="notif-bell" onclick="document.getElementById('notif-dropdown').classList.toggle('open')">
                        🔔
                        @if ($unread->count() > 0)
                            <span class="notif-badge">{{ $unread->count() }}</span>
                        @endif
                    </button>
                    <div class="notif-dropdown" id="notif-dropdown">
                        <div class="notif-dropdown-head">
                            <strong>Notifications</strong>
                            @if ($unread->count() > 0)
                                <form method="POST" action="{{ route('notifications.read-all') }}">
                                    @csrf
                                    <button type="submit" class="btn-link" style="font-size:0.78rem;">Mark all read</button>
                                </form>
                            @endif
                        </div>
                        @forelse ($unread as $n)
                            <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="notif-item">
                                @csrf
                                <button type="submit" class="notif-item-btn">
                                    <strong>{{ $n->title }}</strong>
                                    <span>{{ \Illuminate\Support\Str::limit($n->body, 90) }}</span>
                                </button>
                            </form>
                        @empty
                            <p class="muted" style="padding:12px;">No new notifications.</p>
                        @endforelse
                    </div>
                </div>

                <span class="navbar-user">{{ Auth::user()->name }} · {{ str_replace('_', ' ', Auth::user()->role) }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-link">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}">Log in</a>
                <a href="{{ route('register') }}" class="btn-cta-outline" style="padding:7px 18px;">Get started</a>
            @endauth
        </div>
    </nav>

    <main class="{{ View::hasSection('full_width') ? '' : 'container' }}">
        @if (session('status'))
            <div class="alert alert-success container" style="margin-top:24px;">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error container" style="margin-top:24px;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        &copy; {{ date('Y') }} Smart Agri-Advisory Platform. All rights reserved.
    </footer>

    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] =
            document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
