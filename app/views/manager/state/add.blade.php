@extends('manager.layouts.master')
@section('content')
<div class="row">
  <div class="col-md-6"><strong>Add New State</strong></div>
  <div class="col-md-6">
  <a class="btn btn-primary" href="{{ URL::to('manager/state') }}">State List</a>
  </div>
</div>
<hr class="divider">
{{ Form::open(array('url' => 'manager/state/add', 'class' => 'form-horizontal')) }}
    <div class="form-group">
     <div class="col-xs-offset-1 col-xs-10">
        @if(count($errors) >= 1)
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('name') }}</div>
        @endif
     </div>
    </div>
    <div class="form-group">
        {{ Form::label('country', 'Country', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::select('country', $country_list, array_values($country_list), array('class' => 'form-control selectpicker')) }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('name', 'Name', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::text('name', '', array('class' => 'form-control')) }}
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-offset-2 col-xs-10">
           <button type="submit" class="btn btn-primary">Add</button>
        </div>
    </div>
{{ Form::close() }}
@stop