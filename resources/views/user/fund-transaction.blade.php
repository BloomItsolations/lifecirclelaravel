@extends('user.layout.app')
@section('content')

                <!--app-content open-->
				<div class="app-content">
					<section class="section">

					    <!--page-header open-->
						<div class="page-header">

							<ol class="breadcrumb"><!-- breadcrumb -->
								<li class="breadcrumb-item"><a href="#"><i class="fe fe-home mr-2"></i> Home </a></li>
								<li class="breadcrumb-item active" aria-current="page"> Fund Transaction </li>
							</ol><!-- End breadcrumb -->
						</div>
						<!--page-header closed-->

                  <!--row open-->
						<div class="row">
							<div class="col-md-12">
								<div class="card export-database">

									<div class="card-body">
										<div class="table-responsive">
											<table id="example" class="table table-bordered border-t0 key-buttons text-nowrap w-100" >
												<thead>
													<tr>
														<th> Sl no </th>
														<th> Date  </th>
														<th> Amount </th>
														<th> TDS </th>
														<th> Admin Charge </th>
														<th> Credit </th>
														<th> Type </th>
														<th> Remark  </th>
													</tr>
												</thead>
												<tbody>
                                                    @foreach ($walletRequests as $walletRequest)
                                                    <tr>
														<td> {{$loop->iteration}} </td>
														<td> {{date('d-m-Y',strtotime($walletRequest->created_at))}} </td>
														<td> Rs {{$walletRequest->amount}}  </td>
														<td> Debit </td>
														<td> {{$walletRequest->Active}} </td>
													</tr>
                                                    @endforeach
                                                    @foreach ($rewards as $reward)
                                                    <tr>
														<td> {{$loop->iteration}} </td>
														<td> {{date('d-m-Y',strtotime($reward->created_at))}} </td>
														<td class="text-danger"> Rs {{$reward->amount}}  </td>
														<td> Rs {{$reward->tds}}  </td>
														<td> Rs {{$reward->admin_charges}}  </td>
														<td style="color: green;"><b> Rs {{$reward->credit}}  </b></td>
														<td> {{$reward->description}} </td>
														<td> - </td>
													</tr>
                                                    @endforeach

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--row closed-->

					</section>
				</div>
				<!--app-content closed-->
@endsection
