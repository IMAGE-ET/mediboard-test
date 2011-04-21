<div class="small-info" style="display: none;" id="save-to-take-effect">
  <strong>Enregistrez</strong> pour que la modifiation prenne effet
</div>

<table class="main tbl">
  <col class="narrow" />
  
  <tr>
    <th>Valeur</th>
    <th>Formulaire à déclencher</th>
    <th class="narrow">
      Coché par<br />défaut
    </th>
  </tr>
  
  <tbody>
  {{foreach from=","|explode:"1,0" item=_value}}
    <tr>
      <td>{{tr}}bool.{{$_value}}{{/tr}}</td>
      
      {{if $triggerables|@count}}
        <td>
          <select class="triggered-data-select" onchange="updateTriggerData(this)" style="max-width: 20em;">
            <option value=""> &mdash; </option>
            {{foreach from=$triggerables item=_triggerable}}
              {{assign var=_trigger_value value="`$_triggerable->_id`-`$_value`"}}
              <option value="{{$_trigger_value}}" {{if $context->_triggered_data == $_trigger_value}}selected="selected"{{/if}}>
                {{$_triggerable->name}}
              </option>
            {{/foreach}}
          </select>
        </td>
      {{else}}
        <td class="empty">Aucun formulaire à déclencher</td>
      {{/if}}
			
			<td style="text-align: center;">
				<input type="radio" name="default" value="{{$_value}}" {{if $spec->default == $_value}}checked="checked"{{/if}} />
			</td>
    </tr>
  {{/foreach}}
  </tbody>
</table>

