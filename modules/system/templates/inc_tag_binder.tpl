<tr>
  <th>
    <label for="_bind_tag_view">Tags</label>
  </th>
  <td style="white-space: normal;">
    <button style="float: right;" class="tag-edit" type="button" onclick="Tag.manage('{{$object->_class_name}}')">
      Gérer les tags
    </button>
    
    <ul class="tags">
      {{foreach from=$object->_ref_tag_items item=_item name=tag_items}}
        <li data-tag_item_id="{{$_item->_id}}" style="background-color: #{{$_item->_ref_tag->color}}">
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
            url.addParam("where[object_class]", "{{$object->_class_name}}");
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