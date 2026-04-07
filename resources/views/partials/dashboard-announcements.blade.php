@php
    $routePrefix = $routePrefix ?? 'admin';
    $items = $announcements ?? collect();
@endphp

<section class="panel dashboard-announcements">
    <div class="panel-head">
        <div class="panel-head__title">
            <h3><span class="icon-inline"><x-icon name="announcements" /> Announcements</span></h3>
        </div>
        <div class="panel-head__meta">
            <a class="btn btn-secondary" href="{{ route($routePrefix.'.announcements.index') }}">Manage</a>
            <a class="btn" href="{{ route($routePrefix.'.announcements.create') }}">Create</a>
        </div>
    </div>

    @forelse($items as $a)
        @php
            $excerpt = \Illuminate\Support\Str::limit(
                trim(strip_tags((string) $a->renderedContent())),
                180
            );
        @endphp

        <article class="dashboard-announcement-item">
            <div class="dashboard-announcement-item__top">
                <div class="dashboard-announcement-item__title">
                    <a class="link-plain" href="{{ route($routePrefix.'.announcements.show', $a) }}">
                        {{ $a->title }}
                    </a>
                    @php($status = method_exists($a, 'statusLabel') ? $a->statusLabel() : '')
                    @if($status !== '')
                        <span class="badge dashboard-announcement-badge">{{ $status }}</span>
                    @endif
                </div>
                <div class="dashboard-announcement-item__meta muted">
                    {{ optional($a->publish_at)->format('m/d/Y h:i A') ?? optional($a->created_at)->format('m/d/Y h:i A') }}
                    @if(optional($a->author)->full_name)
                        &middot; {{ $a->author->full_name }}
                    @endif
                </div>
            </div>

            @if(($excerpt ?? '') !== '')
                <p class="dashboard-announcement-item__excerpt muted">{{ $excerpt }}</p>
            @endif
        </article>
    @empty
        <p class="muted">No announcements yet.</p>
    @endforelse
</section>

