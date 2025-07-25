@extends('front.layouts.app')
@section('main')

<section class="section-5">
    <div class="container my-5">
        <div class="py-lg-2">&nbsp;</div>
        <div class="row d-flex justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0 p-5">
                    <h1 class="h3">Register</h1>
                    <form name="registrationForm" id="registrationForm">
                        <div class="mb-3">
                            <label for="" class="mb-2">Name*</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name">
                            <p></p>
                        </div>
                        <div class="mb-3">
                            <label for="" class="mb-2">Email*</label>
                            <input type="text" name="email" id="email" class="form-control" placeholder="Enter Email">
                            <p></p>
                        </div>
                        <div class="mb-3">
                            <label for="" class="mb-2">Password*</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password">
                            <p></p>
                        </div>
                        <div class="mb-3">
                            <label for="" class="mb-2">Confirm Password*</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Please Confirm Password">
                            <p></p>
                        </div>
                        <div class="mb-3">
                            <label for="" class="mb-2">Captcha Validation*</label>
                            {!! NoCaptcha::display(['id' => 'g-recaptcha']) !!}
                            <div id="g-recaptcha-error" class="invalid-feedback d-block" style="display: none;"></div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Register</button>
                    </form>
                </div>
                <div class="mt-4 text-center">
                    <p>Have an account? <a  href="{{ route('account.login') }}">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('customJs')

{!! NoCaptcha::renderJs() !!}

<script>
$("#registrationForm").submit(function(e){
    e.preventDefault();

    //Registration button
    let $submitBtn = $("#registrationForm button[type=submit]");
    $submitBtn.prop("disabled", true).html("Processing Registration...");

    $.ajax({
        url:'{{ route("account.process.registration") }}',
        type: 'post',
        data: $("#registrationForm").serializeArray().concat({
            name: "g-recaptcha-response",
            value: grecaptcha.getResponse()
        }),
        dataType: 'json',
        success: function(response) {
            if(response.status == false) {
                var errors = response.errors;
                // show all field errors
                if(errors.name){
                    $("#name").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors.name)
                }else{
                    $("#name").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html('')
                }

                if(errors.email){
                    $("#email").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors.email)
                }else{
                    $("#email").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html('')
                }

                if(errors.password){
                    $("#password").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors.password)
                }else{
                    $("#password").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html('')
                }

                if(errors.confirm_password){
                    $("#confirm_password").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors.confirm_password)
                }else{
                    $("#confirm_password").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html('')
                }

                if (errors["g-recaptcha-response"]) {
                    $("#g-recaptcha-error")
                        .html(errors["g-recaptcha-response"][0])
                        .show();
                } else {
                    $("#g-recaptcha-error")
                        .html('')
                        .hide();
                }

                grecaptcha.reset();

            } else {

                // clear all errors
                $("#name").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html('');

                $("#email").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html('');

                $("#password").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html('');

                $("#confirm_password").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback')
                    .html('');

                $("#g-recaptcha-error")
                    .html('')
                    .hide();

                alert('Registration successful! Redirecting...');
                window.location.href='{{ route("account.login") }}'
            }

        },
        complete: function() {
            grecaptcha.reset(); // Reset CAPTCHA
            $submitBtn.prop("disabled", false).html("Register");
        }

    });
});
</script>
@endsection
