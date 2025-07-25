<div class="card account-nav border-0 shadow mb-4 mb-lg-0">
    <div class="card-body p-0">
        <ul class="list-group list-group-flush ">
            <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                <a href="{{ route('admin.create.job') }}">Post a Job</a>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                <a href="{{ route('admin.jobs.list') }}">Jobs List</a>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                <a href="{{ route('admin.job.applications.list') }}">Jobs Applications</a>
            </li>
            <li class="list-group-item d-flex justify-content-between p-3">
                <a href="{{ route("admin.users.list") }}">Users List</a>
            </li>
            <li class="list-group-item d-flex justify-content-between p-3">
                <a href="{{ route('admin.profile') }}">Account Settings</a>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                <a href="{{ route('admin.logout') }}">Logout</a>
            </li>
        </ul>
    </div>
</div>
