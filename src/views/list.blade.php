@extends('admin::layout')
@section('title', $instance->getTitle())
@section('content')
<div class="ui grid">
    <div class="two wide column">
        @foreach($instance->getLeftActions() as $action => $info)
        <a class="ui button action {{ $action }} {{ $info['color'] or 'blue' }}" href="{{ $info['url'] }}">
            {{ $info['description'] or $action }}
        </a>
        @endforeach
    </div>
    <div class="fourteen wide column right aligned">
        <form id="search" action="">
        @foreach ($instance->getFilterableColumns() as $column)
        <input type="hidden" name="{{ $column->name }}" value="{{ $query[$column->name] or '' }}">
        @endforeach
        @foreach ($instance->getSearchableColumns() as $column)
        <div class="ui right labeled input">
            <input type="text" placeholder="查询{{ $column->description }}" name="{{ $column->name }}">
            <div class="ui basic label">
                {{ $column->description }}
            </div>
        </div>
        @endforeach
        @if (count($instance->getSearchableColumns())) 
        <div class="ui button action blue search">
            <i class="search icon"></i>
            搜索
        </div>
        @endif
        @foreach($instance->getSingleActions() as $action => $info)
        @if ($info['type'] == 'url')
        @if(Auth::user()->canVisit($info['url']))
        <div class="ui button action {{ $action }} {{ $info['color'] or '' }}">
            <i class="{{ $info['icon'] or 'edit' }} icon"></i>
            {{ $info['description'] or $action }}
        </div>
        @endif
        @else
        @if(Auth::user()->canVisit(action($controller."@admin", ["action" => $action])))
        <div class="ui button action {{ $action }} {{ $info['color'] or '' }}">
            <i class="{{ $info['icon'] or 'edit' }} icon"></i>
            {{ $info['description'] or $action }}
        </div>
        @endif
        @endif
        @endforeach
        </form>
    </div>
</div>
<table class="ui table celled">
    <thead>
        <tr>
            @foreach($instance->getListableColumns() as $column)
            <th>
                @if($instance->canFilterColumn($column->name))
                <select class="ui dropdown filter" name="{{ $column->name }}">
                    <option value="*">全部{{ $column->description }}</option>
                    @foreach($instance->getValueGroups($column->name) as $key => $value)
                    <option @if( (Request::input($column->name) != '*') && (Request::input($column->name) == $key) && (!is_null(Request::input($column->name))) ) selected @endif value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                @else
                {{ $instance->getColumnDescription($column->name) }}
                @endif
            </th>
            @endforeach
            @if(count($instance->getEachActions()))
            <th>
                操作
            </th>
            @endif
        </tr>
    </thead>
    @foreach($data as $item)
    <tr>
        @foreach($instance->getListableColumns() as $column)
        <td>
            @if($instance->isSwitchable($column->name))
            <div class="ui toggle checkbox">
                <input data-id="{{ $item->id }}" class="switch" type="checkbox" name="{{ $column->name }}" @if($item->getRawValue($column->name)) checked @endif>
                <label></label>
            </div>
            @else
            {{ $item->getValue($column->name) }}
            @endif
        </td>
        @endforeach
        @if(count($instance->getEachActions()))
        <td>
            @foreach($instance->getEachActions() as $action => $info)
            @if ($info['type'] == 'url')
            @if(Auth::user()->canVisit($info['url']))
            <button class="ui basic button action {{ $action }} {{ $info['color'] or ''}}" data-id="{{ $item->id }}">
                <i class="{{ $info['icon'] or 'edit' }} icon"></i>
                {{ $info['description'] or $action }}
            </button>
            @endif
            @else
            @if(Auth::user()->canVisit(action($controller."@admin", ["action" => $action])))
            <button class="ui basic button action {{ $action }} {{ $info['color'] or ''}}" data-id="{{ $item->id }}">
                <i class="{{ $info['icon'] or 'edit' }} icon"></i>
                {{ $info['description'] or $action }}
            </button>
            @endif
            @endif
            @endforeach
        </td>
        @endif
    </tr>
    @endforeach
</table>
<div class="ui right floated pagination menu">
            <a class="icon item" href="{{ $data->previousPageUrl() }}">
              <i class="left chevron icon"></i>
            </a>
            <a class="item">{{ $data->currentPage() }}</a>
            @if($data->hasMorePages())
            <a class="icon item" href="{{ $data->nextPageUrl() }}">
              <i class="right chevron icon"></i>
            </a>
@endif
          </div>
<script>
$(function(){
    @foreach($instance->getSingleActions() as $action => $info)
    $('.button.action.{{ $action }}').click(function(){
        @if ($info['type'] == 'confirm')
            Dialog.confirm('确认{{ $info['description'] or $action}}?', '{{ action($controller."@admin", ["action" => $action]) }}' );
        @elseif ($info['type'] == 'modal')
            Dialog.modal('{{ action($controller."@admin", ["action" => $action]) }}');
        @else
            location.href = '/admin/{{ $info['url'] }}';
        @endif
    });
    @endforeach
    @foreach($instance->getEachActions() as $action => $info)
    $('.button.action.{{ $action }}').click(function(){
        var id = $(this).data('id');
        @if ($info['type'] == 'confirm')
            Dialog.confirm('确认{{ $info['description'] or $action}}?', '{{ action($controller."@admin", ["action" => $action]) }}/' + id);
        @elseif ($info['type'] == 'modal')
            Dialog.modal('{{ action($controller."@admin", ["action" => $action]) }}/' + id);
        @elseif ($info['type'] == 'url')
            location.href = '/admin/{{ $info['url'] }}' + id;
            @endif
    });
    @endforeach
    $('.filter select').change(function(){
        console.log($(this).val());
        $('#search input[name=' + $(this).attr('name') + ']').val($(this).val());
        console.log($('#search input[name=' + $(this).attr('name') + ']').val());
        $('#search').submit();
    });
    $('.switch').change(function(){
        var id = $(this).data('id');
        $.post('{{ action($controller."@admin", ["action" => 'switch']) }}_' + $(this).attr('name') +'/' + id, function(){
            location.reload();
        });

    });
    $('.search').click(function(){
        $('#search').submit();
    });
    $('.ui.toggle.checkbox').checkbox();
});
</script>
@endsection
