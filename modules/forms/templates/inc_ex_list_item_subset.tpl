<script type="text/javascript">
toggleListItem = function(button, value, active) {
  var form = getForm("editFieldSpec");
  var item = form.down("input[name='list[]'][value='"+value+"']");
  var row = button.up('tr');
  var checkbox_default_value = row.down("input[name=__default_item]");
  checkbox_default_value.disabled = !active;
  
  if (!active){
    item.disabled = true;
    row.addClassName('opacity-30');
    row.down('button.add').show();
    button.hide();
    
    if (window["__defaultTF_"+value]) {
      checkbox_default_value.checked = false;
      window["__defaultTF_"+value].toggle(value, false);
    }
  }
  else {
    item.disabled = false;
    row.removeClassName('opacity-30');
    row.down('button.remove').show();
    button.hide();
  }
  
  $("save-to-take-effect").show();
  updateFieldSpec();
}

moveListItem = function(e, way){
  var currTr = $(e).up("tr");
  var refTr = currTr[ (way == "up") ? "previous" : "next" ]();
  
  if (!refTr) return;
  
  if (way == "up")
    currTr.insert({after: refTr});
  else
    currTr.insert({before: refTr});
  
  $("save-to-take-effect").show();
  updateFieldSpec();
}

</script>

{{assign var=coded value=false}}

{{if $list_owner instanceof CExList && $list_owner->coded == 1}}
  {{assign var=coded value=true}}
{{/if}}

{{*  
{{foreach from=$items_all item=_value}}
{{/foreach}}
*}}

<table class="main tbl">
  <col class="narrow" />
  
  <tr>
    <th colspan="5" class="title">
      {{tr}}CExList-back-list_items{{/tr}}
      
      <a class="button edit" href="?m=forms&amp;tab=view_ex_list&amp;object_guid={{$list_owner->_guid}}">
        {{tr}}CExList-title-modify{{/tr}}
      </a>
    </th>
  </tr>
  
  <tr>
    {{if $context instanceof CExClassField}}
      <th></th>
    {{/if}}
    
    {{if $coded}}
      <th class="narrow code">
        {{mb_title class=CExListItem field=code}}
      </th>
    {{/if}}
    
    <th>
      {{mb_title class=CExListItem field=name}}
    </th>
    
    {{if $context instanceof CExClassField}}
      <th class="narrow">Formulaire à déclencher</th>
    {{/if}}
    
    {{if !$context instanceof CExConcept}}
      <th class="narrow">
        Coché <br />par défaut
        {{mb_include module=forms template=inc_ex_list_default_value value=""}}
      </th>
    {{/if}}
  </tr>
  
  <tbody>
  {{foreach from=$items_all item=_value}}
    {{assign var=_item value=$list_owner->_back.list_items.$_value}}
    {{assign var=_item_id value=$_item->_id}}
    {{assign var=active value=false}}
    
    {{if array_key_exists($_item->_id, $spec->_locales)}}
      {{assign var=active value=true}}
    {{/if}}
    
    <tr data-id="{{$_item->_id}}" data-name="{{$_item->name}}" data-code="{{$_item->code}}" {{if !$active}}class="opacity-30"{{/if}}>
      {{if $context instanceof CExClassField}}
      <td>
        <input type="hidden" name="list[]" class="internal" value="{{$_value}}" {{if !in_array($_value,$items_sub)}}disabled="disabled"{{/if}} />

        <button class="remove notext compact" type="button" style="{{if !$active}}display: none;{{/if}}" 
                onclick="toggleListItem(this, {{$_item->_id}}, false);">
          {{tr}}Delete{{/tr}}
        </button>
        
        <button class="add notext compact" type="button" style="{{if $active}}display: none;{{/if}}" 
                onclick="toggleListItem(this, {{$_item->_id}}, true);">
          {{tr}}Add{{/tr}}
        </button>
        
        <button class="down notext compact" type="button" onclick="moveListItem(this, 'down')" title="Descendre"></button>
        <button class="up notext compact" type="button" onclick="moveListItem(this, 'up')" title="Monter"></button>
      </td>
      {{/if}}
      
      {{if $coded}}
        <td class="code">{{mb_value object=$_item field=code}}</td>
      {{/if}}
      
      <td class="text">{{mb_value object=$_item field=name}}</td>
      
      {{if $context instanceof CExClassField}}
        {{if $triggerables|@count}}
          <td>
            <select class="triggered-data-select" onchange="updateTriggerData($V(this), '{{$_item->_id}}')" style="max-width: 20em;">
              <option value=""> &mdash; </option>
              {{foreach from=$triggerables item=_triggerable}}
                {{assign var=_trigger_value value=$_triggerable->_id}}
                <option value="{{$_trigger_value}}" {{if array_key_exists($_item_id, $context->_triggered_data) && $context->_triggered_data.$_item_id == $_trigger_value}}selected="selected"{{/if}}>
                  {{$_triggerable->name}}
                </option>
              {{/foreach}}
            </select>
          </td>
        {{else}}
          <td class="empty">Aucun formulaire à déclencher</td>
        {{/if}}
      {{/if}}
      
      {{if !$context instanceof CExConcept}}
      <td style="text-align: center;">
        {{mb_include module=forms template=inc_ex_list_default_value value=$_value}}
      </td>
      {{/if}}
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="4">{{tr}}CExListItem.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  </tbody>
</table>

