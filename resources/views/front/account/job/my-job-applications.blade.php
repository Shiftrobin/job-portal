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

                <div class="card border-0 shadow mb-4 p-3">
                    <div class="card-body card-form">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="fs-4 mb-1">Jobs Applied</h3>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table ">
                                    <thead class="bg-light">
                                        <tr>
                                            <th scope="col">Title</th>
                                            <th scope="col">Job Applied</th>
                                            <th scope="col">Applicants</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-0">

                                        @if ($jobApplications->isNotEmpty())
                                            @foreach ($jobApplications as $jobApplication )

                                                <tr class="active">
                                                    <td>
                                                        <div class="job-name fw-500">{{ $jobApplication->job->title }}</div>
                                                        <div class="info1">{{ $jobApplication->job->type->name }}. {{ $jobApplication->job->location }}</div>
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($jobApplication->applied_date)->format('d M, Y') }}</td>
                                                    <td>{{ $jobApplication->job->applications->count() }} Applications</td>
                                                    <td>
                                                        @if($jobApplication->job->status == 1)
                                                            <div class="job-status text-capitalize">active</div>
                                                        @else
                                                            <div class="job-status text-capitalize">inactive</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="action-dots float-end">
                                                            <button href="#" class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="#" onclick="removeJob({{ $jobApplication->id }})"><i class="fa fa-trash" aria-hidden="true"></i> Remove</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>

                                            @endforeach

                                        @else
                                            <tr>
                                                <td colspan="5">Job applications not found</td>
                                            </tr>

                                        @endif



                                    </tbody>

                                </table>
                            </div>
                        </div>
                        <div>
                            {{ $jobApplications->links() }}
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
    function removeJob(id){
        if(confirm("Are you sure you want to remove?")){

            $.ajax({
                url:'{{ route("account.remove.job.application") }}',
                type: 'post',
                data: {id:id},
                dataType: 'json',
                success: function(response) {
                    window.location.href ="{{ route('account.my.job.applications') }}";
                }
            })
        }
    }
</script>
@endsection
