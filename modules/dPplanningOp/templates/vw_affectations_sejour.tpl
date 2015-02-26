{{if !$refresh}}
  <script>
    addUser = function(form, sejour_id) {
      return onSubmitFormAjax(form, function() {
        var url = new Url("planningOp", "vw_affectations_sejour");
        url.addParam("sejour_id", sejour_id);
        url.addParam("refresh", 1);
        url.requestUpdate('refresh_'+sejour_id);
      });
    }

    delUser = function(form, sejour_id) {
      var options = {
        typeName:'l\'association',
        ajax: 1
      };
      var ajax = {
        onComplete: function() {
          var url = new Url("planningOp", "vw_affectations_sejour");
          url.addParam("sejour_id", sejour_id);
          url.addParam("refresh", 1);
          url.requestUpdate('refresh_'+sejour_id);
        }
      };
      confirmDeletion(form, options, ajax);
    }
  </script>
{{/if}}

<table class="tbl" id="refresh_{{$sejour->_id}}">
  <tr>
    <th class="title" colspan="2">Affectation de personnel pour:<br/>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">{{$sejour->_view}}</span>
    </th>
  </tr>
  {{foreach from=$sejour->_ref_users_by_type item=_users key=type}}
    <tr>
      <th colspan="2">{{tr}}CUserSejour.{{$type}}{{/tr}}</th>
    </tr>
    <tr>
      <td>
        <form name="selUser-{{$type}}" action="?" method="post">
          <input type="hidden" name="@class" value="CUserSejour" />
          {{mb_key   object=$user_sejour}}
          {{mb_class object=$user_sejour}}
          <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />

          <select name="user_id" onchange="addUser(this.form, '{{$sejour->_id}}');" style="width:135px">
            <option value="">{{tr}}Choose{{/tr}}</option>
            {{mb_include module=mediusers template=inc_options_mediuser list=$users.$type}}
          </select>
        </form>
      </td>

      <td {{if !$_users|@count}}class="empty"{{/if}}>
      {{foreach from=$_users item=_user_sejour}}
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_user_sejour->_ref_user}}

        <form name="delUser-{{$type}}-{{$_user_sejour->_id}}" action="?" method="post">
          <input type="hidden" name="@class" value="CUserSejour" />
          {{mb_key   object=$_user_sejour}}
          {{mb_class object=$_user_sejour}}
          <input type="hidden" name="del" value="1" />
          <button type="button" class="cancel notext" onclick="delUser(this.form, '{{$sejour->_id}}');">{{tr}}Delete{{/tr}}</button>
        </form>
        <br/>
      {{foreachelse}}
        {{tr}}CUserSejour.none{{/tr}}
      {{/foreach}}
      </td>
    </tr>
  {{/foreach}}
</table>