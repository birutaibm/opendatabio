@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
<p>@lang ('messages.welcome_message', compact('nplants', 'nvouchers'))</p>

@if (Auth::user()->person)
<p>@lang ('messages.your_default_person'): 
    <a href="{{url('persons/'.Auth::user()->person_id)}}">{{Auth::user()->person->fullname}}</a>
</p>
@else
<p>@lang ('messages.no_default_person') 
    <a href="{{url('/selfedit')}}">@lang('messages.here')</a>
</p>

@endif

@if (Auth::user()->projects()->count())
<p><strong>@lang('messages.projects'):</strong></p>
<ul>
@foreach (Auth::user()->projects as $project)
    <li><a href="{{url('projects/' . $project->id)}}">{{$project->name}}</a>
(@lang('levels.project.' . $project->pivot->access_level )
)</li>
@endforeach
    </ul>
@endif
@if (Auth::user()->datasets()->count())
<p><strong>@lang('messages.datasets'):</strong></p>
<ul>
@foreach (Auth::user()->datasets as $dataset)
    <li><a href="{{url('datasets/' . $dataset->id)}}">{{$dataset->name}}</a>
(@lang('levels.project.' . $dataset->pivot->access_level )
)</li>
@endforeach
    </ul>
@endif




                </div>
            </div>
        </div>
    </div>
</div>
@endsection
