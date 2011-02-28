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
  {{if $subject->_coded}}
  <tr>
    <td colspan="10">
      <div class="small-info">
       La cotation des actes est terminée.<br />
       Pour pouvoir coder des actes, veuillez dévalider la cotation.
       </div>
     </td>
  </tr> 
  {{/if}}
  
  {{if !($can->edit || $modif_operation)}}
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

  <!-- Gestion des codes -->
  {{if !$subject->_coded}}
  <tr>
    <td class="text">
      {{include file="../../dPsalleOp/templates/inc_manage_codes.tpl"}}
    </td>
  </tr>
  {{/if}}

  <tr>
    <td class="text">
      <!-- Codage des actes -->
      {{if $subject->_coded}}
        {{mb_include module=dPsalleOp template=inc_possible_actes_ccam}}
      {{else}}
        {{if $can->edit || $modif_operation}}
          {{mb_include module=dPsalleOp template=inc_edit_actes_ccam}}
        {{else}}
          {{mb_include module=dPsalleOp template=inc_possible_actes_ccam}}
        {{/if}}
      {{/if}}
    </td>
  </tr>
  {{if $conf.dPsalleOp.CActeCCAM.envoi_actes_salle}}
  <tr>
    <td>
      {{if !$subject->_nb_echange_hprim || $m == "dPpmsi"}}
      <button class="tick" onclick="ActesCCAM.exportHPRIM({{$subject->_id}}, 'op')">Export des actes au PMSI</button>
      {{/if}}
      {{if $subject->_nb_echange_hprim}}
      <div class="small-success">
        Export déjà effectué {{$subject->_nb_echange_hprim}} fois
      </div>
      {{else}}
      <div class="small-info">
        Pas d'export effectué
      </div>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td class="text">
    </td>
  </tr>
  <tr>
    <td class="text" id="hprim_export_op{{$subject->_id}}">
    </td>
  </tr>
  {{/if}}

  {{if ($module == "dPsalleOp" || $module == "dPhospi") && $conf.dPsalleOp.CActeCCAM.signature}}
  <!-- Signature des actes -->
  <tr>
	  <td class="button">
	    <button class="tick" onclick="signerActes('{{$subject->_id}}', '{{$subject->_class_name}}')">
	      Signer les actes
	    </button>
	  </td>
  </tr>
  {{/if}}
  
</table>