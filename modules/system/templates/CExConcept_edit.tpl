<button type="button" class="new" onclick="MbObject.edit('{{$object->_class_name}}-0')">
  {{tr}}{{$object->_class_name}}-title-create{{/tr}}
</button>

<script type="text/javascript">
toggleListCustom = function(radio) {
  var enableList = (radio.value == "list");
	var list = radio.form.ex_list_id_autocomplete_view;
	
	list.up(".dropdown").down(".dropdown-trigger").setVisibility(enableList);
  list.disabled = list.readOnly = !enableList;
}
</script>

<form name="edit-{{$object->_guid}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  {{mb_class object=$object}}
  {{mb_key object=$object}}
  
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="MbObject.editCallback" />
  
  <table class="main form">
    
    {{mb_include module=system template=inc_form_table_header css_class="text" colspan=4}}
    
    {{if $object->_id}}
      {{mb_include module=system template=inc_tag_binder colspan=4}}
			
      <tr>
        <td colspan="4"><hr /></td>
      </tr>
    {{/if}}
    
    <tr>
      <th>{{mb_label object=$object field=name}}</th>
      <td>{{mb_field object=$object field=name}}</td>
			
      <th>
      	{{mb_label object=$object field=ex_list_id}}
				<input type="radio" name="_concept_type" value="list" {{if $object->ex_list_id}}checked="checked"{{/if}}
               onclick="toggleListCustom(this)" />
			</th>
      <td>
      	{{mb_field object=$object field=ex_list_id form="edit-`$object->_guid`" autocomplete="true,1,50,false,true"}}
				
				<strong> - OU - </strong> 
				<label>
					Type personnalisé
          <input type="radio" name="_concept_type" value="custom" {{if !$object->ex_list_id}}checked="checked"{{/if}}
					       onclick="toggleListCustom(this)"
					 />
				</label>
			</td>
    </tr>
    
    <tr>
      <td colspan="4" class="button">
        {{if $object->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax: true, typeName:'', objName:'{{$object->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

{{if $object->_id}}
{{mb_include_script module=forms script=ex_class_editor}}

<form name="CExListItem-create" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: MbObject.edit.curry('{{$object->_guid}}')})">
	{{mb_class class=CExListItem}}
	
  <input type="hidden" name="ex_list_item_id" value="" class="ref" />
	<input type="hidden" name="list_id" value="{{$object->_id}}" />
	
	<table class="main tbl">
	  <tr>
	    <th colspan="3" class="title">{{tr}}CExList-back-items{{/tr}}</th>
	  </tr>
		<tr>
	    <th class="narrow"></th>
	    <th class="narrow">{{mb_title class=CExListItem field=value}}</th>
	    <th>{{mb_title class=CExListItem field=name}}</th>
		</tr>
		
	  <tr>
	    <td>
	      <button class="add notext">{{tr}}Add{{/tr}}</button>
	    </td>
	    <td>{{mb_field class=CExListItem field=value}}</td>
	    <td>
	    	{{mb_field class=CExListItem field=name}}
				<button class="cancel notext" type="button" onclick="cancelEditListItem(this.form)" style="display: none">
					{{tr}}Cancel{{/tr}}
				</button>
			</td>
	  </tr>
		
		{{foreach from=$object->_back.items item=_item}}
		  <tr data-id="{{$_item->_id}}" data-name="{{$_item->name}}" data-value="{{$_item->value}}">
		    <td>
		    	<button class="edit notext" type="button" onclick="editListItem($(this).up('tr'))">
		    		{{tr}}Edit{{/tr}}
					</button>
		    </td>
		    <td>{{mb_value object=$_item field=value}}</td>
		    <td>{{mb_value object=$_item field=name}}</td>
		  </tr>
		{{foreachelse}}
		  <tr>
		  	<td class="empty" colspan="3">{{tr}}CExListItem.none{{/tr}}</td>
		  </tr>
		{{/foreach}}
	</table>
</form>

{{/if}}