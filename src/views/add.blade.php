<i class="close icon"></i>
<div class="header">
添加
</div>
<div class="content">
    <form id="admin-form" class="ui form" action="{{ action($controller."@admin", ["action" => "create", "id" => $instance->id]) }}" method="POST">
        @foreach ($instance->getEditableColumns() as $column)
        <div class="field">
            <label>{{ $column->description }}</label>
            @if ($column->type == 'boolean')
            <div class="ui toggle checkbox">
                <input data-id="{{ $instance->id }}" type="checkbox" name="{{ $column->name }}">
                <label></label>
            </div>
            @elseif($column->type == 'enum')
            <select class="ui dropdown" name="{{ $column->name }}">
                @foreach($column->values as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            @elseif($column->type == 'extended')
            @if(is_array($instance->getExtendedType($column->name)))
            <select class="ui dropdown {{ $column->name }}" name="{{ $instance->getExtendedName($column->name) }}">
                @foreach($instance->getExtendedType($column->name) as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            @else
            <input name="{{ $column->name }}" placeholder="{{ $column->description }}" type="file">
            <img class="preview {{ $column->name }}" src="{{ $instance->getValue($column->name)  }}"/>
            @endif
            @else
            <input name="{{ $column->name }}" placeholder="{{ $column->description }}" type="text">
            @endif
        </div>
        @endforeach
    </form>
</div>
<script>
$(function(){
    $('#admin-form .ui.checkbox').checkbox();
    $('#admin-form .ui.dropdown').dropdown();
    $('#admin-form input[type="file"]').change(function(){
        $that = $(this);
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.preview.'+$that.attr('name')).attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    $('#admin-form .ui.dropdown.province').dropdown({
        onChange: function(value, text, choice){
            $.get('/city/' + value, function(data){
                content = "";
                for(var i in data){
                    content += "<option value='" + data[i].id + "'>" + data[i].name + "</option>";
                }
                $('#admin-form .city select').html(content);
                $('#admin-form .city').dropdown('clear').dropdown('setup menu');
                $('#admin-form .district select').html("");
                $('#admin-form .district').dropdown('clear').dropdown('setup menu');
            });
        }
    });
    $('#admin-form .ui.dropdown.city').dropdown({
        onChange: function(value, text, choice){
            $.get('/district/' + value, function(data){
                content = "";
                for(var i in data){
                    content += "<option value='" + data[i].id + "'>" + data[i].name + "</option>";
                }
                $('#admin-form .district select').html(content);
                $('#admin-form .district').dropdown('clear').dropdown('setup menu');

            });
        }
    });

});

</script>

