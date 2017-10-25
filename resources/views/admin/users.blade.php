@extends('admin.master')

@section('content')

	<div class="row">
	    <div class="col-lg-12">
	        <div class="panel panel-default">
	            <div class="panel-heading">
	                User Info - napraviti <a href="https://datatables.net">Datatable</a>
	            </div>
	            <!-- /.panel-heading -->
	            <div class="panel-body">
	                <div class="dataTable_wrapper">
	                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
	                        <thead>
	                            <tr>
	                                <th>Name</th>
	                                <th>Points</th>
	                                <th>Registered</th>
	                                <th>Last active</th>
	                                <th>Action</th>
	                            </tr>
	                        </thead>
	                        <tbody>

	                        @foreach($users as $user)

	                            <tr>
	                                <td @if($user->banned_until)
	                                		class="text-danger"
	                                	@endif
	                                >
	                                	{{ $user->name }}
	                                </td>
	                                <td>{{ $user->points }}</td>
	                                <td>
	                                	{{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}
	                                </td>
	                                <td>
	                                	<a href="https://laracasts.com/discuss/channels/general-discussion/middleware-to-log-users-last-activity">
	                                		Coming Soon
	                                	</a>
	                                </td>
	                                <td>
	                                	<a class="text-danger" title="Ban user {{ $user->name }}"
	                                		href="users/ban/{{ $user->id }}">
	                                		<i class="fa fa-ban" aria-hidden="true"></i>
	                                	</a>&nbsp;
	                                	<a class="text-success" title="Unban user {{ $user->name }}"
	                                		href="users/unban/{{ $user->id }}">
	                                		<i class="fa fa-check" aria-hidden="true"></i>
	                                	</a>
	                                </td>
	                            </tr>

							@endforeach

	                        </tbody>
	                    </table>
	                </div>
	                <!-- /.table-responsive -->
	                <div class="well">
	                    <h4>DataTables Usage Information</h4>
	                    <p>DataTables is a very flexible, advanced tables plugin for jQuery. In SB Admin, we are using a specialized version of DataTables built for Bootstrap 3. We have also customized the table headings to use Font Awesome icons in place of images. For complete documentation on DataTables, visit their website at <a target="_blank" href="https://datatables.net/">https://datatables.net/</a>.</p>
	                    <a class="btn btn-default btn-lg btn-block" target="_blank" href="https://datatables.net/">View DataTables Documentation</a>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

@endsection