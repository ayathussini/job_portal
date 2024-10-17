@extends('front.layouts.master')
@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('user.home')}}">Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
            @include('admin.sidebar')
            </div>
            <div class="col-lg-9">
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
                <div class="card border-0 shadow mb-4">
                    <div class="card-body dashboard text-center">
                        <p class="h2 dashboard">Welcome Administrator</p>
                    </div>
                   
                </div>              
            </div>
        </div>
    </div>
</section>
@endsection


@section('customJs')

@endsection





