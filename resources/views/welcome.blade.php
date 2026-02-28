<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cabugbugan Community School</title>
<link rel="stylesheet" href="{{ asset('css/ccs-ui.css') }}">
</head>
<body>
<header class="welcome-hero">
    <div class="welcome-hero-inner">
        <div class="welcome-topbar">
            <div class="welcome-brand">
                <img src="{{ asset('images/branding/logo.png') }}" alt="School logo">
                <div>
                    <strong>Cabugbugan Community School</strong>
                    <small>Official Information and Online Enrollment System</small>
                </div>
            </div>
            <div class="welcome-actions">
                <a class="btn btn-secondary" href="{{ route('login') }}">Login</a>
                <a class="btn" href="{{ route('register') }}">Register</a>
            </div>
        </div>

        <div class="welcome-headline">
            <div>
                <h1>Welcome to Cabugbugan Community School</h1>
                <p>Modernized enrollment, institutional information access, and transparent academic governance for the community.</p>
            </div>
        </div>
    </div>
</header>

<main class="container welcome-main">
    <section class="panel welcome-banner-card">
        <img src="{{ asset('images/branding/banner.jpg') }}" alt="School Banner" class="welcome-banner-img">
    </section>

    <section class="welcome-grid">
        <article class="panel welcome-info">
            <div class="panel-head"><h2>School Background and Institutional Identity</h2></div>
            <p>Cabugbugan Community School is committed to accessible, values-based, and quality education for learners in the community.</p>
            <h3>Mission</h3>
            <p>Provide inclusive, learner-centered education supported by responsible governance and community partnership.</p>
            <h3>Vision</h3>
            <p>Develop disciplined, competent, and socially responsible students through safe and supportive schooling.</p>
            <h3>Core Services</h3>
            <ul class="welcome-list">
                <li>Online enrollment application submission and monitoring</li>
                <li>Institutional announcements and academic events feed</li>
                <li>Transparent status timeline and governance updates</li>
            </ul>
        </article>

        <aside class="panel welcome-side">
            <div class="panel-head"><h2>Campus Profile</h2></div>
            <img src="{{ asset('images/branding/campus.jpg') }}" alt="School Campus" class="welcome-campus-img">
            <div class="contact-card">
                <p><strong>Contact Details</strong></p>
                <p>Email: ccs.edu.ph</p>
                <p>Phone: XX-XXX-XXX</p>
                <p>Address: Cabugbugan, Tagudin, Ilocos Sur</p>
            </div>
        </aside>
    </section>

    <section class="panel">
        <div class="panel-head"><h2>Announcements and Events</h2><p class="muted">Latest official updates</p></div>
        @forelse($announcements as $item)
            <article class="feed-post">
                <h3>{{ $item->title }}</h3>
                <div class="feed-meta">Published: {{ optional($item->publish_at)->format('M d, Y h:i A') ?? $item->created_at->format('M d, Y h:i A') }}</div>
                <p>{{ $item->content }}</p>
                @if($item->image_path)
                    <img src="{{ asset('storage/'.$item->image_path) }}" alt="Announcement image">
                @endif
            </article>
        @empty
            <p>No announcements posted yet.</p>
        @endforelse
    </section>
</main>
</body>
</html>

