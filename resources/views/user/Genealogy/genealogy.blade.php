@extends('user.layout.app')
@section('content')
    <style>
        .td_style {
            text-align: center;
            position: relative;
            /* padding: 5px; */
        }

        .td_style>div {
            padding-top: 15px;
        }

        .td_style .td_top_left_style {
            width: 50%;
            height: 20px;
            background: transparent;
            margin: auto;
            position: absolute;
            right: -1px;
            top: 0px;
            border-left: 3px solid #000;
            border-top: 3px solid #000;
        }

        .td_style .td_top_right_style {
            width: 50%;
            height: 20px;
            background: transparent;
            margin: auto;
            position: absolute;
            left: -1px;
            top: 0px;
            border-right: 3px solid #000;
            border-top: 3px solid #000;
        }

        /* .td_style .td_bottom_style{
        width: 3px;
        height: 20px;
        background: #000;
        margin: auto;
        position: absolute;
        left: 50%;
        bottom: -3px;
    } */
    </style>
    <!--app-content open-->
    <div class="app-content">
        <section class="section">

            <!--page-header open-->
            <div class="page-header">

                <ol class="breadcrumb">
                    <!-- breadcrumb -->
                    <li class="breadcrumb-item"><a href="#"><i class="fe fe-home mr-2"></i> Home </a></li>
                    <li class="breadcrumb-item active" aria-current="page"> Genealogy Tree </li>
                </ol><!-- End breadcrumb -->


            </div>
            <!--page-header closed-->



            <!--row open-->
            <div class="row">
                <div class="col-md-12">
                    <div class="card export-database">
                        @if (\Session::has('error'))
                            <div class="alert alert-danger">
                                <p>{{ \Session::get('error') }}</p>
                            </div>
                        @endif
                        <div class="card-header">
                            <h4> Genealogy Tree </h4>
                        </div>
                        <form method="get" action="{{ url('user/genealogy') }}">
                            <div class="row card-body">
                                <div class="col-md-3">
                                    <label>Member ID Search:<input type="search" class="form-control form-control-sm"
                                            value="{{ Request()->member_id }}" name="member_id" id="member-id"
                                            placeholder="Enter Member ID" aria-controls="example"></label>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-secondary" value="Search">Search</button>

                                </div>
                            </div>
                        </form>


                        <!--row open-->
                        <div class="row">
                            <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                <div class="card">
                                    <div class="card-body table-responsive">


                                        <div class="row td_style">
                                            <div class="col-md-12">
                                                <img class="profile-user-img img-fluid img-circle" style=""
                                                    src="{{ asset('backend/assets/img/avatar5.png') }}"
                                                    alt="User profile picture">
                                                <p>{{ ucfirst($user->name) }}<br>
                                                    {{ $user->member_id }}
                                                </p>
                                                <div class="td_bottom_style"></div>
                                            </div>
                                        </div>
                                        <div class="row td_style">

                                            <div class="col-md-6">
                                                <img class="profile-user-img img-fluid img-circle"
                                                    src="{{ asset('backend/assets/img/avatar5.png') }}">
                                                    
                                                @if ($left)
                                                    <?php $child_user=$left; ?>
                                                    <a href="{{ url('user/genealogy') }}?member_id={{ $child_user->member_id }}">
                                                        <p>{{ ucfirst($child_user->name) }}<br>
                                                            {{ $child_user->member_id }}
                                                        </p>
                                                    </a>
                                                @else
                                                    <?php
                                                        $child_user=null;
                                                    ?>
                                                    <a target="_blank" href="{{ url('register') }}">
                                                        <p><strong>Vacant Place</strong></p>
                                                    </a>
                                                @endif
                                                <div class="td_top_left_style"></div>
                                                <div class="td_bottom_style"></div>

                                                @include('user.Genealogy.child-genealogy')

                                            </div>

                                            <div class="col-md-6">
                                                <img class="profile-user-img img-fluid img-circle"
                                                    src="{{ asset('backend/assets/img/avatar5.png') }}">
                                                    
                                                @if ($right)
                                                    <?php
                                                        $child_user=$right;
                                                    ?>
                                                    <a
                                                        href="{{ url('user/genealogy') }}?member_id={{ $child_user->member_id }}">
                                                        <p>{{ ucfirst($child_user->name) }}<br>
                                                            {{ $child_user->member_id }}
                                                        </p>
                                                    </a>
                                                @else
                                                    <?php $child_user=null; ?>
                                                    <a target="_blank" href="{{ url('register') }}">
                                                        <p><strong>Vacant Place</strong></p>
                                                    </a>
                                                @endif
                                                <div class="td_top_right_style"></div>
                                                <div class="td_bottom_style"></div>
                                                @include('user.Genealogy.child-genealogy')
                                            </div>
                                        </div>
                                        {{-- @endforeach
                                        @else

                                        <div class="row td_style">

                                            <div class="col-md-6">
                                                <img class="profile-user-img img-fluid img-circle" src="{{ asset('backend/assets/img/avatar5.png') }}">
                                                <a target="_blank" href="{{url('register')}}"><p><strong>Vacant Place</strong></p></a>
                                                <div class="td_top_left_style"></div>
                                                <div class="td_bottom_style"></div>

                                                @include('user.Genealogy.child-genealogy')

                                            </div>

                                            <div class="col-md-6">
                                                <img class="profile-user-img img-fluid img-circle" src="{{ asset('backend/assets/img/avatar5.png') }}">
                                                <a target="_blank" href="{{url('register')}}"><p><strong>Vacant Place</strong></p></a>
                                                <div class="td_top_right_style"></div>
                                                <div class="td_bottom_style"></div>

                                                    @include('user.Genealogy.child-genealogy')
                                            </div>
                                        </div>

                                        @endif --}}
                                    </div>

                                </div>
                            </div>

                        </div>
                        <!--row closed-->




                    </div>
                </div>
            </div>
            <!--row closed-->

        </section>
    </div>
    <!--app-content closed-->
@endsection
