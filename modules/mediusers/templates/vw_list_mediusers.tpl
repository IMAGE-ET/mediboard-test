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
    {{if $configLDAP}}
      <th>{{mb_title class="CUser" field="_ldap_linked"}}</th>
    {{/if}}
    <th>
      {{mb_colonne class="CUser" field="user_last_login" order_col=$order_col order_way=$order_way url="?m=$m&tab=$tab"}}
    </th>
  </tr>

  {{foreach from=$mediusers item=_user}}
  <tr {{if $_user->_id == $user_id}} class="selected" {{/if}}>

    {{if $_user->_ref_user->_id}}

     <td class="text">
       <a href="#{{$_user->_guid}}" onclick="showMediuser('{{$_user->_id}}', this)" class="mediuser" style="border-left-color: #{{$_user->_ref_function->color}};">
         <span onmouseover="ObjectTooltip.createEx(this,'{{$_user->_guid}}', 'identifiers')">
           {{mb_value object=$_user field=_user_username}}
         </span>
       </a>
     </td>
     
		 <td class="text">
       <a href="#{{$_user->_guid}}" onclick="showMediuser('{{$_user->_id}}', this)">
         {{mb_value object=$_user field=_user_last_name}}
       </a>
     </td>
     
		 <td class="text">
       <a href="#{{$_user->_guid}}" onclick="showMediuser('{{$_user->_id}}', this)">
         {{mb_value object=$_user field=_user_first_name}}
       </a>
     </td>
		
     <td class="text" style="text-align: center">
     	{{mb_ditto name=function_view value=$_user->_ref_function->_view}}
		 </td>

     <td class="text" style="text-align: center">
       {{assign var=type value=$_user->_user_type}}
       {{if array_key_exists($type, $utypes)}}
       {{mb_ditto name=type_name value=$utypes.$type}}
			 {{/if}}
     </td>
      
     <td class="text" style="text-align: center">
       {{mb_ditto name=profile_name value=$_user->_ref_profile->user_username}}
		 </td>
     
     <td class="text">{{mb_value object=$_user field=remote}}</td>
     
     {{if $configLDAP}}
     <td>{{mb_value object=$_user->_ref_user field=_ldap_linked}}</td>
     {{/if}}
      
     <td {{if !$_user->actif}} class="cancelled" {{/if}}>
       {{if $_user->_user_last_login}}
       <label title="{{mb_value object=$_user field=_user_last_login}}">
         {{mb_value object=$_user field=_user_last_login format=relative}}
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
   {{foreachelse}}
   <tr>
     <td colspan="10" class="empty">{{tr}}No result{{/tr}}</td>
   </tr>
   {{/foreach}}
 </table>