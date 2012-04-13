{{foreach from=$ex_objects_by_class item=_ex_objects key=_ex_class_id}}
    {{assign var=_ex_obj value=$_ex_objects|@reset}}
  
    {{if !$ex_class_id}}
      <h3>{{$ex_classes.$_ex_class_id->name}}</h3>
    {{/if}}
    
    {{if $_ex_obj}}
      <table class="main tbl" style="width: 1%;">
        <!-- First line -->
        <thead>
          <tr style="font-size: 0.9em;">
            <th class="narrow"></th>
            <th {{if !$print}} style="min-width: 20em;" {{/if}}>Champ</th>
            {{foreach from=$_ex_objects item=_ex_object name=_ex_object}}
              <th class="narrow {{if $print}}text{{/if}}">
                
                {{mb_value object=$_ex_object->_ref_first_log field=date}}
                
                {{if !$print}}
                 <hr />
                
                 <button style="margin: -1px;" class="edit notext" 
                         onclick="ExObject.edit('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}', '{{$target_element}}')">
                   {{tr}}Edit{{/tr}}
                 </button>
                 
                 <button style="margin: -1px;" class="search notext" 
                         onclick="ExObject.display('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
                   {{tr}}Display{{/tr}}
                 </button>
                
                 <button style="margin: -1px;" class="history notext" 
                         onclick="ExObject.history('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}')">
                   {{tr}}History{{/tr}}
                 </button>
                 
                 <button style="margin: -1px;" class="print notext" 
                         onclick="ExObject.print('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
                   {{tr}}Print{{/tr}}
                 </button>
                 {{/if}}
              </th>
            {{/foreach}}
          </tr>
        </thead>
        
        {{foreach from=$_ex_obj->_ref_ex_class->_ref_groups item=_ex_group name=_ex_group}}
          {{if $_ex_group->_ref_fields|@count}}
          
          <tbody {{if $_ex_group->_empty}} empty {{/if}}>
            <tr style="border-top: 2px solid #333;">
              <th rowspan="{{math equation="x+1" x=$_ex_group->_ref_fields|@count}}">
                {{vertical}}{{$_ex_group}}{{/vertical}}
              </th>
              <td colspan="{{math equation="x+2" x=$_ex_objects|@count}}" style="padding: 1px;"></td>
            </tr>
            
              {{foreach from=$_ex_group->_ref_fields item=_ex_field}}
                {{assign var=field_name value=$_ex_field->name}}
                
                <tr class="field {{if $_ex_field->_empty}} empty {{/if}}">
                  
                  <td class="text label" style="font-weight: bold;">
                    {{mb_label object=$_ex_obj field=$field_name}}</span>
                  </td>
                  
                  {{foreach from=$_ex_objects item=_ex_object2}}
                    <td {{if $_ex_object2->$field_name === null || $_ex_object2->$field_name === ""}} style="color: #aaa;" {{/if}} class="text value {{if $_ex_object2->_specs.$field_name instanceof CTextSpec}} compact {{/if}}">
                      {{mb_value object=$_ex_object2 field=$field_name}} 
                    </td>
                  {{/foreach}}
                </tr>
              {{/foreach}}
          </tbody>
          
          {{/if}}
        {{/foreach}}
      </table>
    {{else}}
      <em>{{tr}}CExClass.none{{/tr}}</em>
    {{/if}}
  
  {{/foreach}}