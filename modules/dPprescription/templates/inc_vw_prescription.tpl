<script type="text/javascript">

preselectType = function(contexte, oForm){
  {{if $contexteType}}
	  var contexteType = {{$contexteType|@json}};
	  var types = contexteType[contexte];
	  $V(oForm.type, types[0]);
	  $A(oForm.type).each( function(input) {
	    input.disabled = !types.include(input.value);
	  });
  {{/if}}
}

Main.add(function () {
  // Preparation du formulaire de creation de protocole
  var form = document.forms.addProtocolePresc;
  if(form){
    prepareForm(form);
    preselectType("CSejour", form);
  }
});

</script>

<!-- Formulaire de creation du protocole -->
{{if !$prescription->_id && $mode_protocole && !$mode_pharma}}
  {{if $function_id || $praticien_id || $group_id}}
		<form action="?m=dPprescription" method="post" name="addProtocolePresc" onsubmit="return Protocole.add();">	
		   <input type="hidden" name="m" value="dPprescription" />
		   <input type="hidden" name="dosql" value="do_prescription_aed" />
		   <input type="hidden" name="prescription_id" value="" />
		   <input type="hidden" name="del" value="0" />
		   <input type="hidden" name="object_class" value=""/>
		   <input type="hidden" name="object_id" value=""/>
		   <input type="hidden" name="praticien_id" value="" />
		   <input type="hidden" name="function_id" value="" />
		   <input type="hidden" name="group_id" value="" />
		   <input type="hidden" name="callback" value="Prescription.reloadAddProt" />
		   <table class="form">
		     <tr>
		       <th class="category" colspan="2">
		         Création d'un protocole
		       </th>
		    </tr>
		    <tr>
		      <th>{{mb_label object=$protocole field="libelle"}}</th>
		      <td>{{mb_field object=$protocole field="libelle" class="notNull"}}</td>
		    </tr>
		    <tr>
		      <th>{{mb_label object=$protocole field="object_class"}}</th>
					<td>{{mb_field object=$protocole field="object_class" onchange="preselectType(this.value, this.form)"}}</td>
			  </tr>
			  <tr>
			    <th>{{mb_label object=$protocole field="type"}}</th>
			    <td>
			      <select name="type">
			        <option value="pre_admission">Pré-admission</option>
			        <option value="sejour">Séjour</option>
			        <option value="sortie">Sortie</option>
			        <option value="externe">Externe</option>
			      </select>  
			    </td>
			  </tr>
			  <tr>
			   <td colspan="2" style="text-align: center">
					  <button type="button" onclick="this.form.onsubmit();" class="new">Créer un protocole</button>
			   </td>  
			  </tr>
		  </table>
		</form>
  {{else}}
    <div class="big-info">
      <ul>
	      <li>Pour <strong>créer un protocole</strong>, veuillez sélectionner un praticien, un cabinet ou un établissement puis cliquez sur "Créer un protocole".</li>
	      <li>Pour <strong>visualiser un protocole</strong>, veuillez sélectionner un protocole dans la liste des protocoles.</li>
      </ul>
    </div>
  {{/if}}
{{/if}}


{{if !$prescription->_id && $mode_pharma}}
  <div class="big-info">
    Veuillez sélectionner un séjour pour pouvoir accéder à sa prescription.
  </div>
{{/if}}


{{if $prescription->_id || $mode_pack}}
{{if !$mode_pack}}
  <!-- Affichage de l'entete de la prescription -->
  {{include file="../../dPprescription/templates/inc_header_prescription.tpl"}}	
{{/if}}
	<!-- Affichage des elements de la prescription -->
	<div id="produits_elements">
	  {{include file="../../dPprescription/templates/inc_vw_produits_elements.tpl"}}  
	</div>
{{/if}}