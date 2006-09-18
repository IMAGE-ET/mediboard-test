<!-- $Id: vw_idx_patients.tpl 699 2006-09-01 10:37:10Z rhum1 $ -->

<table class="main">
  <tr>
    <td class="halfPane">
      {{include file="inc_list_users.tpl"}}
    </td>

    {{if $user->user_id}}
    <td class="halfPane">
      {{if $canEdit}}
      {{include file="inc_edit_user.tpl"}}
      {{else}}
      {{include file="inc_vw_user.tpl"}}
      {{/if}}
    </td>
    {{/if}}
  </tr>
</table>