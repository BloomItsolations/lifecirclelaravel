@extends('user.layout.app')
@section('content')
@if (Session::has('flash_success'))
    <div class="alert alert-success">
    	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {!! Session::get('flash_success') !!}
    </div>
@endif

                <!--app-content open-->
				<div class="app-content">
					<section class="section">

					    <!--page-header open-->
						<div class="page-header">

							<ol class="breadcrumb"><!-- breadcrumb -->
								<li class="breadcrumb-item"><a href="#"><i class="fe fe-home mr-2"></i> Home </a></li>
								<li class="breadcrumb-item active" aria-current="page"> Pin Generation </li>
							</ol><!-- End breadcrumb -->


						</div>
						<!--page-header closed-->

                        <!--row open-->
						<div class="row">
							
							<div class="col-lg-12 col-xl-7 col-md-12 col-sm-12">
							    <div class="card">
									<div class="card-header">
                                        <h4>PIN Generate</h4>
									</div>
									<div class="card-body">
                                    <form action="" method="post">
                                        @csrf
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label style="display: block;width:100%">Select Package</label>
                                                    <select name="package_id" class="form-control" required>
                                                        <option value="">Select</option>
                                                        @foreach($packages as $package)
                                                        <option value="{{$package->id}}">{{$package->amount}}-{{$package->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label style="display: block;width:100%">&nbsp;</label>
                                                    <input type="submit" class="btn btn-info" value="Generate">
                                                </div>
                                            </div>
                                        </form>
									</div>

								</div>
							</div>
						</div>
						<!--row closed-->

                        <div class="row">
                <div class="col-lg-12">
                    <div class="e-panel card">
                        <div class="card-header">
                            <h4> All Pins </h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example"
                                    class="table table-bordered border-t0 key-buttons text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>SN#</th>
                                            <th>User Name</th>
                                            <th>Package Name</th>
                                            <th>Package Amount</th>
                                            <th>Pin Number</th>
                                            <th>Status</th>
                                            <th>Created Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pins as $index=>$pin)
                                        <tr>
                                            <td> {{++$index}} </td>
                                            <td> {{($pin->user)?$pin->user->member_id:''}} </td>
                                            <td> {{$pin->package->name}} Package</td>
                                            <td> {{$pin->package_amount}} </td>
                                            <td> {{$pin->pin_number}} </td>
                                            <td> {{$pin->used_status}} </td>
                                            <td> {{date('d-m-Y',strtotime($pin->created_at))}} </td>
                                            <td>
                                                @php
                                                $url = $pin->pin_number;
                                                @endphp
                                                <button class="btn btn-primary" onclick="navigator.clipboard.writeText('{{$url}}')">Copy the link</button>
                                                <a href="whatsapp://send?text={{$url}}" data-action="share/whatsapp/share">Share via Whatsapp</a>
                                            </td>


                                        </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </section>
    </div>
    <!--app-content closed-->


@endsection
@section('script')
<script>
	$(document).ready(function(){
		$('#state_id').select2();
		$('#pincode').change(function(){
			var pincode=$('#pincode').val();

			$.ajax({
        url : "{{route('pincode')}}",
        type: "POST",
        data : {pincode: pincode, "_token": "{{ csrf_token() }}"},
        success: function(data, textStatus, jqXHR)
            {
				if (data.status == true) {
                    $('#state_id').html(data.state);
                    $('#city_id').html(data.city);
                    $('#district').val(data.district);
                    $('#district').prop('readonly', true);
                    $('#state_id').prop('readonly', true);
                    $('#city_id').prop('readonly', true);
                    $('#invalid_pincode').hide();
                     $('#valid_pincode').html(data.message).show();
                }
                    else{
                        $('#state_id').find('option').remove().end();
                        $('#city_id').find('option').remove().end();
						$('#district').val('');

                        $('#valid_pincode').hide();
                        $('#invalid_pincode').html(data.message).show();
                    }

            },
        error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
		});
	})
</script>



@endsection
