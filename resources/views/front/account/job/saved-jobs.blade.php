@extends('front.layouts.master')
@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('user.home') }}">Home</a></li>
                        <li class="breadcrumb-item "><a href="{{route('account.profile')}}">Account Settings</a></li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('front.account.sidebar')
            </div>
            <div class="col-lg-9">
                @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if(Session::has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ Session::get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="card border-0 shadow mb-4 p-3">
                    <div class="card-body card-form">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="fs-4 mb-1">Saved Jobs</h3>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">Title</th>
                                        <th scope="col">Applicants</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border-0">
                                    @if($savedJobs->isNotEmpty())
                                    @foreach($savedJobs as $savedJob)
                                    <tr class="active">
                                        <td>
                                            <div class="job-name fw-500">{{ $savedJob->job->title }}</div>
                                            <div class="info1">{{ $savedJob->job->jobType->name }} . {{ $savedJob->job->location }}</div>
                                        </td>
                                        <td>{{$savedJob->job->applications->count()}} Applications</td>
                                        <td>
                                            <div class="job-status text-capitalize">{{ $savedJob->job->status == 1 ? 'Active' : 'Blocked' }}</div>
                                        </td>
                                        <td>
                                            <div class="action-dots float-end">
                                                <button class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item "  href="{{ route('jobDetail', $savedJob->job->id) }}"><i class="fa fa-eye" aria-hidden="true"></i> View</a></li>
                                                    <li>
                                                        <a class="dropdown-item " style="background-color: red; color:aliceblue" href="javascript:void(0)" onclick="removeJob({{ $savedJob->id }})">
                                                            <i class="fa fa-trash" aria-hidden="true"></i> Remove
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="5">Job Applications not found</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{$savedJobs->links()}}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('customJs')
<script type="text/javascript">
function removeJob(id) {
    if (confirm('Are you sure you want to remove?')) {
        $.ajax({
            url: '{{ route("account.removeSavedJobs") }}',
            type: 'POST', 
            data: {
                _token: '{{ csrf_token() }}', 
                id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    window.location.href = '{{ route("account.savedJobs") }}';
                } else {
                    alert('Error: Job not found or already deleted');
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('An error occurred while trying to delete the job');
            }
        });
    }
}
</script>
@endsection
