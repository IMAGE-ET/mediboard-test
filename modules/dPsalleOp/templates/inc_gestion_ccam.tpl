<table class="form">
  {{if $subject->_coded}}
  <tr>
    <td colspan="6">
      <div class="big-info">
       La cotation des actes est terminée.<br />
       Pour pouvoir coder des actes, veuillez dévalider la consultation.
       </div>
     </td>
  </tr> 
  {{/if}}
  <tr>
    <td class="text">
      <!-- Gestion des codes -->
      {{if $subject->_coded != "1"}}
        {{include file="../../dPsalleOp/templates/inc_manage_codes.tpl"}}
      {{/if}}
    </td>
  </tr>
  <tr>
    <td class="text">
      <!-- Codage des actes -->
      {{if $subject->_coded == "1"}}
        {{include file="../../dPsalleOp/templates/inc_vw_actes.tpl"}}
      {{else}}
        {{include file="../../dPsalleOp/templates/inc_codage_actes.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>