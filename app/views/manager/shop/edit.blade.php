@extends('manager.layouts.master')
@section('content')
<div class="row">
  <div class="col-md-6"><strong>Add New Town</strong></div>
  <div class="col-md-6">
  <a class="btn btn-primary" href="{{ URL::to('manager/shop') }}">Shop List</a>
  </div>
</div>
<hr class="divider">
{{ Form::open(array('route' => ['manager/shop/edit', $shop->id], 'class' => 'form-horizontal')) }}
    <input type="hidden" name="baseUrl" id="baseUrl" value="{{ url() }}"/>
    <div class="form-group">
     <div class="col-xs-offset-1 col-xs-10">
        @if(count($errors) >= 1)
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('name') }}</div>
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('address') }}</div>
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('town') }}</div>
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('state') }}</div>
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('tel') }}</div>
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('fax') }}</div>
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('cperson') }}</div>
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('mobile') }}</div>
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('email') }}</div>
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('description') }}</div>
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('rank') }}</div>
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('latitude') }}</div>
            <div class="alert alert-danger col-xs-offset-2 col-xs-5">{{ $errors->first('longitude') }}</div>
        @endif
     </div>
    </div>
    <div class="form-group">
        {{ Form::label('name', 'Shop name', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::text('name', $shop->name, array('class' => 'form-control')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('category', 'Category', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::select('category', $category_list, array_values($category_list), array('class' => 'form-control selectpicker')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('country', 'Country', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::select('country', $country_list, array_values($country_list), array('class' => 'form-control selectpicker')) }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('state', 'State', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::select('state', $state_list,  $stateId, array('class' => 'form-control selectpicker')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('town', 'Town', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::select('town', $town_list,  $townId, array('class' => 'form-control selectpicker')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('address', 'Address', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::textarea('address', $shop->address, array('class' => 'form-control', 'size' => '20x4')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('tel', 'Telephone', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::text('tel', $shop->tel, array('class' => 'form-control')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('fax', 'Fax', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::text('fax', $shop->fax, array('class' => 'form-control')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('cperson', 'Contact Person', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::text('cperson', $shop->cperson, array('class' => 'form-control')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('mobile', 'Mobile', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::text('mobile', $shop->mobile, array('class' => 'form-control')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('email', 'Email', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::text('email', $shop->email, array('class' => 'form-control')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('description', 'Description', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::textarea('description', $shop->description, array('class' => 'form-control', 'size' => '20x4')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('urlcom', 'URL Com', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::text('urlcom', $shop->urlcom, array('class' => 'form-control')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('urladv', 'URL Adv', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::text('urladv', $shop->urladv, array('class' => 'form-control')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('rank', 'Rank', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::text('rank', $shop->rank, array('class' => 'form-control')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('latitude', 'Latitude', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::text('latitude', $shop->latitude, array('class' => 'form-control')) }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('longitude', 'Longitude', array('class' => 'control-label col-xs-2')) }}
        <div class="col-xs-5">
            {{ Form::text('longitude', $shop->longitude, array('class' => 'form-control')) }}
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-offset-2 col-xs-10">
           <button type="submit" class="btn btn-primary">Edit</button>
        </div>
    </div>
{{ Form::close() }}

<!-- States Ajax Call to fill the drop down -->
{{ HTML::script('js/states.js'); }}
{{ HTML::script('js/town.js'); }}
@stop