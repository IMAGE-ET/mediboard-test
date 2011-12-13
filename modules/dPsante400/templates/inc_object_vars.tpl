{{foreach from=$vars key=_view item=_value}}
  <button type="button" onclick="this.form.pattern.replaceInputSelection(this.value)" value="{{$_value}}" class="up">{{$_view}}</button>
{{/foreach}}