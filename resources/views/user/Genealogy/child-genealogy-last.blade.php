@if($user)
    @php $trees = \App\Models\Tree::where('user_id',$user->id)->get();   @endphp
@else
    @php $trees = \App\Models\Tree::where('user_id',0)->get();   @endphp
@endif
@if($trees->count()>0)
@foreach($trees as $tree)
<div class="row td_style">

    @php $user = $tree->left_user;   @endphp
    <div class="col-md-6">
        <img class="profile-user-img img-fluid img-circle" src="{{ asset('backend/assets/img/avatar5.png') }}">
        @if($user)
        <a href="{{url('user/genealogy')}}?member_id={{$user->member_id}}">
        <p>{{ ucfirst($user->name) }}<br>
            {{ $user->member_id }}
        </p>
        </a>
        @else
        <a target="_blank" href="{{url('register')}}"><p><strong>Vacant Place</strong></p></a>
        @endif
        <div class="td_top_left_style"></div>
        <div class="td_bottom_style"></div>

    </div>

    @php $user = $tree->right_user;   @endphp
    <div class="col-md-6">
        <img class="profile-user-img img-fluid img-circle" src="{{ asset('backend/assets/img/avatar5.png') }}">
        @if($user)
        <a href="{{url('user/genealogy')}}?member_id={{$user->member_id}}">
        <p>{{ ucfirst($user->name) }}<br>
            {{ $user->member_id }}
        </p>
        </a>
        @else
        <a target="_blank" href="{{url('register')}}"><p><strong>Vacant Place</strong></p></a>
        @endif
        <div class="td_top_right_style"></div>
        <div class="td_bottom_style"></div>


    </div>
</div>
@endforeach
@else
<div class="row td_style">

    <div class="col-md-6">
        <img class="profile-user-img img-fluid img-circle" src="{{ asset('backend/assets/img/avatar5.png') }}">
        <p><strong>Vacant Place</strong></p>
        <div class="td_top_left_style"></div>
        <div class="td_bottom_style"></div>

    </div>

    <div class="col-md-6">
        <img class="profile-user-img img-fluid img-circle" src="{{ asset('backend/assets/img/avatar5.png') }}">
        <p><strong>Vacant Place</strong></p>
        <div class="td_top_right_style"></div>
        <div class="td_bottom_style"></div>

    </div>
</div>

@endif