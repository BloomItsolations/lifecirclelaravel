@if(!empty($child_child_user))
    <?php
        $child_child_user_left = App\Models\User::where('placement_id', $child_child_user->member_id)
                ->where('side', 'left')
                ->first();
        $child_child_user_right = App\Models\User::where('placement_id', $child_child_user->member_id)
            ->where('side', 'right')
            ->first();
    ?>
@endif
@if(!empty($child_child_user))
<div class="row td_style">

    <div class="col-md-6">
        <img class="profile-user-img img-fluid img-circle" src="{{ asset('backend/assets/img/avatar5.png') }}">
        @if($child_child_user_left)

        <a href="{{url('user/genealogy')}}?member_id={{$child_child_user_left->member_id}}">
        <p>{{ ucfirst($child_child_user_left->name) }}<br>
            {{ $child_child_user_left->member_id }}
        </p>
        </a>
        @else
        <a target="_blank" href="{{url('register')}}"><p><strong>Vacant Place</strong></p></a>
        @endif
        <div class="td_top_left_style"></div>
        <div class="td_bottom_style"></div>

    </div>

    <div class="col-md-6">
        <img class="profile-user-img img-fluid img-circle" src="{{ asset('backend/assets/img/avatar5.png') }}">
        @if($child_child_user_right)
        <a href="{{url('user/genealogy')}}?member_id={{$child_child_user_right->member_id}}">
        <p>{{ ucfirst($child_child_user_right->name) }}<br>
            {{ $child_child_user_right->member_id }}
        </p>
        </a>
        @else
        <a target="_blank" href="{{url('register')}}"><p><strong>Vacant Place</strong></p></a>
        @endif
        <div class="td_top_right_style"></div>
        <div class="td_bottom_style"></div>


    </div>
</div>
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