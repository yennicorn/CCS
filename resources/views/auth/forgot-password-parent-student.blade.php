<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Parent/Student Password Recovery</title>
<link rel="stylesheet" href="{{ asset('css/ccs-ui.css') }}">
</head>
<body>
<div class="auth-portal">
    <section class="auth-left">
        <div class="auth-brand">
            <div class="auth-seal">
                <img src="{{ asset('images/branding/CCS_logo.png') }}" alt="School seal logo">
            </div>
            <h1>Parent/Student Recovery</h1>
            <p>This page is for Parent and Student account recovery.</p>
        </div>
    </section>

    <section class="auth-right">
        <div class="auth-shell-card auth-compact">
            <div class="auth-shell-head">
                <h2>Forgot Password</h2>
                <p>Recover Parent/Student access.</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.recover.parent-student') }}">
                @csrf
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>

                <label>Full Name</label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Enter full name" required>

                <label>New Password</label>
                <input type="password" name="password" required>

                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation" required>

                <button class="btn btn-auth mt-12" type="submit">Recover Password</button>
            </form>

            <p class="auth-foot"><a href="{{ route('login') }}">Back to Login</a></p>
        </div>
    </section>
</div>
</body>
</html>
