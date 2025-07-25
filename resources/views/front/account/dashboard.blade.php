
@extends('front.layouts.app')

@section('main')


<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Account Settings</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
               @include('front.account.sidebar')
            </div>
            <div class="col-lg-9">
                @include('front.message')
                <div class="card border-0 shadow mb-4">

                    <div class="card-body">

                        <h5 class="card-title mb-4">Dashboard Overview</h5>
                        <div class="row g-4">

                            <!-- Applications Card -->
                            <div class="col-md-6">
                                <div class="card text-white bg-danger h-100">
                                    <div class="card-body d-flex flex-column justify-content-center text-center">
                                        <h2 class="card-title text-white">{{ $applicationCount ?? 0 }}</h2>
                                        <p class="card-text">Job Applications</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Users Card -->
                            <div class="col-md-6">
                                <div class="card text-white bg-success h-100">
                                    <div class="card-body d-flex flex-column justify-content-center text-center">
                                        <h2 class="card-title text-white">{{ $jobSavedCount ?? 0 }}</h2>
                                        <p class="card-text"> Saved Job / Wishlist</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                </div>

            </div>
        </div>
    </div>
</section>


@endsection

@section('customJs')

@endsection
