{{mb_include module=system template=CMbObject_edit}}

{{if $object->_id}}

<script type="text/javascript">
editListItem = function(line) {
  var form = line.up('form');
  $V(form.ex_list_item_id, line.get('id'));
  $V(form.elements.name, line.get('name'));
  $V(form.elements.value, line.get('value'));
	var button = form.down('button');
  button.removeClassName('add').addClassName('save');
	form.down('button.cancel').show();
}

cancelEditListItem = function(form) {
  $V(form.ex_list_item_id, "");
  $V(form.elements.name, "");
  $V(form.elements.value, "");
  var button = form.down('button');
  button.removeClassName('save').addClassName('add');
  form.down('button.cancel').hide();
}
</script>

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