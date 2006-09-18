<table class="main">
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th class="title">Utilisateur : {{$user->_view}}</th>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      {{include file="inc_perms_modules.tpl"}}
    </td>
    <td class="halfPane">
      {{include file="inc_perms_objects.tpl"}}
    </td>
  </tr>
</table>