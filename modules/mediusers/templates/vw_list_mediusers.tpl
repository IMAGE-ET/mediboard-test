{{assign var=configLDAP value=$conf.admin.LDAP.ldap_connection}}

<table class="tbl">
  <tr>
    <th class="narrow"></th>
    <th>
      {{mb_colonne class="CUser" field="user_username" order_col=$order_col order_way=$order_way function="changeFilter"}}
    </th>
    <th>
      {{mb_colonne class="CUser" field="user_last_name" order_col=$order_col order_way=$order_way function="changeFilter"}}
    </th>
    <th>
      {{mb_colonne class="CUser" field="user_first_name" order_col=$order_col order_way=$order_way function="changeFilter"}}
    </th>
    <th>
      {{mb_colonne class="CMediusers" field="function_id" order_col=$order_col order_way=$order_way function="changeFilter"}}
    </th>
    <th>
      {{mb_colonne class="CUser" field="user_type" order_col=$order_col order_way=$order_way function="changeFilter"}}
    </th>
    <th>{{mb_title class=CUser      field=profile_id}}</th>
    <th>{{mb_title class=CMediusers field=remote}}</th>
    {{if $configLDAP}}
      <th>{{mb_title class="CUser" field="_ldap_linked"}}</th>
    {{/if}}
    <th>
      {{mb_title class=CMediusers field=_user_last_login}}
    </th>
    <th>{{mb_title class="CUser" field="_login_locked"}}</th>
  </tr>

  {{foreach from=$mediusers item=_user}}
  <tr class="{{if $_user->_id == $user_id}}selected{{/if}} {{if !$_user->actif}}hatching{{/if}}">
    <td class="compact">
      <button class="edit notext" onclick="editMediuser('{{$_user->_id}}', this)"></button>
    </td>

    {{if $_user->_ref_user->_id}}
     <td class="text">
       <span onmouseover="ObjectTooltip.createEx(this,'{{$_user->_guid}}')" class="mediuser" style="border-left-color: #{{$_user->_color}};">
         {{mb_value object=$_user field=_user_username}}
       </span>
     </td>
     
     <td class="text">
         {{mb_value object=$_user field=_user_last_name}}
     </td>
     
     <td class="text">
         {{mb_value object=$_user field=_user_first_name}}
     </td>
    
     <td class="text" style="text-align: center">
       {{mb_ditto name=function_view value=$_user->_ref_function->_view}}
     </td>

     <td class="text" style="text-align: center">
       {{assign var=type value=$_user->_user_type}}
       {{assign var=_type value="CUser"|static:"types"}}
       {{mb_ditto name=type_name value=$_type.$type}}
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
     <td class="narrow">
       {{mb_include module=admin template=unlock _user=$_user->_ref_user}}
     </td>
     {{else}}
       <td colspan="10" class="text">
         <div class="small-warning">
           Pas d'utilisateur <em>core</em> pour 
           <a class="action" href="?m=mediusers&tab=vw_idx_mediusers&user_id={{$_user->_id}}">ce Mediuser</a>.
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