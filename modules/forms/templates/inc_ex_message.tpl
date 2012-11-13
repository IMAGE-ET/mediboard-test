
{{assign var=_properties value=$_message->_default_properties}}

{{assign var=_style value=""}}
{{foreach from=$_properties key=_type item=_value}}
  {{assign var=_style value="$_style $_type:$_value;"}}
{{/foreach}}

{{if $_message->type == "title"}}
  <div class="ex-message-title" style="{{$_style}}">
    {{$_message->text}}
  </div>
  <span class="ex-message-title-spacer">&nbsp;</span>
{{else}}
  <div class="ex-message small-{{$_message->type}}" style="{{$_style}}">{{$_message->text}}</div>
{{/if}}