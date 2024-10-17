<!DOCTYPE html>
<html class="no-js" lang="en_AU" />
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>InspierLink | Find Best Jobs</title>
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1, user-scalable=no" />
	<meta name="HandheldFriendly" content="True" />
	<meta name="pinterest" content="nopin" />
	<meta name="csrf-token"content="{{csrf_token()}}"/>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/trumbowyg.min.css" integrity="sha512-Fm8kRNVGCBZn0sPmwJbVXlqfJmPC13zRsMElZenX6v721g/H7OukJd8XzDEBRQ2FSATK8xNF9UYvzsCtUpfeJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}" />
	<!-- Fav Icon -->
	<link rel="shortcut icon" type="image/x-icon" href="{{route('account.profile')}}" />
</head>
<body data-instant-intensity="mousedown">
@include('front.layouts.header')



@yield('main')
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title pb-0" id="exampleModalLabel">Change Profile Picture</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="profilePicForm"name="profilePicForm" action="{{ route('account.updateProfilePic') }}" method="post" enctype="multipart/form-data">
			@csrf
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Profile Image</label>
                <input type="file" class="form-control" id="image"  name="image">
				<p class="text-danger" id="image-error"></p>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary mx-3">Update</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            
        </form>
      </div>
    </div>
  </div>
</div>

@include('front.layouts.footer')
@include('front.layouts.scripts')
<script>
	$('.textarea').trumbowyg();
	$.ajaxSetup({
	    headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    }
	});
	$("#profilePicForm").submit(function(e){
	e.preventDefault(); 

	var formData = new FormData(this);

	$.ajax({
		url: '{{ route("account.updateProfilePic") }}',
		type: 'post', 
		data: formData, 
		dataType: 'json',
		contentType: false, // لمنع jQuery من تعديل نوع البيانات
		processData: false, // منع jQuery من تحويل البيانات إلى نص
		success: function(response) {
			if(response.status == false) {
				var errors = response.errors;
				if(errors.image) {
					$("#image-error").html(errors.image); // عرض الأخطاء
				}
			} else {
				window.location.href = '{{ url()->current() }}'; // إعادة تحميل الصفحة إذا تم بنجاح
			}
		}
	});
});


	
</script>
@yield('customJs')
</body>
</html>