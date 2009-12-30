<table class="tbl">
  <tr>
    <th>
      {{mb_colonne class="CUser" field="user_username" order_col=$order_col order_way=$order_way url="?m=$m&tab=$tab"}}
    </th>
    <th>
      {{mb_colonne class="CUser" field="user_last_name" order_col=$order_col order_way=$order_way url="?m=$m&tab=$tab"}}
    </th>
    <th>
      {{mb_colonne class="CUser" field="user_first_name" order_col=$order_col order_way=$order_way url="?m=$m&tab=$tab"}}
    </th>
    <th>
      {{mb_colonne class="CMediusers" field="function_id" order_col=$order_col order_way=$order_way url="?m=$m&tab=$tab"}}
    </th>
    <th>
      {{mb_colonne class="CUser" field="user_type" order_col=$order_col order_way=$order_way url="?m=$m&tab=$tab"}}
    </th>
    <th>{{mb_title class=CUser      field=profile_id}}</th>
    <th>{{mb_title class=CMediusers field=remote}}</th>
    <th>
      {{mb_colonne class="CUser" field="user_last_login" order_col=$order_col order_way=$order_way url="?m=$m&tab=$tab"}}
    </th>
  </tr>

  {{foreach from=$mediusers item=curr_user}}
  <tr {{if $curr_user->_id == $object->_id}}class="selected"{{/if}}>

    {{assign var=user_id value=$curr_user->_id}}
    {{if $curr_user->_ref_user->_id}}

     <td class="text"><a href="#" onclick="showMediuser('{{$user_id}}')" class="mediuser" style="border-left-color: #{{$curr_user->_ref_function->color}};">{{mb_value object=$curr_user field=_user_username}}</a></td>
     <td class="text"><a href="#" onclick="showMediuser('{{$user_id}}')">{{mb_value object=$curr_user field=_user_last_name}}</a></td>
     <td class="text"><a href="#" onclick="showMediuser('{{$user_id}}')">{{mb_value object=$curr_user field=_user_first_name}}</a></td>
     <td  class="text" style="text-align: center">
     	{{mb_ditto name=function_name value=$curr_user->_ref_function->_view}}
		 </td>

     <td class="text">
       {{assign var=type value=$curr_user->_user_type}}
       {{if array_key_exists($type, $utypes)}}{{$utypes.$type}}{{/if}}
     </td>
      
     <td class="text">{{$curr_user->_ref_profile->user_username}}</td>
     
     <td class="text">{{mb_value object=$curr_user field=remote}}</td>
      
     <td class="{{if !$curr_user->actif}}cancelled{{/if}}">
       {{if $curr_user->_user_last_login}}
       <label title="{{mb_value object=$curr_user field=_user_last_login}}">
         {{mb_value object=$curr_user field=_user_last_login format=relative}}
       </label>
       {{/if}}
     </td>

     {{else}}
     <td colspan="10" class="text">
       <div class="small-warning">
         Pas d'utilisateur <em>core</em> pour 
         <a class="action" href="{{$href}}">ce Mediuser</a>.
       </div>
     </td>
     {{/if}}
     
   </tr>
   {{/foreach}}
 </table>