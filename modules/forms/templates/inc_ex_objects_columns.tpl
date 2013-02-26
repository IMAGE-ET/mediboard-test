{{assign var=_title_col value=10}}

{{assign var=_ex_obj value=$_ex_objects|@reset}}

{{if !$ex_class_id || $search_mode}}
  <h3>{{$ex_classes.$_ex_class_id->name}}</h3>
{{/if}}

{{if $_ex_objects|@count}}
  <table class="main tbl" style="width: 1%;">
    <!-- First line -->
    <thead>
      <tr style="font-size: 0.9em;">
        <th class="narrow"></th>
        <th {{if !$print}} style="min-width: 20em;" {{/if}}>Champ</th>
        
        {{foreach from=$_ex_objects item=_ex_object name=_ex_object}}
          <th class="narrow text">
            {{mb_value object=$_ex_object->_ref_first_log field=date}}
            
            {{if !$print}}
             <hr />
             
             <div style="white-space: nowrap;">
               {{if !$search_mode}}
                 <button class="edit notext compact" title="{{tr}}Edit{{/tr}}"
                         onclick="ExObject.edit('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}', '{{$target_element}}')">
                 </button>
               {{/if}}
               
               <button class="search notext compact" title="{{tr}}Display{{/tr}}"
                       onclick="ExObject.display('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
               </button>
              
               <button class="history notext compact" title="{{tr}}History{{/tr}}"
                       onclick="ExObject.history('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}')">
               </button>
               
               <button class="print notext compact" title="{{tr}}Print{{/tr}}"
                       onclick="ExObject.print('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
               </button>
             </div>
             {{/if}}
          </th>
          
          {{if $smarty.foreach._ex_object.iteration%$_title_col==0}}
            <th class="narrow"></th>
            <th>Champ</th>
          {{/if}}
        {{/foreach}}
      </tr>
    </thead>
    
    {{foreach from=$_ex_obj->_ref_ex_class->_ref_groups item=_ex_group name=_ex_group}}
      {{if $_ex_group->_ref_fields|@count}}
      
      <tbody class="data-row">
        <tr style="border-top: 2px solid #333;"> 
          <th rowspan="{{math equation="x+1" x=$_ex_group->_ref_fields|@count}}" class="ex_group">
            {{vertical}}{{$_ex_group}}{{/vertical}}
          </th>
          <td colspan="{{$_title_col+1}}" style="padding: 0;"></td>
          
          {{foreach from=$_ex_objects item=_ex_object name=_ex_object}}
            {{if $smarty.foreach._ex_object.iteration%$_title_col==0}}
              <th rowspan="{{math equation="x+1" x=$_ex_group->_ref_fields|@count}}" class="ex_group">
                {{vertical}}{{$_ex_group}}{{/vertical}}
              </th>
              <td colspan="{{$_title_col+1}}" style="padding: 0;"></td>
            {{/if}}
          {{/foreach}}
        </tr>
        
        {{foreach from=$_ex_group->_ref_fields item=_ex_field name=_ex_field}}
          {{assign var=field_name value=$_ex_field->name}}
          
          <tr class="field {{if $_ex_field->_empty}} empty {{/if}}">
            <th class="text section" style="font-size: 0.9em; min-width: 12em;">
              {{mb_label object=$_ex_obj field=$field_name}}
            </th>
            
            {{foreach from=$_ex_objects item=_ex_object2 name=_ex_object2}}
              <td style="{{if $_ex_object2->$field_name === null || $_ex_object2->$field_name === ""}} color: #aaa; {{/if}} {{if $_ex_field->formula}} font-weight: bold; {{/if}}"
              class="text value {{if $_ex_object2->_specs.$field_name instanceof CTextSpec}} compact {{/if}}">
                {{mb_value object=$_ex_object2 field=$field_name}} 
              </td>
              
              {{if $smarty.foreach._ex_object2.iteration%$_title_col==0}}
                <th class="text section" style="font-size: 0.9em; min-width: 12em;">
                  {{mb_label object=$_ex_obj field=$field_name}}
                </th>
              {{/if}}
            {{/foreach}}
          </tr>
        {{/foreach}}
      </tbody>
      
      {{if !$smarty.foreach._ex_group.last}}
        <tr style="font-size: 0.9em;">
          <th class="narrow"></th>
          <th>Champ</th>
          
          {{foreach from=$_ex_objects item=_ex_object name=_ex_object}}
            <th class="narrow text">
              {{mb_value object=$_ex_object->_ref_first_log field=date}}
            </th>
            {{if $smarty.foreach._ex_object.iteration%$_title_col==0}}
              <th></th>
              <th></th>
            {{/if}}
          {{/foreach}}
        </tr>
      {{/if}}
      
      {{/if}}
    {{/foreach}}
  </table>
{{else}}
  <em>{{tr}}CExClass.none{{/tr}}</em>
{{/if}}
