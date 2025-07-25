@extends('admin.layouts.app')

@section('main')


<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Jobs List</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
               @include('admin.sidebar')
            </div>
            <div class="col-lg-9">
                @include('front.message')
                <div class="card border-0 shadow mb-4">

                <div class="card-body card-form">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="fs-4 mb-1">Job Applications List</h3>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table ">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Job Title</th>
                                        <th scope="col">Applicant Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Applied Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border-0">

                                    @if ($applications->isNotEmpty())
                                        @foreach ($applications as $application )

                                            <tr class="active">
                                                <td>{{ $application->id }}</td>
                                                <td>
                                                    <p>{{ $application->job->title }}</p>
                                                    <p>{{ $application->job->location }}</p>

                                                </td>
                                                <td> {{ $application->user->name }}</td>
                                                <td> {{ $application->user->email }}</td>
                                                <td>{{ \Carbon\Carbon::parse($application->created_at)->format('d M, Y') }}</td>
                                                <td>
                                                    <div class="action-dots">
                                                        <button href="#" class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item" href="{{ route('admin.job.applications.detail', $application->id) }}""><i class="fa fa-trash" aria-hidden="true"></i> Detail </a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="deleteJobApplication({{ $application->id }})"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>

                                        @endforeach

                                    @endif



                                </tbody>

                            </table>
                        </div>
                    </div>

                    <div>
                        {{ $applications->links() }}
                    </div>


                </div>
            </div>
        </div>
    </div>
</section>


@endsection

@section('customJs')

<script>

function deleteJobApplication(id){

    if(confirm("Are you sure you want to delete?")){

        $.ajax({
            url:'{{ route("admin.job.application.destroy") }}',
            type: 'delete',
            data: {id:id},
            dataType: 'json',
            success: function(response){
                window.location.href = '{{ route("admin.job.applications.list") }}';
            }
        });
    }
}
</script>

@endsection
