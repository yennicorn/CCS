@extends('layouts.master-admin')

@section('page_title', 'Manage Enrollment')
@section('page_subtitle', 'Final approval queue for reviewed applications')

@section('content')
<section class="panel">
    <div class="panel-head"><h3> Final Approval Queue</h3><p class="muted">Reviewed applications awaiting final approval.</p></div>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Applicant</th>
                <th>Grade Level</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($applications as $app)
                <tr>
                    <td>
                        <div class="applicant-cell">
                            <div class="app-avatar">{{ strtoupper(substr($app->learner_full_name, 0, 1)) }}</div>
                            <div>
                                <strong>{{ $app->learner_full_name }}</strong>
                                <p class="muted">Application #{{ $app->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td>{{ $app->grade_level }}</td>
                    <td><span class="badge {{ $app->status }}">{{ strtoupper($app->status) }}</span></td>
                    <td>
                        <div class="action-row">
                            <form method="POST" action="{{ route('master.applications.decide', $app) }}">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button class="btn" type="submit">Enroll Student</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No reviewed applications in queue.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $applications->links() }}</div>
</section>
@endsection
