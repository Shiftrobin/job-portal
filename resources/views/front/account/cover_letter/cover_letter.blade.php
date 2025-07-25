@extends('front.layouts.app')

@section('main')


<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Cover Letter</li>
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
                <form method="post" id="CoverLetterForm" name="CoverLetterForm">
                    <div class="card border-0 shadow mb-4 ">
                        <div class="card-body card-form p-4">
                            <h3 class="fs-4 mb-1">Cover Letter</h3>

                            <div class="mb-4">
                                <label for="" class="mb-2">Update Your Cover Letter</label>
                                <textarea class="textarea" name="cover_letter" id="cover_letter" cols="5" rows="5" placeholder="Cover Letter">{{ $cover_letter }}</textarea>
                                <p></p>
                            </div>

                        </div>
                        <div class="card-footer  p-4">
                            <button type="submit" class="btn btn-primary">Update Cover Letter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>


@endsection

@section('customJs')

<script type="text/javascript">
$("#CoverLetterForm").submit(function(e){
    e.preventDefault();

    $("button[type='submit']").prop('disabled',true);

    // console.log($("#CoverLetterForm").serializeArray());
    // return false;

    $.ajax({
        url: '{{ route("account.cover.letter.update") }}',
        type: 'POST',
        dataType: 'json',
        data: $("#CoverLetterForm").serializeArray(),
        success: function(response){

            $("button[type='submit']").prop('disabled',false);

            if(response.status === true){

                $("#cover_letter").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html('');

                 window.location.href="{{ route('account.cover.letter') }}";
            } else{
                var errors = response.errors;

                if(errors.cover_letter){
                    $("#cover_letter").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors.cover_letter)
                }else{
                    $("#cover_letter").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html('')
                }

            }
        }
    });
});
</script>

@endsection
