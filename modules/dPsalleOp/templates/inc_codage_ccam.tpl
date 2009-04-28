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
    <th class="category">Codage des actes CCAM</th>
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

  {{if ($module == "dPsalleOp" || $module == "dPhospi") && $dPconfig.dPsalleOp.CActeCCAM.signature}}
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