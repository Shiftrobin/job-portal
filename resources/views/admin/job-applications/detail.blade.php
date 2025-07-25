@extends('admin.layouts.app')

@section('main')


<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Job Application Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
               @include('admin.sidebar')
            </div>
            <div class="col-lg-9">
                {{-- @include('front.message') --}}
                <div class="card border-0 shadow mb-4">

                    <div class="card-body card-form">

                        <form>
                            <div class="card-body p-4">
                                <h3 class="fs-4 mb-1">Job Application Detail</h3>
                                <div class="mb-4">
                                    <label for="" class="mb-2">Aapplication Date</label>
                                    <input type="text" value="{{ \Carbon\Carbon::parse($application->created_at)->format('d M, Y') }}" class="form-control" disabled>
                                    <p></p>
                                </div>
                                <div class="mb-4">
                                    <label for="" class="mb-2">Applied Job Title</label>
                                    <input type="text" value="{{ $application->job->title }}"class="form-control" disabled>
                                    <p></p>
                                </div>
                                <div class="mb-4">
                                    <label for="" class="mb-2">Applied Job Location</label>
                                    <input type="text" value="{{ $application->job->location }}"class="form-control" disabled>
                                    <p></p>
                                </div>
                                <div class="mb-4">
                                    <label for="" class="mb-2">Applicant Name</label>
                                    <input type="text" value="{{ $application->user->name }}"class="form-control" disabled>
                                    <p></p>
                                </div>
                                <div class="mb-4">
                                    <label for="" class="mb-2">Applicant Email</label>
                                    <input type="text"
                                     value="{{ $application->user->email }}"
                                     class="form-control" disabled>
                                    <p></p>
                                </div>
                                <div class="mb-4">
                                    <label for="" class="mb-2">Applicant Mobile</label>
                                    <input type="text" value="{{ $application->user->mobile }}" class="form-control" disabled>
                                </div>

                                @if (!empty($application->user->designation))
                                    <div class="mb-4">
                                        <label for="" class="mb-2">Applicant Designation</label>
                                        <input type="text" value="{{ $application->user->designation }}" class="form-control" disabled>
                                    </div>
                                @endif

                                @if (!empty($application->user->address))
                                    <div class="mb-4">
                                        <label for="" class="mb-2">Applicant Address</label>
                                        <input type="text" value="{{ $application->user->address }}" class="form-control" disabled>
                                    </div>
                                 @endif

                                 @if (!empty($application->user->address))
                                    <div class="mb-4">
                                        <label for="" class="mb-2">Cover Letter</label>
                                        <textarea class="textarea" cols="5" rows="5" disabled>{{ $application->user->cover_letter }}</textarea>
                                    </div>
                                 @endif

                            </div>
                            <div class="card-footer  p-4">
                                <a class="btn btn-primary" href="{{ asset('public/upload/cv/'.$application->user->cv ) }}"> Download CV</a>
                            </div>
                        </form>

                    </div>


                </div>
            </div>
        </div>
    </div>
</section>


@endsection

@section('customJs')

<script>


</script>

@endsection
