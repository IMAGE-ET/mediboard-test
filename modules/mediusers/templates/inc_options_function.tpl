{{mb_default var=selected value=null}}
{{mb_default var=disabled value=null}}

{{foreach from=$list item=_function}}
  {{assign var=color value=$_function->color}}
  <option class="mediuser" 
          style="border-color: #{{$color}};" 
          value="{{$_function->_id}}" 
          {{if $selected == $_function->_id}} selected="selected" {{/if}}
          {{if $disabled == $_function->_id}} disabled="disabled" {{/if}}
	>
    {{$_function}}
  </option>
{{foreachelse}}
  {{if @$showEmptyList}}
  <option disabled="disabled">{{tr}}CFunction.none{{/tr}}</option>
  {{/if}}
{{/foreach}}