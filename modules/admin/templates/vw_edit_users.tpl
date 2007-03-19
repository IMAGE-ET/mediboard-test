<!-- $Id: vw_idx_patients.tpl 699 2006-09-01 10:37:10Z rhum1 $ -->

<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="index.php?m={{$m}}&amp;user_id=0">
        Créer un nouvel utilisateur
      </a>
      {{include file="inc_list_users.tpl"}}
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      {{include file="inc_edit_user.tpl"}}
      {{else}}
      {{include file="inc_vw_user.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>