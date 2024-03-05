@if ($child_user)
    <?php
    $child_left = App\Models\User::where('placement_id', $child_user->member_id)
        ->where('side', 'left')
        ->first();
    $child_right = App\Models\User::where('placement_id', $child_user->member_id)
        ->where('side', 'right')
        ->first();
    ?>
@endif
@if ($child_user)
    <div class="row td_style">
        <div class="col-md-6">
            <img class="profile-user-img img-fluid img-circle" src="{{ asset('backend/assets/img/avatar5.png') }}">
            @if ($child_left)
                @php
                    $child_child_user = $child_left;
                @endphp
                <a href="{{ url('user/genealogy') }}?member_id={{ $child_child_user->member_id }}">
                    <p>{{ ucfirst($child_child_user->name) }}<br>
                        {{ $child_child_user->member_id }}
                    </p>
                </a>
            @else
                <?php $child_child_user = null; ?>
                <a target="_blank" href="{{ url('register') }}">
                    <p><strong>Vacant Place</strong></p>
                </a>
            @endif
            <div class="td_top_left_style"></div>
            <div class="td_bottom_style"></div>

            @include('user.Genealogy.child-genealogy-last')

        </div>

        <div class="col-md-6">
            <img class="profile-user-img img-fluid img-circle" src="{{ asset('backend/assets/img/avatar5.png') }}">
            @if ($child_right)
                @php $child_child_user = $child_right;   @endphp
                <a href="{{ url('user/genealogy') }}?member_id={{ $child_child_user->member_id }}">
                    <p>{{ ucfirst($child_child_user->name) }}<br>
                        {{ $child_child_user->member_id }}
                    </p>
                </a>
            @else
                <?php $child_child_user = null; ?>
                <a target="_blank" href="{{ url('register') }}">
                    <p><strong>Vacant Place</strong></p>
                </a>
            @endif
            <div class="td_top_right_style"></div>
            <div class="td_bottom_style"></div>

            @include('user.Genealogy.child-genealogy-last')


        </div>
    </div>
@else
    <div class="row td_style">

        <div class="col-md-6">
            <img class="profile-user-img img-fluid img-circle" src="{{ asset('backend/assets/img/avatar5.png') }}">
            <p><strong>Vacant Place</strong></p>
            <div class="td_top_left_style"></div>
            <div class="td_bottom_style"></div>

            @include('user.Genealogy.child-genealogy-last')

        </div>

        <div class="col-md-6">
            <img class="profile-user-img img-fluid img-circle" src="{{ asset('backend/assets/img/avatar5.png') }}">
            <p><strong>Vacant Place</strong></p>
            <div class="td_top_right_style"></div>
            <div class="td_bottom_style"></div>

            @include('user.Genealogy.child-genealogy-last')
        </div>
    </div>

@endif
