@extends('front.layouts.master')
@section('main')
<section class="section-5">
    <div class="container my-5">
        <div class="py-lg-2">&nbsp;</div>

        @if(Session::has('success'))
        <div class="alert alert-success">
            <p class="mb-0 pb-0">{{Session::get('success')}}</p>
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="row d-flex justify-content-center">
             <!-- here message code -->
            @if(Session::has('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ Session::get('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ Session::get('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
            <div class="col-md-5">
                <div class="card shadow border-0 p-5">
                    <h1 class="h3">Reset Password</h1>
                    <form action="{{route('account.processResetPassword')}}" method="get">
                        @method('get')
                        @csrf
                        <input type="hidden" name="token" >
                         <div class="mb-3">
                            <label for="email" class="mb-2">Email*</label>
                            <input type="text" name="email" id="email" class="form-control" placeholder="Enter Email">
                            <p class="text-danger"></p>
                        </div> 
                        <div class="mb-3">
                            <label for="new_password" class="mb-2">New Password</label>
                            <input type="password" name="new_password" id="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="New password">
                            @error('new_password')
                            <p class="invalid-feedback">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="mb-2">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control @error('confirm_password') is-invalid @enderror" placeholder="Confirm password">
                            @error('confirm_password')
                            <p class="invalid-feedback">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="justify-content-between d-flex">
                            <button class="btn btn-primary mt-2">Submit</button>
                        </div>
                    </form>
                </div>
                
                <div class="mt-4 text-center">
                    <p>Do not have an account? <a href="{{route('account.login')}}">Back to login</a></p>
                </div>
            </div>
        </div>

        <div class="py-lg-5">&nbsp;</div>
    </div>
</section>
@endsection
