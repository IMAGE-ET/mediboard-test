<script type="text/javascript">
MbObject.editCallback = function(){
  location.reload(true);
}
</script>
				
<button type="button" class="new" onclick="MbObject.edit('{{$object->_class}}-0')">
  {{tr}}{{$object->_class}}-title-create{{/tr}}
</button>

<form name="edit-{{$object->_guid}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  {{mb_class object=$object}}
  {{mb_key object=$object}}
	{{mb_field object=$object field=object_class hidden=true}}
	
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="MbObject.editCallback" />
  
  <table class="main form">
    <col class="narrow" />
    
    {{mb_include module=system template=inc_form_table_header css_class="text"}}
		
    <tr>
      <th>{{mb_label object=$object field=name}}</th>
      <td>{{mb_field object=$object field=name}}</td>
    </tr>
		
		<tr>
      <th>{{mb_label object=$object field=parent_id}}</th>
      <td>{{mb_field object=$object field=parent_id form="edit-`$object->_guid`" autocomplete="true,1,50,false,true"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$object field=color}}</th>
      <td>
        <script type="text/javascript">
	        ColorSelector.init = function(){
	          this.sForm  = "edit-{{$object->_guid}}";
	          this.sColor = "color";
	          this.sColorView = "edit-{{$object->_guid}}_color";
	          this.pop();
	        }
        </script>
				
        {{mb_field object=$object field=color style="background: #`$object->color`;" readonly="readonly"}}
        <button type="button" class="search notext" onclick="ColorSelector.init()">
          {{tr}}Choose{{/tr}}
        </button>
			</td>
    </tr>
    
    <tr>
      <td colspan="2" class="button">
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