<script type="text/javascript">
  signerActes = function(object_id, object_class){
    var url = new Url("dPsalleOp", "vw_signature_actes");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("dialog", 1);
    url.requestModal(700, 400, 'Signature des actes');
  }
  
  clotureActivite = function(object_id, object_class) {
    var url = new Url("dPsalleOp", "ajax_cloture_activite");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.requestModal(500, 300);
  }
</script>

<table class="form">
  {{if $subject->_coded && $m != "dPpmsi"}}
    {{if $subject->_class == "CConsultation"}}
    <tr>
      <td colspan="10">
        <div class="small-info">
         La cotation des actes est terminée.<br />
         Pour pouvoir coder des actes, veuillez dévalider la cotation.
         </div>
       </td>
    </tr> 
    {{else}}
    <tr>
      <td colspan="10" class="text">
        <div class="small-info">
          Les actes ne peuvent plus être modifiés pour la raison suivante : {{tr}}config-dPsalleOp-COperation-modif_actes-{{$conf.dPsalleOp.COperation.modif_actes}}{{/tr}}
          <br />
          Veuillez contacter le PMSI pour toute modification.
        </div>
      </td>
    </tr>
    {{/if}}
  {{/if}}
  {{if !$subject->_canRead}}
    <tr>
      <td colspan="10" class="text">
        <div class="small-info">Vous n'avez pas les droits nécessaires pour coder les actes</div>
      </td>
    </tr>
  {{else}}
    <!-- Gestion des codes -->
    {{if $can->admin || $m == "dPpmsi" || (!$subject->_coded && $subject->_canRead)}}
    <tr>
      <td class="text">
        {{mb_include module=salleOp template=inc_manage_codes}}
      </td>
    </tr>
    {{/if}}

    <tr>
      <td class="text">
        <!-- Codage des actes -->
        {{if $subject->_coded && !$can->admin && $m != "dPpmsi"}}
          {{mb_include module=salleOp template=inc_possible_actes_ccam}}
        {{else}}
          {{mb_include module=salleOp template=inc_edit_actes_ccam}}
        {{/if}}
      </td>
    </tr>
  {{/if}}
</table>