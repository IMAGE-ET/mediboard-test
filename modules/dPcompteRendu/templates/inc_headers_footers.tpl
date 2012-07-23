<option value="">&mdash; {{tr}}Choose{{/tr}}</option>
{{foreach from=$components item=componentsByOwner key=owner}}
<optgroup label="{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}">
  {{foreach from=$componentsByOwner item=_component}}
  <option value="{{$_component->_id}}">{{$_component->nom}}</option>
  {{foreachelse}}
  <option value="" disabled="disabled">{{tr}}None{{/tr}}</option>
  {{/foreach}}
</optgroup>
{{/foreach}}