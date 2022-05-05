@extends('phobrv::adminlte3.layout')

@section('header')
<ul>
	<li>

	</li>
	<li class="text-center">
		{{ Form::open(array('route'=>'drugstore.updateUserSelectRegion','method'=>'post')) }}
		<table class="form" width="100%" border="0" cellspacing="1" cellpadding="1">
			<tbody>
				<tr>
					<td style="text-align:center; padding-right: 10px;">
						<div class="form-group">
							{{ Form::select('select',$data['arrayRegion'],(isset($data['select']) ? $data['select'] : '0'),array('id'=>'choose','class'=>'form-control')) }}
						</div>
					</td>
					<td>
						<div class="form-group">
							<button id="btnSubmitFilter" class="btn btn-primary ">@lang('Filter')</button>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		{{Form::close()}}
	</li>
</ul>
@endsection

@section('content')
<div class="row">

	<div class="col-md-5">
		<div class="card">
			<div class="card-header font16">
				List Drugstore
			</div>
			<div class="card-body">

				<table id="table-no-order" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>{{__('Area')}}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@if($data['drugstores'])
						@foreach($data['drugstores'] as $r)
						<tr>
							<td>{{$r->title}}</td>
							<td align="center">
								<a href="{{route('drugstore.edit',array('drugstore'=>$r->id))}}"><i class="far fa-edit" title="Sửa"></i></a>
								@if(!isset($r->childs) || count($r->childs) == 0)
								&nbsp;&nbsp;&nbsp;
								<a style="color: red" href="#" onclick="destroy('destroy{{$r->id}}')"><i class="fa fa-times" title="Sửa"></i></a>
								<form id="destroy{{$r->id}}" action="{{ route('drugstore.destroy',array('drugstore'=>$r->id)) }}" method="post" style="display: none;">
									@method('delete')
									@csrf
								</form>
								@endif
							</td>
						</tr>
						@if($r->childs)
						@foreach($r->childs as $c)
						<tr>
							<td style="padding-left: 30px;">{{$c->title}}</td>
							<td align="center">
								<a href="{{route('drugstore.edit',array('drugstore'=>$c->id))}}"><i class="far fa-edit" title="Sửa"></i></a>

								&nbsp;&nbsp;&nbsp;
								<a style="color: red" href="#" onclick="destroy('destroy{{$c->id}}')"><i class="fa fa-times" title="Sửa"></i></a>
								<form id="destroy{{$c->id}}" action="{{ route('drugstore.destroy',array('drugstore'=>$c->id)) }}" method="post" style="display: none;">
									@method('delete')
									@csrf
								</form>
							</td>
						</tr>
						@endforeach
						@endif

						@endforeach
						@endif
					</tbody>

				</table>
			</div>
		</div>
	</div>
	<div class="col-md-7">
		<div class="card">
			<div class="card-header font16">
				Create/Edit Drugstore
				<a href="#" onclick="save()"  class="pull-right btn btn-primary mr-auto">
					<i class="far fa-edit"></i> Save
				</a>
			</div>
			<div class="card-body">
				<form method="post" action="{{isset($data['post']) ? route('drugstore.update',array('drugstore'=>$data['post']->id)) : route('drugstore.store')}}"  enctype="multipart/form-data" id="formSubmit"  class="form-horizontal" >
					@csrf
					@isset($data['post']) @method('put') @endisset
					<input type="hidden" id="typeSubmit" name="typeSubmit" value="">
					<input type="hidden" name="type" value="drugstore">
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-2 control-label">Region</label>
						<div class="col-sm-10">
							{{Form::select('region_id', $data['arrayRegion'] ,(isset($data['select']) ? $data['select'] : '0'),array('class'=>'form-control'))}}
						</div>
					</div>
					@if(!isset($data['childs']) || count($data['childs']) == 0)
					@include('phobrv::input.inputSelect',['label'=>'Parent','key'=>'parent','array'=>$data['arrayRegionParent']])
					@endif
					@include('phobrv::input.inputText',['label'=>'Name','key'=>'title','required'=>true])
					@include('phobrv::input.inputTextarea',['label'=>'Content','key'=>'content','style'=>'short'])
					<label class="font16" style="margin-top: 10px;">{{__('Seo Meta')}}</label>
					@include('phobrv::input.inputText',['label'=>'Meta Title','key'=>'meta_title','type'=>'meta'])
					@include('phobrv::input.inputText',['label'=>'Meta Description','key'=>'meta_description','type'=>'meta'])
					@include('phobrv::input.inputText',['label'=>'Meta Keywords','key'=>'meta_keywords','type'=>'meta'])
					<button id="btnSubmit" style="display: none" type="submit" >Submit</button>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@section('styles')

@endsection

@section('scripts')
<script type="text/javascript">
	window.onload = function() {
		CKEDITOR.replace('content', options);
	};
</script>
@endsection