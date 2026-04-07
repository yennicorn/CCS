@extends('layouts.super-admin')

@section('page_title', 'Backup')
@section('page_subtitle', 'Database backup')

@section('content')
<section class="split">
    <article class="panel backup-card">
        <div class="panel-head"><h3> Database Backup</h3></div>
        <p class="muted">Generate a local backup file for records recovery and safekeeping.</p>
        <form method="POST" action="{{ route('super-admin.backup.database') }}">
            @csrf
            <button class="btn btn-backup" type="submit"> Generate Backup</button>
        </form>
    </article>
</section>
@endsection
