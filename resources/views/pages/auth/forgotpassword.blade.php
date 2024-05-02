@extends('layout.auth')
@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner py-4">
        <!-- Forgot Password -->
        <div class="card">
          <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center mb-4 mt-2">
              <img src="{{ asset('template/assets/img/meepo.png') }}" class="img-fluid" />
            </div>
            <!-- /Logo -->
            <h4 class="mb-1 pt-2">Forgot Password? 🔒</h4>
            <p class="mb-4">Enter your email and we'll send you instructions to reset your password</p>
            <form id="formAuthentication" class="mb-3" action="{{ url('/forgot_password') }}" method="POST">
              <input type="hidden" name="_token" value="{{ csrf_token() }}" />
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                  type="text"
                  class="form-control"
                  id="email"
                  name="email"
                  placeholder="Enter your email"
                  autofocus />
              </div>
              <button class="btn btn-primary d-grid w-100">Send Reset Link</button>
            </form>
            <div class="text-center">
              <a href="{{ url('/login') }}" class="d-flex align-items-center justify-content-center">
                <i class="ti ti-chevron-left scaleX-n1-rtl"></i>
                Back to login
              </a>
            </div>
          </div>
        </div>
        <!-- /Forgot Password -->
      </div>
    </div>
  </div>

@endsection