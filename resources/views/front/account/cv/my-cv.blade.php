@extends('front.layouts.app')

@section('main')


<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">My CV</li>
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
                    <form id="CVForm" name="CVForm" method="post" enctype="multipart/form-data">
                        <div class="card-body  p-4">
                            <h3 class="fs-4 mb-1">My CV</h3>
                            <div class="mb-4">
                                <label for="Upload CV in PDF Format" class="mb-2">Upload or Update Your CV (PDF Format Only and not more than 5MB)</label>
                                <input type="file" class="form-control" id="cv"  name="cv">
                                <p class="text-danger" id="cv-error"></p>
                            </div>

                            @if ($cv > 0)
                                <div class="mb-4">
                                    <label for="" class="mb-2">Uploaded CV</label>
                                    <p> <a href="{{ asset('public/upload/cv/'.$cv) }}" target="_blank">Download</a> </p>
                                </div>
                            @else
                               <span class="text-danger"> {{ 'You have not uploaded your cv yet.' }} </span>
                            @endif

                        </div>
                        <div class="card-footer  p-4">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>


            </div>
        </div>
    </div>
</section>


@endsection

@section('customJs')

<script type="text/javascript">


$("#CVForm").submit(function(e){
    e.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        url: '{{ route("account.cv.store") }}',
        type: 'post',
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function(response){
           if(response.status === false) {
                var errors = response.errors;
                    if(errors.cv) {
                        $("#cv-error").html(errors.cv)
                }
           } else {
                window.location.href = '{{ url()->current() }}'
           }
        }
    })
});


</script>

@endsection
