<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Job Notification Email</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5;">

    <h1 style="color: #333;">Hello, {{ $mailData['employer']->name }}</h1>

    <p><strong>Job Title:</strong> {{ $mailData['job']->title }}</p>

    <h2>Applicant Details</h2>
    <p><strong>Name:</strong> {{ $mailData['user']->name }}</p>
    <p><strong>Email:</strong> {{ $mailData['user']->email }}</p>
    <p><strong>Mobile No:</strong> {{ $mailData['user']->mobile }}</p>

    @if (!empty($mailData['user']->cover_letter))
        <p><strong>Cover Letter:</strong> {!! $mailData['user']->cover_letter !!}</p>
    @endif

    <p style="margin-top: 20px;">Please see the attached CV for more details.</p>

    <hr>
    <p style="font-size: 12px; color: #777;">
        This email was generated automatically by the AIMS Education Job Portal.
    </p>

</body>
</html>
