<script type="text/javascript">
editListItem = function(line) {
  var form = line.up('form');
  $V(form.ex_list_item_id, line.get('id'));
  $V(form.elements.name, line.get('name'));
  $V(form.elements.code, line.get('code'));
  var button = form.down('button');
  button.removeClassName('add').addClassName('save');
  form.down('button.cancel').setVisibility(true);
}

cancelEditListItem = function(form) {
  $V(form.ex_list_item_id, "");
  $V(form.elements.name, "");
  $V(form.elements.code, "");
  var button = form.down('button');
  button.removeClassName('save').addClassName('add');
  form.down('button.cancel').setVisibility(false);
}
  
Main.add(function(){
  var form = getForm("edit-{{$object->_guid}}");
  
  $A(form.elements.coded).each(function(e){
    e.observe("click", function(){
      getForm('CExListItem-create').select("table .code").invoke('setVisible', e.value == 1);
    });
  });
});
</script>

{{assign var=coded value=false}}

{{if !$object instanceof CExList || $object->coded == 1}}
  {{assign var=coded value=true}}
{{/if}}

<form name="CExListItem-create" method="post" action="?" 
      onsubmit="return onSubmitFormAjax(this, {onComplete: MbObject.edit.curry('{{$object->_guid}}')})">
      	
  {{mb_class class=CExListItem}}
  <input type="hidden" name="ex_list_item_id" value="" class="ref" />
	
	{{assign var=owner value="list_id"}}
	
	{{if $object instanceof CExConcept}}
    {{assign var=owner value="concept_id"}}
	{{elseif $object instanceof CExClassField}}
    {{assign var=owner value="field_id"}}
	{{/if}}
	
	<input type="hidden" name="{{$owner}}" value="{{$object->_id}}" />
  
  <table class="main tbl">
    <tr>
      <th colspan="4" class="title">{{tr}}CExList-back-list_items{{/tr}}</th>
    </tr>
		
    <tr>
      <th class="narrow"></th>
      <th class="narrow code" {{if !$coded}}style="display: none"{{/if}}>
        {{mb_title class=CExListItem field=code}}
      </th>
      <th>
        {{mb_title class=CExListItem field=name}}
      </th>
			<th class="narrow"></th>
    </tr>
    
    <tr>
      <td>
        <button class="add notext" style="margin: -1px;">{{tr}}Add{{/tr}}</button>
      </td>
      <td class="code" {{if !$coded}}style="display: none"{{/if}}>{{mb_field class=CExListItem field=code size=6}}</td>
      <td>
        {{mb_field class=CExListItem field=name style="width: 99%;"}}
			</td>
			<td>
        <button class="cancel notext" type="button" onclick="cancelEditListItem(this.form)" style="margin: -1px; visibility: hidden;">
          {{tr}}Cancel{{/tr}}
        </button>
      </td>
    </tr>
    
    {{foreach from=$object->_back.list_items item=_item}}
      <tr data-id="{{$_item->_id}}" data-name="{{$_item->name}}" data-code="{{$_item->code}}">
        <td>
          <button class="edit notext" type="button" style="margin: -1px;" onclick="editListItem($(this).up('tr'))">
            {{tr}}Edit{{/tr}}
          </button>
        </td>
        <td class="code" {{if !$coded}}style="display: none"{{/if}}>{{mb_value object=$_item field=code}}</td>
        <td>{{mb_value object=$_item field=name}}</td>
				<td></td>
      </tr>
    {{foreachelse}}
      <tr>
        <td class="empty" colspan="4">{{tr}}CExListItem.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</form>
