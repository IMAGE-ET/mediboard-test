{{if !$function->_id}}
  {{mb_return}}
{{/if}}

<script>
  Main.add(function () {
    Control.Tabs.create('tab_user', true);
  });
</script>


<ul id="tab_user" class="control_tabs small">
  <li>
    <a {{if !$total_sec_functions}}class="empty"{{/if}} href="#list-primary-users" id="list-primary-users-title">
      Principaux <small>({{$total_sec_functions}})</small>
    </a>
  </li>
  <li>
    <a {{if !$function->_back.secondary_functions|@count}}class="empty"{{/if}} href="#list-secondary-users" id="list-secondary-users-title">
      Secondaires <small>({{$function->_back.secondary_functions|@count}})</small>
    </a>
  </li>
</ul>

<div id="list-primary-users" style="display: none;">
  <table class="tbl">
    <tr>
      <td colspan="6">
        <form name="listFilterPrimaryUsers" action="?" method="get">
          {{mb_include module=system template=inc_pagination total=$total_sec_functions current=$page_function change_page='changePagePrimaryUsers' step=25}}
        </form>
      </td>
    </tr>
    <tr>
      <th>{{mb_title class=CUser field=user_username}}</th>
      <th>{{mb_title class=CUser field=user_last_name}}</th>
      <th>{{mb_title class=CUser field=user_first_name}}</th>
      <th>{{mb_title class=CUser field=user_type}}</th>
      <th>{{mb_title class=CUser field=profile_id}}</th>
      <th>{{mb_title class=CMediusers field=_user_last_login}}</th>
    </tr>
    {{foreach from=$primary_users item=_user}}
    <tr {{if !$_user->actif}}class="hatching"{{/if}}>
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
      <td colspan="6" class="empty">{{tr}}CFunctions-back-users.empty{{/tr}}</td>
    </tr>
    {{/foreach}}
  </table>
</div>

<div id="list-secondary-users" style="display: none;">
  {{if $can->edit}}
  <form name="addSecUser" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: changePagePrimaryUsers})">
  <input type="hidden" name="m" value="{{$m}}" />
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
        <input type="hidden" name="user_id" class="notNull" value=""/>
        <input type="text" name="_view" value="" class="autocomplete"/>
        <script type="text/javascript">
          Main.add(function() {
            var form = getForm("addSecUser");
            var element = form._view;

            var url = new Url("system", "ajax_seek_autocomplete");
            url.addParam("object_class", "CMediusers");
            url.addParam("input_field", element.name);
            url.autoComplete(element, null, {
              minChars: 3,
              method: "get",
              select: "view",
              dropdown: true,
              afterUpdateElement: function(field, selected){
                var id = selected.getAttribute("id").split("-")[2];
                $V(form.user_id, id);
                if ($V(element) == "") {
                  $V(element, selected.down('.view').innerHTML);
                }
              }
            });
          });
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
  {{/if}}

  <table class="tbl">
    <tr>
      <th>{{mb_title class=CUser field=user_username}}</th>
      <th>{{mb_title class=CUser field=user_last_name}}</th>
      <th>{{mb_title class=CUser field=user_first_name}}</th>
      <th>{{mb_title class=CUser field=user_type}}</th>
      <th>{{mb_title class=CUser field=profile_id}}</th>
      <th>{{mb_title class=CUser field=_user_last_login}}</th>
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
        {{if $can->edit}}
        <form name="delSecUser-{{$curr_function->_id}}" action="?" method="post">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="dosql" value="do_secondary_function_aed" />
          <input type="hidden" name="secondary_function_id" value="{{$curr_function->secondary_function_id}}" />
          <input type="hidden" name="del" value="1" />
          <button class="trash notext" type="button"
                  onclick="confirmDeletion(this.form, {
                    typeName: 'l\'utilisateur secondaire',
                    objName: '{{$curr_function->_ref_user->_view|smarty:nodefaults|JSAttribute}}',
                    ajax: true},
                    {onComplete: changePagePrimaryUsers})">
            {{tr}}Delete{{/tr}}
          </button>
        </form>
        {{/if}}
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="7" class="empty">{{tr}}CFunctions-back-secondary_functions.empty{{/tr}}</td>
    </tr>
    {{/foreach}}
  </table>
</div>