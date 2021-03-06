@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-sm-offset-2 col-sm-8">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" href="#help" class="btn btn-default">
@lang('messages.help')
</a>
      </h4>
    </div>
    <div id="help" class="panel-collapse collapse">
      <div class="panel-body">
@lang('messages.hint_trait_create')
      </div>
    </div>
  </div>
            <div class="panel panel-default">
                <div class="panel-heading">
		@lang('messages.new_trait')
                </div>

                <div class="panel-body">
                    <!-- Display Validation Errors -->
		    @include('common.errors')

@if (isset($odbtrait))
		    <form action="{{ url('traits/' . $odbtrait->id)}}" method="POST" class="form-horizontal">
{{ method_field('PUT') }}

@else
		    <form action="{{ url('traits')}}" method="POST" class="form-horizontal">
@endif
		     {{ csrf_field() }}
<div class="form-group">
    <div class="col-sm-12">
<?php
// TODO: should be moved to be PSR compliant
function genInputTranslationTable($odbtrait, $type, $language, $order) {
    $text = "<td><input name='cat_" . $type . "[". $order . "][" . $language . "]' value='";
    if (is_numeric($order)) {
        if (isset($odbtrait)) {
            $cat = $odbtrait->categories->where('rank', $order)->first();
            if($cat) {
                switch($type) {
                case "name":
                    $get_old = $cat->translate(\App\UserTranslation::NAME, $language);
                    break;
                case "description":
                    $get_old = $cat->translate(\App\UserTranslation::DESCRIPTION, $language);
                }
            }
        }
        $text .= old('cat_' . $type .'.' . $order . '.' . $language, isset($get_old) ? $get_old : null);
    }
    $text .= "'></td>";
    return $text;
}

// call this function with order = int to use OLD values, order = null produces a blank category (for use in js)
function genTraitCategoryTranslationTable($order, $odbtrait) {
    if (is_null($order)) $order = "__PLACEHOLDER__";
    $TH = "<table class='table table-striped'> <thead>" .
        "<th class='table-ordinal'>" . Lang::get('messages.category_order') . " </th>" .
        "<th>" . Lang::get('messages.language') . " </th>" . 
        "<th class='mandatory'>" . Lang::get('messages.name') . " </th>" .
        "<th>" . Lang::get('messages.description') . " </th>" . 
        "</thead> <tbody>"; 
    $TB = '';
    $languages = \App\Language::all();
    $first = true;
    foreach ($languages as $language) {
        $TB .="<tr>";
        if ($first) {
            $TB .= "<td class='table-ordinal' rowspan=" . sizeof($languages) .  
                " style='vertical-align: middle; text-align: center;'>" . $order . "</td>";
            $first = false;
        }
        $TB .= "<td>" .$language->name. "</td>";
        $TB .= genInputTranslationTable($odbtrait, "name", $language->id, $order);
        $TB .= genInputTranslationTable($odbtrait, "description", $language->id, $order);
        $TB .="</tr>";
    } 
    $TF = "</tbody></table>";
    return $TH . $TB . $TF;
}
?>
<table class="table table-striped">
<thead>
    <th>
@lang('messages.language')
    </th>
    <th class='mandatory'>
@lang('messages.name')
    </th>
    <th>
@lang('messages.description')
    </th>
</thead>
<tbody>
@foreach ($languages as $language) 
    <tr>
        <td>{{$language->name}}</td>
        <td><input name="name[{{$language->id}}]" value="{{ old('name.' . $language->id, isset($odbtrait) ? $odbtrait->translate(\App\UserTranslation::NAME, $language->id) : null) }}"></td>
        <td><input name="description[{{$language->id}}]" value="{{ old('description.' . $language->id, isset($odbtrait) ? $odbtrait->translate(\App\UserTranslation::DESCRIPTION, $language->id) : null) }}"></td>
    </tr>
@endforeach
    <tr>
</tbody>
</table>
    </div>
</div>

