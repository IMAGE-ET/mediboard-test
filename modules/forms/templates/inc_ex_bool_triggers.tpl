<div class="small-info" style="display: none;" id="save-to-take-effect">
  <strong>Enregistrez</strong> pour que la modifiation prenne effet
</div>

<table class="main tbl">
  <col class="narrow" />
  
  <tr>
    <th colspan="2" class="title">
      Sous formulaires
    </th>
  </tr>
  
  <tr>
    <th>
      Valeur
    </th>
    <th>Formulaire à déclencher</th>
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
    </tr>
  {{/foreach}}
  </tbody>
</table>

