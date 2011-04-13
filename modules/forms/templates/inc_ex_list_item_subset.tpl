<script type="text/javascript">
toggleListItem = function(button, value, active) {
  var form = getForm("editFieldSpec");
  var item = form.down("input[name='list[]'][value='"+value+"']");
  var row = button.up('tr');
  
  if (!active){
    item.disabled = true;
    row.addClassName('opacity-30');
    row.down('button.add').show();
    button.hide();
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

<div class="small-info" style="display: none;" id="save-to-take-effect">
	<strong>Enregistrez</strong> pour que la modifiation prenne effet
</div>

{{*  
{{foreach from=$items_all item=_value}}
{{/foreach}}
*}}

<table class="main tbl">
	<col class="narrow" />
	
  <tr>
    <th colspan="4" class="title">
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
      <th>Formulaire à déclencher</th>
    {{/if}}
		
		<th class="narrow">Coché <br />par défaut</th>
  </tr>
  
	<tbody>
  {{foreach from=$items_all item=_value}}
    {{assign var=_item value=$list_owner->_back.list_items.$_value}}
	  {{assign var=active value=false}}
		
		{{if array_key_exists($_item->_id, $spec->_locales)}}
	    {{assign var=active value=true}}
		{{/if}}
		
    <tr data-id="{{$_item->_id}}" data-name="{{$_item->name}}" data-code="{{$_item->code}}" {{if !$active}}class="opacity-30"{{/if}}>
      {{if $context instanceof CExClassField}}
      <td>
				<input type="hidden" name="list[]" class="internal" value="{{$_value}}" {{if !in_array($_value,$items_sub)}}disabled="disabled"{{/if}} />

        <button class="remove notext" type="button" style="margin: -1px; {{if !$active}}display: none;{{/if}}" 
				        onclick="toggleListItem(this, {{$_item->_id}}, false);">
          {{tr}}Delete{{/tr}}
        </button>
				
        <button class="add notext" type="button" style="margin: -1px; {{if $active}}display: none;{{/if}}" 
				        onclick="toggleListItem(this, {{$_item->_id}}, true);">
          {{tr}}Add{{/tr}}
        </button>
				
        <button class="down notext" type="button" style="margin: -1px;" onclick="moveListItem(this, 'down')" title="Descendre"></button>
        <button class="up notext" type="button" style="margin: -1px;" onclick="moveListItem(this, 'up')" title="Monter"></button>
      </td>
      {{/if}}
			
      {{if $coded}}
        <td class="code">{{mb_value object=$_item field=code}}</td>
			{{/if}}
			
      <td>{{mb_value object=$_item field=name}}</td>
      
      {{if $context instanceof CExClassField}}
        {{if $triggerables|@count}}
          <td>
            <select class="triggered-data-select" onchange="updateTriggerData(this)">
              <option value=""> &mdash; </option>
              {{foreach from=$triggerables item=_triggerable}}
                {{assign var=_trigger_value value="`$_triggerable->_id`-`$_item->_id`"}}
                <option value="{{$_trigger_value}}" {{if $context->_triggered_data == $_trigger_value}}selected="selected"{{/if}}>
                  {{$_triggerable->name}}
                </option>
              {{/foreach}}
            </select>
          </td>
        {{else}}
          <td class="empty">Aucun formulaire à déclencher</td>
        {{/if}}
      {{/if}}
			
      <td style="text-align: center;">
        {{mb_include module=forms template=inc_ex_list_default_value value=$_value}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="4">{{tr}}CExListItem.none{{/tr}}</td>
    </tr>
  {{/foreach}}
	</tbody>
</table>