<div class="form-group">
    <label for="export_name" class="col-sm-3 control-label mandatory">
        @lang('messages.export_name')
    </label>
        <a data-toggle="collapse" href="#hinte" class="btn btn-default">?</a>
	    <div class="col-sm-6">
        <input name="export_name" value="{{ old('export_name', isset($odbtrait) ? $odbtrait->export_name : null) }}"
    class="form-control">
        </div>
  <div class="col-sm-12">
    <div id="hinte" class="panel-collapse collapse">
	@lang('messages.trait_export_hint')
    </div>
  </div>
</div>
    
<div class="form-group">
    <label for="type" class="col-sm-3 control-label mandatory">
@lang('messages.type')
</label>
        <a data-toggle="collapse" href="#hintp" class="btn btn-default">?</a>
	    <div class="col-sm-6">
	<?php $selected = old('type', isset($odbtrait) ? $odbtrait->type : null); ?>

	<select name="type" id="type" class="form-control" >
	@foreach (\App\ODBTrait::TRAIT_TYPES as $ttype)
		<option value="{{ $ttype }}" {{ $ttype == $selected ? 'selected' : '' }}>
@lang('levels.traittype.' . $ttype)
		</option>
	@endforeach
	</select>
            </div>
  <div class="col-sm-12">
    <div id="hintp" class="panel-collapse collapse">
	@lang('messages.trait_type_hint')
    </div>
  </div>
</div>
<div class="form-group">
    <label for="objects" class="col-sm-3 control-label mandatory">
@lang('messages.object_types')
</label>
        <a data-toggle="collapse" href="#hint3" class="btn btn-default">?</a>
	    <div class="col-sm-6">
{!! Multiselect::select(
    'objects', 
    \App\ODBTrait::getObjectTypeNames(), 
    isset($odbtrait) ? $odbtrait->getObjectKeys() : [],
    ['class' => 'multiselect form-control']
) !!}
            </div>
  <div class="col-sm-12">
    <div id="hint3" class="panel-collapse collapse">
	@lang('messages.trait_objects_hint')
    </div>
  </div>
</div>
<div class="form-group trait-number">
    <label for="unit" class="col-sm-3 control-label mandatory">
@lang('messages.unit')
</label>
        <a data-toggle="collapse" href="#hint1" class="btn btn-default">?</a>
	    <div class="col-sm-6">
	<input type="text" name="unit" id="unit" class="form-control" value="{{ old('unit', isset($odbtrait) ? $odbtrait->unit : null) }}">
            </div>
  <div class="col-sm-12">
    <div id="hint1" class="panel-collapse collapse">
@lang('messages.hint_trait_unit')
    </div>
  </div>
</div>
<div class="form-group trait-number">
    <label for="range_min" class="col-sm-3 control-label">
@lang('messages.range_min')
</label>
        <a data-toggle="collapse" href="#hint11" class="btn btn-default">?</a>
	    <div class="col-sm-6">
	<input type="text" name="range_min" id="range_min" class="form-control" value="{{ old('range_min', isset($odbtrait) ? $odbtrait->range_min : null) }}">
            </div>
  <div class="col-sm-12">
    <div id="hint11" class="panel-collapse collapse">
@lang('messages.hint_trait_min')
    </div>
  </div>
</div>
<div class="form-group trait-number">
    <label for="range_max" class="col-sm-3 control-label">
@lang('messages.range_max')
</label>
        <a data-toggle="collapse" href="#hint12" class="btn btn-default">?</a>
	    <div class="col-sm-6">
	<input type="text" name="range_max" id="range_max" class="form-control" value="{{ old('range_max', isset($odbtrait) ? $odbtrait->range_max : null) }}">
            </div>
  <div class="col-sm-12">
    <div id="hint12" class="panel-collapse collapse">
@lang('messages.hint_trait_max')
    </div>
  </div>
