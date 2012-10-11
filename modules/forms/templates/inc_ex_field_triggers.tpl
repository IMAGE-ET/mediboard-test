{{if $triggerables_cond|@count || $triggerables_others|@count}}
  <td>
    <select class="triggered-data-select" onchange="updateTriggerData($V(this), '{{$_value}}')" style="max-width: 20em;">
      <option value=""> &mdash; </option>
      <optgroup label="Sous-formulaires">
        {{foreach from=$triggerables_cond item=_triggerable}}
          {{assign var=_trigger_value value=$_triggerable->_id}}
          <option value="{{$_trigger_value}}" {{if array_key_exists($_value, $context->_triggered_data) && $context->_triggered_data.$_value == $_trigger_value}}selected="selected"{{/if}}>
            {{$_triggerable->name}}
          </option>
        {{/foreach}}
      </optgroup>
      
      <optgroup label="Autres">
        {{foreach from=$triggerables_others item=_triggerable}}
          {{assign var=_trigger_value value=$_triggerable->_id}}
          <option value="{{$_trigger_value}}" {{if array_key_exists($_value, $context->_triggered_data) && $context->_triggered_data.$_value == $_trigger_value}}selected="selected"{{/if}}>
            {{$_triggerable->name}}
          </option>
        {{/foreach}}
      </optgroup>
    </select>
  </td>
{{else}}
  <td class="empty">Aucun formulaire à déclencher</td>
{{/if}}