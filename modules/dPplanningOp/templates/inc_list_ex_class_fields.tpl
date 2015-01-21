<ul>
  {{foreach from=$ex_class_fields item=_field}}
    <li data-id="{{$_field->_id}}" data-view="{{$_field}}">{{$_field}}</li>
  {{/foreach}}
</ul>