</div>
<div class="form-group trait-category">
<div class="col-sm-12" id="to_append_categories">
<h3> @lang('messages.categories') </h3>
<?php 
if (isset($odbtrait)) {
    // do we have "old" input?
    if (empty(old()) or empty(old("cat_name"))) {
        foreach($odbtrait->categories as $category) {
            echo genTraitCategoryTranslationTable($category->rank, $odbtrait); 
        }
    } else {
        foreach(array_keys(old("cat_name")) as $rank) {
            echo genTraitCategoryTranslationTable($rank, $odbtrait); 
        }
    }
} else { // no odbtrait, so we're creating a new
    // do we have "old" input?
    if (empty(old()) or empty(old("cat_name"))) {
        foreach([1,2,3] as $rank) {
            echo genTraitCategoryTranslationTable($rank, null); 
        }
    } else {
        foreach(array_keys(old("cat_name")) as $rank) {
            echo genTraitCategoryTranslationTable($rank, null); 
        }
    }
} 
?>
</div>
<div class="col-sm-12">
				<button type="submit" class="btn btn-default" id="add_category">
				    <i class="glyphicon glyphicon-plus"></i>
@lang('messages.add_category')
				</button>

</div>
</div>
<div class="form-group trait-link">
    <label for="link_type" class="col-sm-3 control-label mandatory">
@lang('messages.link_type')
</label>
        <a data-toggle="collapse" href="#hintlt" class="btn btn-default">?</a>
	    <div class="col-sm-6">
	<?php $selected = old('type', isset($odbtrait) ? $odbtrait->link_type : null); ?>
	<select name="link_type" id="link_type" class="form-control" >
	@foreach (\App\ODBTrait::LINK_TYPES as $ttype)
		<option value="{{ $ttype }}" {{ $ttype == $selected ? 'selected' : '' }}>
@lang('classes.' . $ttype)
		</option>
	@endforeach
	</select>
            </div>
  <div class="col-sm-12">
    <div id="hintlt" class="panel-collapse collapse">
@lang('messages.hint_trait_link_type')
    </div>
  </div>
</div>

		        <div class="form-group">
			    <div class="col-sm-offset-3 col-sm-6">
				<button type="submit" class="btn btn-success" name="submit" value="submit">
				    <i class="fa fa-btn fa-plus"></i>
@lang('messages.add')

				</button>
				<a href="{{url()->previous()}}" class="btn btn-warning">
				    <i class="fa fa-btn fa-plus"></i>
@lang('messages.back')
				</a>
			    </div>
			</div>
		    </form>
        </div>
    </div>
@endsection

@push ('scripts')
<script>
$(document).ready(function() {
	function setFields(vel) {
		var adm = $('#type option:selected').val();
		if ("undefined" === typeof adm) {
			return; // nothing to do here...
		}
		switch (adm) {
			case "0": // numeric
			case "1": // numeric FALL THROUGH
				$(".trait-number").show(vel);
				$(".trait-category").hide(vel);
				$(".trait-link").hide(vel);
				break;
			case "2": // categories
			case "3": // categories FALL THROUGH
				$(".trait-number").hide(vel);
				$(".trait-category").show(vel);
				$(".table-ordinal").hide(vel);
				$(".trait-link").hide(vel);
				break;
			case "4": // ordinal
				$(".trait-number").hide(vel);
				$(".trait-category").show(vel);
				$(".table-ordinal").show(vel);
				$(".trait-link").hide(vel);
				break;
            case "7": // link
				$(".trait-number").hide(vel);
				$(".trait-category").hide(vel);
				$(".table-ordinal").hide(vel);
				$(".trait-link").show(vel);
                break;
			default: // other
				$(".trait-number").hide(vel);
				$(".trait-category").hide(vel);
				$(".trait-link").hide(vel);
		}
	}
	$("#type").change(function() { setFields(400); });
    // trigger this on page load
	setFields(0);
    $("#add_category").click(function(event) {
        event.preventDefault();
        var text = "<?php echo genTraitCategoryTranslationTable(null, null); ?>";
        // infers the number of categories already displayed by the number of table-ordinal headers
        var newcat = $('th.table-ordinal').length + 1;
        text = text.replace(/__PLACEHOLDER__/g, newcat);
        $('#to_append_categories').append(text);
        setFields(0); 
    });
});
</script>
@endpush
