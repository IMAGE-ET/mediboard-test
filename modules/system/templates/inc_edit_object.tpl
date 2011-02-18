<button type="button" class="new" onclick="MbObject.edit('{{$object->_class_name}}-0')">
	{{tr}}{{$object->_class_name}}-title-create{{/tr}}
</button>

<form name="edit-{{$object->_guid}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  {{mb_class object=$object}}
  {{mb_key object=$object}}
	
	<input type="hidden" name="callback" value="MbObject.editCallback" />
	
	<table class="main form">
		<col class="narrow" />
		
		{{mb_include module=system template=inc_form_table_header}}
		
		{{if $object->_id}}
		<tr>
			<th>
				Tags
			</th>
			<td style="white-space: normal;">
				<ul class="tags">
					{{foreach from=$object->_ref_tag_items item=_item name=tag_items}}
					  <li data-tag_item_id="{{$_item->_id}}" style="background-color: {{$_item->_ref_tag->color}}">
					  	{{$_item}} 
							<button type="button" class="delete" 
							        onclick="Tag.removeItem($(this).up('li').getAttribute('data-tag_item_id'), MbObject.edit.curry('{{$object->_guid}}'))">
							</button>
						</li>
					{{/foreach}}
					<li class="input">
						<input type="text" name="_bind_tag_view" class="autocomplete" size="15" />
						<script type="text/javascript">
							Main.add(function(){
                var form = getForm("edit-{{$object->_guid}}");
                var element = form._bind_tag_view;
                var url = new Url("system", "ajax_seek_autocomplete");
                
                url.addParam("object_class", "CTag");
                url.addParam("input_field", element);
                url.autoComplete(element, null, {
                  minChars: 3,
                  method: "get",
                  select: "view",
                  dropdown: true,
                  afterUpdateElement: function(field,selected){
									  var id = selected.getAttribute("id").split("-")[2];
                    Tag.bindTag("{{$object->_guid}}", id, MbObject.edit.curry("{{$object->_guid}}"));
                    if ($V(element) == "") {
                      $V(element, selected.down('.view').innerHTML);
                    }
                  }
                });
							});
						</script>
					</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td colspan="2"><hr /></td>
		</tr>
    {{/if}}
		
		{{foreach from=$object->_specs item=_spec key=_field}}
		  {{if ($_field.0 !== "_") && ($_field != $object->_spec->key) && ($_spec->show != "0") || ($_field.0 === "_" && $_spec->show == 1)}}
		  <tr>
	      <th>{{mb_label object=$object field=$_field}}</th>
	      <td>{{mb_field object=$object field=$_field}}</td>
		  </tr>
			{{/if}}
		{{/foreach}}
		
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