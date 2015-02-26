<div style="display: none" id="affectation_{{$sejour->_guid}}">
  <table class="tbl">
    {{foreach from=$sejour->_ref_users_by_type item=_users key=type}}
      <tr>
        <th>{{tr}}CUserSejour.{{$type}}{{/tr}}</th>
      </tr>
      {{foreach from=$_users item=_user}}
        <tr>
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_user->_ref_user}}</td>
        </tr>
        {{foreachelse}}
        <tr>
          <td class="empty">{{tr}}CUserSejour.none{{/tr}}</td>
        </tr>
      {{/foreach}}
    {{/foreach}}
  </table>
</div>