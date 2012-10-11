{{foreach from=$ex_objects item=_ex_objects key=_ex_class_id}}
  {{assign var=_ex_class value=$ex_classes.$_ex_class_id}}
  
  {{if $_ex_objects|@count}}
  {{if !$ex_class_id}}
    <h3 style="margin: 0.5em 1em;">{{$_ex_class->name}}</h3>
  {{/if}}
  
  {{foreach from=$_ex_objects item=_ex_object name=_ex_object}}
     <table class="layout">
       <tr>
         <td>
           <button class="edit notext compact" 
                   onclick="ExObject.edit('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}', '@ExObject.refreshSelf.{{$self_guid}}')">
             {{tr}}Edit{{/tr}}
           </button>
           
           <button class="search notext compact" 
                   onclick="ExObject.display('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
             {{tr}}Display{{/tr}}
           </button>
          
           <button class="history notext compact" 
                   onclick="ExObject.history('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}')">
             {{tr}}History{{/tr}}
           </button>
           
           <button class="print notext compact" 
                   onclick="ExObject.print('{{$_ex_object->_id}}', '{{$_ex_object->_ex_class_id}}', '{{$_ex_object->_ref_object->_guid}}')">
             {{tr}}Print{{/tr}}
           </button>
         </td>
         <td class="text compact">
           <strong style="color: #000;">{{mb_value object=$_ex_object->_ref_first_log field=date}}</strong>
           <br />
           {{$_ex_object->_ref_object}}
         </td>
       </tr>
     </table>
  {{/foreach}}
    
  <hr style="border-color: #aaa;" />
  {{/if}}
    
{{foreachelse}}
  <div class="empty">Aucun formulaire</div>
{{/foreach}}