@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">
					@include('common.message')
					@include('common.errors')
					<h3><b>Clientes</b></h3>
				</div>

				<div class="panel-body">
					Listado de todos los clientes que ha tenido la inversión
				</div>

				@if (count($clients) > 0)
					<table class="table table-hover">
						<thead>
							<tr>
								<th data-field="name">Nombre</th>
								<th data-field="id">Cedula</th>
								<th data-field="address">Dirección</th>
								<th data-field="phone">Celular</th>
								<th data-field="operation">Operaciones</th>
							</tr>
						</thead>

						<tbody id="tb-clt">
							@foreach ($clients as $client)

									<tr>
										<td><div>{{ $client->name }}</div></td>
										<td><div>{{ $client->id }}</div></td>
										<td><div>{{ $client->address }}</div></td>
										<td><div>{{ $client->phone }}</div></td>
										<td>
											<div>
												@include('button.delete_obj',['id' => $client->id,'route' => 'delete_client','obj' => 'cliente'])
												@include('button.update_client', ['client' => $client])
											</div>
										</td>
									</tr>

							@endforeach
						</tbody>
					</table>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection