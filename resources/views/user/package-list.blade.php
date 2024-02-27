<table class="table table-hover table-bordered">
<thead class="thead-dark">
    <tr>
    <th>SI.NO</th>
    <th>Binary Plan</th>
    <th>Amount</th>
    <th>Action</th>
    </tr>
</thead>
<tbody>
@foreach ($packages as $index=>$package)
    <tr>
    <td>{{++$index}}</td>
    <td>{{$package->name}}</td>
    <td>{{$package->amount}}</td>
    <td>
        <a href="" class="btn btn-info">Login to user dashboard</a>
    </td>
    </tr>
@endforeach
</tbody>
</table>