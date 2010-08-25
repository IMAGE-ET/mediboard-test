<script type="text/javascript">
  Main.add(function () {
    Control.Tabs.create('tab_user', true);
  });
  
  function changePagePrimaryUsers(page) {
    $V(getForm('listFilterPrimaryUsers').page_function,page);
  }

</script>

{{if $function->_id}}
<table style="width:100%">
  <tr>
    <td>
      <ul id="tab_user" class="control_tabs">
        <li>
          <a {{if !$total_sec_functions}}class="empty"{{/if}} href="#list-primary-users" id="list-primary-users-title">
            Utilisateurs principaux <small>({{$total_sec_functions}})</small>
          </a>
        </li>
        <li>
          <a {{if !$function->_back.secondary_functions|@count}}class="empty"{{/if}} href="#list-secondary-users" id="list-secondary-users-title">
            Utilisateurs secondaires <small>({{$function->_back.secondary_functions|@count}})</small>
          </a>
        </li>
      </ul>
      
      <hr class="control_tabs" />
      
      <div id="list-primary-users" style="display: none;">
        <table class="tbl">
          <tr>
            <td colspan="6">
              <form name="listFilterPrimaryUsers" action="?m=mediusers" method="get">
                <input type="hidden" name="m" value="mediusers" />
                <input type="hidden" name="tab" value="vw_idx_functions" />
                <input type="hidden" name="page_function" value="{{$page_function}}" onchange="this.form.submit()"/>
                
                {{if $total_sec_functions != 0}}
                  {{mb_include module=system template=inc_pagination total=$total_sec_functions current=$page_function change_page='changePagePrimaryUsers'}}
                {{/if}}
              </form>
            </td>
          </tr>
          <tr>
            <th>{{mb_title class=CUser field=user_username}}</th>
            <th>{{mb_title class=CUser field=user_last_name}}</th>
            <th>{{mb_title class=CUser field=user_first_name}}</th>
            <th>{{mb_title class=CUser field=user_type}}</th>
            <th>{{mb_title class=CUser field=profile_id}}</th>
            <th>{{mb_title class=CUser field=user_last_login}}</th>
          </tr>
          {{foreach from=$primary_users item=_user}}
          <tr>
            {{assign var=user_id value=$_user->_id}}
            {{assign var="href" value="?m=mediusers&tab=vw_idx_mediusers&user_id=$user_id"}}
            <td><a href="{{$href}}">{{$_user->_user_username}}</a></td>
            <td><a href="{{$href}}">{{$_user->_user_last_name}}</a></td>
            <td><a href="{{$href}}">{{$_user->_user_first_name}}</a></td>
            <td>
              {{assign var=type value=$_user->_user_type}}
              {{if array_key_exists($type, $utypes)}}{{$utypes.$type}}{{/if}}
            </td>
            <td>{{$_user->_ref_profile->user_username}}</td>
            <td>
              {{if $_user->_user_last_login}}
              <label title="{{mb_value object=$_user field=_user_last_login}}">
                {{mb_value object=$_user field=_user_last_login format=relative}}
              </label>
              {{/if}}
            </td>
          </tr>
          {{foreachelse}}
          <tr>
            <td colspan="6">Aucun utilisateur principal</td>
          </tr>
          {{/foreach}}
        </table>
      </div>
      <div id="list-secondary-users" style="display: none;">
        <form name="addSecUser" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_secondary_function_aed" />
        <input type="hidden" name="secondary_function_id" value="" />
        <input type="hidden" name="function_id" value="{{$function->_id}}" />
        <input type="hidden" name="del" value="0" />
        <table class="form">
          <tr>
            <th class="title" colspan="2">
              Ajout d'un utilisateur
            </th>
          </tr>
      
          <tr>
            <th>{{mb_label object=$secondary_function field="user_id"}}</th>
            <td>
              <input type="text" name="user_id" class="notNull" value=""/>
              <input type="hidden" name="object_class" value="CMediusers" />
              <button class="search" type="button" onclick="ObjectSelector.initEdit()">Chercher</button>
              <script type="text/javascript">
               ObjectSelector.initEdit = function(){
                  this.sForm     = "addSecUser";
                  this.sId       = "user_id";
                  this.sClass    = "object_class";  
                  this.onlyclass = "true";
                  this.pop();
                }
              </script>
            </td>
          </tr>
          <tr>
            <td class="button" colspan="2">
              <button class="submit" name="btnFuseAction" type="submit">{{tr}}Save{{/tr}}</button>
            </td>
          </tr>
        </table>
        </form>
        
        <table class="tbl">
          <tr>
            <th>{{mb_title class=CUser field=user_username}}</th>
            <th>{{mb_title class=CUser field=user_last_name}}</th>
            <th>{{mb_title class=CUser field=user_first_name}}</th>
            <th>{{mb_title class=CUser field=user_type}}</th>
            <th>{{mb_title class=CUser field=profile_id}}</th>
            <th>{{mb_title class=CUser field=user_last_login}}</th>
            <th></th>
          </tr>
          {{foreach from=$function->_back.secondary_functions item=curr_function}}
          <tr>
            {{assign var=user_id value=$curr_function->_ref_user->_id}}
            {{assign var="href" value="?m=mediusers&tab=vw_idx_mediusers&user_id=$user_id"}}
            <td><a href="{{$href}}">{{$curr_function->_ref_user->_user_username}}</a></td>
            <td><a href="{{$href}}">{{$curr_function->_ref_user->_user_last_name}}</a></td>
            <td><a href="{{$href}}">{{$curr_function->_ref_user->_user_first_name}}</a></td>
            <td>
              {{assign var=type value=$curr_function->_ref_user->_user_type}}
              {{if array_key_exists($type, $utypes)}}{{$utypes.$type}}{{/if}}
            </td>
            <td>{{$curr_function->_ref_user->_ref_profile->user_username}}</td>
            <td>
              {{if $curr_function->_ref_user->_user_last_login}}
              <label title="{{mb_value object=$curr_function->_ref_user field=_user_last_login}}">
                {{mb_value object=$curr_function->_ref_user field=_user_last_login format=relative}}
              </label>
              {{/if}}
            </td>
            <td class="button">
              <form name="addSecUser" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
              <input type="hidden" name="dosql" value="do_secondary_function_aed" />
              <input type="hidden" name="secondary_function_id" value="{{$curr_function->secondary_function_id}}" />
              <input type="hidden" name="del" value="1" />
              <button class="trash notext" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'utilisateur secondaire',objName:'{{$curr_function->_ref_user->_view|smarty:nodefaults|JSAttribute}}'})">
                {{tr}}Delete{{/tr}}
              </button>
              </form>
            </td>
          </tr>
          {{foreachelse}}
          <tr>
            <td colspan="7">Aucun utilisateur secondaire</td>
          </tr>
          {{/foreach}}
        </table>
      </div>
    </td>
  </tr>
</table>
{{/if}}