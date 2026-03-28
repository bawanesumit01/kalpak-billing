<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login — Kalpak Billing</title>
  <link href="{{ asset('public/assets/css/style.min.css') }}" rel="stylesheet"/>
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-box {
      background: #fff;
      border-radius: 12px;
      padding: 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    .login-logo {
      text-align: center;
      margin-bottom: 30px;
    }
    .login-logo h2 {
      font-size: 26px;
      font-weight: 700;
      color: #2962FF;
      margin: 10px 0 4px;
    }
    .login-logo p {
      color: #888;
      font-size: 14px;
      margin: 0;
    }
    .login-logo i {
      font-size: 48px;
      color: #2962FF;
    }
  </style>
</head>
<body>

<div class="login-box">

  {{-- Logo --}}
  <div class="login-logo">
    <i class="mdi mdi-receipt"></i>
    <h2>Kalpak Billing</h2>
    <p>Sign in to your account</p>
  </div>

  {{-- Alerts --}}
  @if(session('error'))
    <div class="alert alert-danger">
      <i class="mdi mdi-alert-circle"></i> {{ session('error') }}
    </div>
  @endif
  @if(session('success'))
    <div class="alert alert-success">
      <i class="mdi mdi-check-circle"></i> {{ session('success') }}
    </div>
  @endif

  {{-- Login Form --}}
  <form action="{{ route('login.post') }}" method="POST">
    @csrf

    <div class="form-group my-3">
      <label class="font-weight-bold">Username</label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="mdi mdi-account"></i></span>
        </div>
        <input type="text" name="username"
               class="form-control @error('username') is-invalid @enderror"
               placeholder="Enter username"
               value="{{ old('username') }}"
               autofocus required>
      </div>
      @error('username')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group my-3">
      <label class="font-weight-bold">Password</label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="mdi mdi-lock"></i></span>
        </div>
        <input type="password" name="password"
               class="form-control  @error('password') is-invalid @enderror"
               placeholder="Enter password" required>
        <div class="input-group-append">
          <span class="input-group-text" style="cursor:pointer" onclick="togglePassword()">
            <i class="mdi mdi-eye" id="eye-icon"></i>
          </span>
        </div>
      </div>
      @error('password')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary btn-lg btn-block mt-3">
      <i class="mdi mdi-login"></i> Sign In
    </button>

  </form>

</div>

<script>
  function togglePassword() {
    const input   = document.querySelector('input[name="password"]');
    const icon    = document.getElementById('eye-icon');
    const isText  = input.type === 'text';
    input.type    = isText ? 'password' : 'text';
    icon.className = isText ? 'mdi mdi-eye' : 'mdi mdi-eye-off';
  }
</script>

</body>
</html>