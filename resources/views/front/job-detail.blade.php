@extends('front.layouts.app')

@section('main')

<section class="section-4 bg-2">
    <div class="container pt-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('jobs') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;Back to Jobs</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="container job_details_area">
        <div class="row pb-5">
            <div class="col-md-8">

                @include('front.message')

                <div class="card shadow border-0">
                    <div class="job_details_header">
                        <div class="ps-4 single_jobs white-bg d-flex justify-content-between">
                            <div class="jobs_left d-flex align-items-center">

                                <div class="jobs_conetent">
                                        <h1 class="fs-3">{{ $job->title }}</h1>
                                        
                                    <div class="d-flex flex-wrap gap-2">
                                        <div class="d-flex align-items-center me-3 text-muted">
                                            <i class="fa fa-map-marker me-1"></i>
                                            <small>{{ $job->location }}</small>
                                        </div>
                                        <div class="d-flex align-items-center me-3 text-muted">
                                            <i class="fa fa-clock-o me-1"></i>
                                            <small>{{ $job->type->name }}</small>
                                        </div>
                                        <div class="d-flex align-items-center me-3 text-muted">
                                            <i class="fa fa-money me-1"></i>
                                            <small>{{ $job->salary ?? 'Negotiable' }}</small>
                                        </div>
                                        <div class="d-flex align-items-center me-3 text-muted">
                                            <i class="fa fa-user me-1"></i>
                                            <small>{{ $job->vacancy ?? '1' }}</small>
                                        </div>
                                        <div class="d-flex align-items-center me-3 text-muted">
                                            <i class="fa fa-calendar me-1"></i>
                                            <small>{{ $job->deadline ?? ''}}</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="jobs_right">
                                <div class="apply_now {{ ($count ==1) ? 'saved-job' : '' }}">
                                    <a class="heart_mark" href="javascript:void(0)" onclick="saveJob({{ $job->id }})"> <i class="fa fa-heart-o" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>


                        <div class="ps-4 text-start">
                            @php
                                $cv = Auth::check() ? Auth::user()->cv : null;
                            @endphp

                            @if (Auth::check() && $cv)
                                <a href="#" id="apply-button" onclick="applyJob({{ $job->id }})" class="btn btn-primary">Apply</a>
                            @elseif (Auth::check())
                                <a href="{{ route('account.my.cv') }}" class="btn btn-primary">To Apply Please Upload Your CV First</a>
                            @else
                                <a href="{{ route('account.login') }}" class="btn btn-primary">Login to Apply</a>
                            @endif
                        </div>


                    </div>
                    <div class="descript_wrap white-bg">
                        <div class="single_wrap">
                            <h4>Job description</h4>
                            {!! nl2br($job->description) !!}
                        </div>
                        @if (!empty($job->reponsibility))
                            <div class="single_wrap">
                                <h4>Responsibility</h4>
                                {!! $job->reponsibility !!}
                            </div>
                        @endif
                        @if (!empty($job->qualifications))
                            <div class="single_wrap">
                                <h4>Qualifications</h4>
                                {!! $job->qualifications !!}
                            </div>
                         @endif
                         @if (!empty($job->benefits))
                            <div class="single_wrap">
                                <h4>Benefits</h4>
                                {!! $job->benefits !!}
                            </div>
                         @endif
                        <div class="border-bottom"></div>
                        <div class="pt-3 text-end">

                            @if (Auth::check())
                                <a href="#" onclick="saveJob({{ $job->id }})" class="btn btn-secondary">Save</a>
                            @else
                                <a href="{{ route('account.login') }}" class="btn btn-primary">Login to Save in Wishlist</a>
                            @endif
                          

                        </div>
                    </div>
                </div>

            @if (Auth::user())
                @if (Auth::user()->id == $job->user_id)
                    <div class="card shadow border-0 mt-4">
                        <div class="job_details_header">
                            <div class="single_jobs white-bg d-flex justify-content-between">
                                <div class="jobs_left d-flex align-items-center">
                                    <div class="jobs_conetent">
                                        <h4>Applicants</h4>
                                    </div>
                                </div>
                                <div class="jobs_right"></div>
                            </div>
                        </div>
                        <div class="descript_wrap white-bg">
                            <table class="table table-striped">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Applied Date</th>
                                </tr>
                                @if ($applications->isNotEmpty())
                                    @foreach ( $applications as $application)
                                        <tr>
                                            <td>{{ $application->user->name }}</td>
                                            <td>{{ $application->user->email }}</td>
                                            <td>{{ $application->user->mobile }}</td>
                                            <td>{{ \Carbon\Carbon::parse($application->applied_date )->format('d M, Y') }}</td>
                                        </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="4">Applicants not found</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                @endif
            @endif

            </div>
            <div class="col-md-4">
                <div class="card shadow border-0">
                    <div class="job_sumary">
                        <div class="summery_header pb-1 pt-4">
                            <h3>Job Summery</h3>
                        </div>
                        <div class="job_content pt-3">
                            <ul>
                                <li>Published on: <span> {{ \Carbon\Carbon::parse($job->created_at)->format('d M,  Y') }} </span></li>
                                 @if (!empty($job->vacancy))
                                    <li>Vacancy: <span> {{ $job->vacancy }} </span></li>
                                 @endif
                                 @if (!empty($job->salary))
                                    <li>Salary: <span> {{ $job->salary }} </span></li>
                                 @endif
                                 @if (!empty($job->location))
                                    <li>Location: <span> {{ $job->location }} </span></li>
                                 @endif
                                <li>Job Nature: <span> {{ $job->type->name }} </span></li>
                                 @if(!empty($job->deadline))
                                    <li>Deadline: <span> {{ $job->deadline ?? '' }} </span></li>
                                 @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card shadow border-0 my-4">
                    <div class="job_sumary">
                        <div class="summery_header pb-1 pt-4">
                            <h3>Company Details</h3>
                        </div>
                        <div class="job_content pt-3">
                            <ul>
                                <li>Name: <span> {{ $job->company_name }} </span></li>
                                <li>Locaion: <span> {{ $job->company_location }} </span></li>
                                <li>Webite: <span> <a href="{{ $job->company_website }}">{{ $job->company_website }}</a>  </span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection

@section('customJs')
<script type="text/javascript">

//apply job
function applyJob(id){
   // alert(id);
   if (confirm("Are you sure you want to apply on this job?")) {

        // Disable button and change text
        $('#apply-button')
            .prop('disabled', true)
            .text('Sending please wait...');

        //ajax call
        $.ajax({
            url: '{{ route("apply.job") }}',
            type: 'post',
            data: {id:id},
            dataType: 'json',
            success: function(response) {
               // window.location.reload();

               if(response.status) {
                   window.location.href = "{{ url()->current() }}";

                   // Change button text back
                   $('#apply-button')
                        .prop('disabled', false)
                        .text('Apply Now');
               } else {

                  // Change button text back
                  $('#apply-button')
                        .prop('disabled', false)
                        .text('Apply Now');

                  alert('An unexpected error occurred. Please try again.');

               }


            }
        })
   }

}


//save job
function saveJob(id){
   // alert(id);

    $.ajax({
        url: '{{ route("save.job") }}',
        type: 'post',
        data: {id:id},
        dataType: 'json',
        success: function(response) {
            // window.location.reload();
            window.location.href = "{{ url()->current() }}";
        }
    })


}

</script>


@endsection
