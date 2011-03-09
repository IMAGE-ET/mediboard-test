<script type="text/javascript">

signerActes = function(object_id, object_class){
  var url = new Url;
  url.setModuleAction("dPsalleOp", "vw_signature_actes");
  url.addParam("object_id", object_id);
  url.addParam("object_class", object_class);
  url.popup(625, 600, 'Signature des actes');
}

</script>

<table class="form">
  {{if $subject->_coded && $m != "dPpmsi"}}
  {{if $subject->_class_name == "CConsultation"}}
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
  {{if (!$can->edit && $subject->_class_name == "CConsultation") || !$can->read}}
  <tr>
    <td colspan="10" class="text">
      <div class="small-info">Vous n'avez pas les droits nescessaires pour coder les actes</div>
    </td>
  </tr>
  {{else}}

  <!-- Gestion des codes -->
  {{if $can->admin || $m == "dPpmsi" || (!$subject->_coded && (($can->edit && ($subject->_class_name == "CConsultation")) || $can->read))}}
  <tr>
    <td class="text">
      {{include file="../../dPsalleOp/templates/inc_manage_codes.tpl"}}
    </td>
  </tr>
  {{/if}}

  <tr>
    <td class="text">
      <!-- Codage des actes -->
      {{if $subject->_coded && !$can->admin && $m != "dPpmsi"}}
        {{mb_include module=dPsalleOp template=inc_possible_actes_ccam}}
      {{else}}
        {{mb_include module=dPsalleOp template=inc_edit_actes_ccam}}
      {{/if}}
    </td>
  </tr>
  {{if $can->admin || $m == "dPpmsi"}}
  <tr>
  {{else}}
  <tr style="display: none;">
  {{/if}}
    <td class="text" id="hprim_export_op{{$subject->_id}}">
      <div class="small-info">
        Fenêtre de résultat de l'envoi HPRIM
      </div>
    </td>
  </tr>
  {{/if}}
  
</table>