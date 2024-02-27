@extends('admin.layouts.app')
@section('title', ' Pin Generation ')
@section('content')
    <div class="app-content">
        <section class="section">

            <!--page-header open-->
            <div class="page-header">

                <ol class="breadcrumb">
                    <!-- breadcrumb -->
                    <li class="breadcrumb-item"><a href="#"><i class="fe fe-home mr-2"></i> Home </a></li>
                    <li class="breadcrumb-item active" aria-current="page"> Pin List </li>
                </ol><!-- End breadcrumb -->

                
            </div>
            <!--page-header closed-->
            @include('pages.notification')

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
                                            <th>Member</th>
                                            <th>User</th>
                                            <th>Package</th>
                                            <th>Amount</th>
                                            <th>Pin Number</th>
                                            <th>Created Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pins as $index=>$pin)
                                        <tr>
                                            <td> {{++$index}} </td>
                                            <td> {{$pin->member_id}} </td>
                                            <td> {{($pin->user)?$pin->user->member_id:''}} </td>
                                            <td> {{$pin->package->name}} Package</td>
                                            <td> {{$pin->package_amount}} </td>
                                            <td> {{$pin->pin_number}} </td>
                                            <td> {{date('d-m-Y',strtotime($pin->created_at))}} </td>
                                            <td>
                                                @if($pin->used_status=='Pending')
                                                <a class="btn btn-success" href="{{url('admin/pin-approved')}}/{{$pin->pin_number}}" onclick="return confirm('Are you sure?');">Approve</a>
                                                @else
                                                <strong class="text-success">Approved</strong>
                                                @endif
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
@endsection
