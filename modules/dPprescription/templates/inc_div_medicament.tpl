{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

// On met à jour les valeurs de praticien_id
Main.add( function(){
  if(document.forms.selPraticienLine){
    changePraticienMed(document.forms.selPraticienLine.praticien_id.value);
  }
  
  Prescription.refreshTabHeader("div_medicament","{{$prescription->_counts_by_chapitre.med}}","{{if $prescription->object_id}}{{$prescription->_counts_by_chapitre_non_signee.med}}{{else}}0{{/if}}");
 
  if(document.forms.addLine && document.forms.searchProd){
    var oFormProduit = getForm("searchProd");
		          
    // Autocomplete des medicaments
    var urlAuto = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
    urlAuto.addParam("produit_max", 40);
    window.ac = urlAuto.autoComplete(oFormProduit.produit, "produit_auto_complete", {
      minChars: 3,
      updateElement: updateFieldsMedicament,
      callback: 
        function(input, queryString){
				  var oFormSelPrat = getForm("selPrat");
					var praticien_id = "";
					var function_id = "";
          var group_id = "";

          // Protocole
					if(oFormSelPrat){
					  praticien_id = $V(oFormSelPrat.praticien_id);
						function_id = $V(oFormSelPrat.function_id);
            group_id = $V(oFormSelPrat.group_id);
          } 
					// Prescription
					else {
					  praticien_id = document.forms.selPraticienLine ? $V(document.forms.selPraticienLine.praticien_id) : '{{$prescription->_ref_current_praticien->_id}}';
          }
          return (queryString + "&inLivret="+($V(oFormProduit._recherche_livret)?'1':'0')+"&praticien_id="+praticien_id+"&function_id="+function_id+"&group_id="+group_id+"&type="+'{{$prescription->type}}'{{if !$mode_pharma}}+"&fast_access='1'"{{/if}});
        }
    } );
  }
	
	{{if $app->user_prefs.easy_mode}}
	  toggleSearchOptions('searchProd', 'med');
	{{/if}}
	
} );

transfertLineTP = function(line_id, sejour_id){
  var oForm = getForm("transfert_line_TP");
  var oFormDate = document.forms.selDateLine;
  
	$V(oForm.prescription_line_medicament_id, line_id);
  
  if(oFormDate){
	  {{if $prescription->type == "sejour"}}
      $V(oForm.debut, '{{$prescription->_ref_object->entree|iso_date}}');
		{{else}}
	    if(oFormDate.debut && oFormDate.debut.value){
	      $V(oForm.debut, oFormDate.debut.value);  
	    }
	    if(oFormDate.time_debut && oFormDate.time_debut.value){
	      $V(oForm.time_debut, oFormDate.time_debut.value);
	    }
		{{/if}}    
    if(oFormDate.jour_decalage && oFormDate.jour_decalage.value){
      $V(oForm.jour_decalage, oFormDate.jour_decalage.value);
    }
    if(oFormDate.decalage_line && oFormDate.decalage_line.value){
      $V(oForm.decalage_line, oFormDate.decalage_line.value);
    }
    if(oFormDate.unite_decalage && oFormDate.unite_decalage.value){
      $V(oForm.unite_decalage, oFormDate.unite_decalage.value);
    }
    if(oFormDate.operation_id && oFormDate.operation_id.value){
      $V(oForm.operation_id, oFormDate.operation_id.value);
    }
  }
		
  submitFormAjax(oForm, 'systemMsg', { onComplete: function(){ 
    Prescription.reload('{{$prescription->_id}}', '', 'medicament','',null, null);
  } } );
}


addAerosol = function(){
  var oFormAerosol = getForm("add_aerosol");
  var oFormDate = getForm("selDateLine");

  if (oFormDate) {
    $V(oFormAerosol.date_debut, $V(oFormDate.debut));
  }
	return onSubmitFormAjax(oFormAerosol);
}

// refresh de l'aerosol en modal lors de sa creation
callbackAerosol = function(aerosol_id){
  Prescription.reloadLine("CPrescriptionLineMix-"+aerosol_id, "{{$mode_protocole}}","{{$mode_pharma}}","{{$operation_id}}");
}

updateModaleAfterAddOxygene = function(line_guid){
  Prescription.reloadLine(line_guid, '{{$mode_protocole}}', '{{$mode_pharma}}', '{{$operation_id}}');
}

updateModaleAfterAddLine = function(line_id){
  if(line_id){
    Prescription.reloadLine("CPrescriptionLineMedicament-"+line_id, '{{$mode_protocole}}', '{{$mode_pharma}}', '{{$operation_id}}');
  }
}


</script>

<form name="transfert_line_TP" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_transfert_line_tp_aed" />
	<input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="prescription_line_medicament_id" value="" />
	<input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
	<!-- Champs permettant de gerer les elements relatifs -->
  <input type="hidden" name="debut" value="" />
  <input type="hidden" name="time_debut" value="" />
  <input type="hidden" name="jour_decalage" value="" />
  <input type="hidden" name="decalage_line" value="" />
  <input type="hidden" name="unite_decalage" value="" />
  <input type="hidden" name="operation_id" value="" />
</form>

<form name="add_aerosol" action="?" method="post">
  <input type="hidden" name="m" value="dPprescription" />
	<input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="prescription_line_mix_id" value="" />
	<input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
	<input type="hidden" name="type_line" value="aerosol" />
	<input type="hidden" name="type" value="nebuliseur_ultrasonique" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
	<input type="hidden" name="unite_duree" value="jour" />
	<input type="hidden" name="unite_duree_passage" value="minute" />
  <input type="hidden" name="date_debut" value="" />
	<input type="hidden" name="callback" value="callbackAerosol" />
</form>

<!-- Cas normal -->
<!-- Formulaire d'ajout de ligne dans la prescription -->
<form action="?m=dPprescription" method="post" name="addLine" onsubmit="return checkForm(this);">  
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
  <input type="hidden" name="prescription_line_medicament_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="object_class" value="{{$prescription->object_class}}" />
  <input type="hidden" name="object_id" value="{{$prescription->object_id}}" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
  <input type="hidden" name="code_cip" value=""/>
  <!-- Date de debut -->
	{{if $prescription->type == "externe"}}
	  <input type="hidden" name="debut" value="" />
  {{else}}
	  <input type="hidden" name="debut" value="{{$today}}" />
  {{/if}}
  <input type="hidden" name="time_debut" value="" />
  <input type="hidden" name="mode_pharma" value="{{$mode_pharma}}" />
  <input type="hidden" name="refresh_pharma" value="{{$refresh_pharma}}" />
  <input type="hidden" name="substitute_for_id" value="" />
  <input type="hidden" name="substitution_active" value="1" />
  {{if $prescription->object_id}}
  <input type="hidden" name="_most_used_poso" value="1" />
  {{/if}}
	<input type="hidden" name="traitement_personnel" value="0" />
	
	<!-- Champs permettant de gerer les elements relatifs -->
	<input type="hidden" name="jour_decalage" value="" />
  <input type="hidden" name="decalage_line" value="" />
  <input type="hidden" name="unite_decalage" value="" />
	<input type="hidden" name="operation_id" value="" />
	
	<input type="hidden" name="callback" value="updateModaleAfterAddLine" />
	
</form>

<table class="form">
{{if $prescription->_score_prescription >= 1}}
  <tr>
    <td colspan="2">
      <div class="{{if $prescription->_score_prescription == '1'}}small-warning{{else}}small-error{{/if}}">
        <strong>
          <span onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-alertes-{{$prescription->_id}}")'>
		        Niveau
		        {{mb_value object=$prescription field=_score_prescription}}
	        <span>
		      {{if $mode_pharma && $prescription->_score_prescription == "2"}}
		      <br />
		      Validation automatique impossible
		      {{/if}}
	      </strong>
      </div>
      <div id="tooltip-content-alertes-{{$prescription->_id}}" style="display: none;">
      <ul>
      {{foreach from=$prescription->_scores key=type item=scores_by_type}}
        {{if is_array($scores_by_type)}}
          {{if $scores_by_type|@count}}
            <li>{{$scores_by_type|@count}} {{tr}}CPrescription._type_alerte.{{$type}}{{/tr}}</li>
          {{/if}}
        {{else}}
          {{if $scores_by_type}}
            <li>{{$scores_by_type}} {{tr}}CPrescription._type_alerte.{{$type}}{{/tr}}</li>
          {{/if}}
        {{/if}}
      {{/foreach}}
      </ul>
      <button class="search" onclick="Prescription.viewFullAlertes('{{$prescription->_id}}');">Afficher le détail des alertes</button>
      </div>      
    </td>
  </tr>
  {{/if}}  	
  </div>


  {{assign var=perm_add_med value=0}}
	{{if (!$prescription->_protocole_locked &&
	     ($is_praticien || $mode_protocole || @$operation_id || $can->admin || $mode_pharma || ($current_user->isExecutantPrescription() && $conf.dPprescription.CPrescription.droits_infirmiers_med && !$conf.dPprescription.CPrescription.role_propre)))}} 
	  {{assign var=perm_add_med value=1}} 		 
  {{/if}}
	
	{{if $perm_add_med}} 
  <tr>
    <th class="title">
    	{{if $app->user_prefs.easy_mode}}
      <button type="button" class="add notext" onclick="toggleSearchOptions('searchProd','med');" style="float: left">Détails</button>
			{{/if}}
			Nouvelle ligne de prescription - Médicaments
		</th>
  </tr>
  {{/if}}
  
	<tr>
  	<td class="text">
  	 <!-- Ne pas donner la possibilite de signer les lignes d'un protocole -->
    {{if $prescription->object_id && ($is_praticien || ($mode_pharma && $prescription->_score_prescription != "2"))}}
    <button class="tick" type="button" onclick="submitValideAllLines('{{$prescription->_id}}', 'medicament', '{{$mode_pharma}}');" style="float: right">
      {{if $mode_pharma}}
        Valider toutes les lignes
      {{else}}
        Signer les lignes de médicaments
      {{/if}}
    </button>
    {{/if}}
		
		{{if $prescription->type == "sejour" && $prescription->object_id && !$prescription->_ref_object->sortie_reelle}}
			{{if $hide_old_lines}}
	      <button type="button" class="search" style="float: right;" onclick="refreshElementPrescription('medicament', true, '0')">Afficher les prescriptions terminées ({{$hidden_lines_count}})</button>
	    {{else}}
	      <button type="button" class="search" style="float: right;" onclick="refreshElementPrescription('medicament', true, '1')">Masquer les prescriptions terminées</button>
	    {{/if}}
		{{/if}}
		
    {{if $perm_add_med}}
			<!-- Affichage des div des medicaments et autres produits -->
			  <form action="?" method="get" name="searchProd" onsubmit="return false;">
					<!-- Affichage des produits les plus utilises -->
					<select name="favoris" onchange="Prescription.addLine(this.value); this.value = '';" style="width: 120px;" onclick="updateFavoris('{{$favoris_praticien_id}}','med', this); headerPrescriptionTabs.setActiveTab('div_ajout_lignes');">
				 	  <option value="">&mdash; les plus utilisés</option>
					</select>
					
					{{if $prescription->object_class == "CSejour" && $prescription->object_id}}
					<select name="tp" onchange="transfertLineTP(this.value, '{{$prescription->_ref_object->_id}}'); this.value = '';" style="width: 12em;" onclick="updateSelectTP('{{$prescription->_ref_object->patient_id}}', this); headerPrescriptionTabs.setActiveTab('div_ajout_lignes');">
						<option value="">&mdash; Traitements perso</option>
					</select>
					{{/if}}
					
					<span id="addComment-med">
			    <button  class="new" onclick="toggleFieldComment(this, $('add_line_comment_med'),'commentaire');" type="button">Ajouter commentaire</button>
			    <button  class="new" onclick="addAerosol();" type="button">Aérosol</button>
          <br />
					</span>
			    <input type="text" name="produit" value="&mdash; {{tr}}CPrescription.select_produit{{/tr}}" size="20" style="font-weight: bold; font-size: 1.3em; width: 300px;" class="autocomplete" 
					       onclick="this.value = ''; headerPrescriptionTabs.setActiveTab('div_ajout_lignes');" />
			    <label title="Recherche dans le livret thérapeutique">
			      <input type="checkbox" value="1" name="_recherche_livret"
            {{if ($prescription->type=="sejour" && $conf.dPprescription.CPrescription.preselect_livret) ||
              ($prescription->type=="externe" && $app->user_prefs.lt_checked_externe)}}
                checked="checked"
            {{/if}}
              onchange="if($V(this.form.produit)) { ac.activate.bind(ac)() };" />
			      Livret Thérap.
			    </label>
			    
			    <div style="display:none; width: 350px;" class="autocomplete" id="produit_auto_complete"></div>
			    <button id="searchButton-med" type="button" class="search" onclick="MedSelector.initsearch('produit'); headerPrescriptionTabs.setActiveTab('div_ajout_lignes');">Rechercher</button>
			    <input type="hidden" name="code_cip" onchange="Prescription.addLine(this.value);"/>
			
			    <script type="text/javascript">
			      MedSelector.initsearch = function(onglet){
			        this.sForm = "searchProd";
			        this.sView = "produit";
			        this.sCode = "code_cip";
			        this.sRechercheLivret = document.searchProd._recherche_livret.checked;
			        this.sSearch = document.searchProd.produit.value;
			        this.sOnglet = onglet;
			        this.selfClose = false;
			        this.pop();
			      }
			  </script>
			  </form>
    {{/if}}
	 </td>
  </tr>
  <tbody id="add_line_comment_med" style="display: none">
  <tr>
	  <th colspan="2" class="category">{{mb_title class=CPrescriptionLineComment field=commentaire}}</th>
	</tr>
	<tr>
	  <td colspan="2" style="text-align:center;"> 		 
		  <div>
	    <form name="addLineCommentMed" method="post" action="" onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ Prescription.reload('{{$prescription->_id}}',null,'medicament','{{$mode_protocole}}','{{$mode_pharma}}')} } )">
	      <input type="hidden" name="m" value="dPprescription" />
	      <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="prescription_line_comment_id" value="" />
	      <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
	      <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
	      <input type="hidden" name="chapitre" value="medicament" />
	      <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
	      {{mb_field class=CPrescriptionLineComment field=commentaire}}
	      <button class="submit" type="button" onclick="this.form.onsubmit();">Ajouter ce commentaire</button>
	    </form>
	    </div>
	</td>
  </tr>
  </tbody>
</table>

{{if $prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.comment}}
<table class="tbl">
  <tr>
    <th colspan="7" class="title">Médicaments</th>
  </tr>
  <tr>
    <th style="width: 5%;" class="narrow">&nbsp;</th> 
    <th style="width: 25%">Produit</th>
    <th style="width: 40%;">Posologie</th>
		{{if $prescription->object_id}}
	    <th style="width: 10%">Début</th>
	    <th style="width: 10%">Durée</th>
		{{else}}
		  <th style="width: 20%">Dates</th>
		{{/if}}
		<th style="width: 10%">Praticien</th>
  </tr>
</table>
{{/if}}

<!--  div permettant de ranger les lignes -->
<div id="med"></div>
<div id="med_art"></div>

{{if $prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.comment || $prescription->_ref_prescription_line_mixes}}

  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
    {{if !$praticien_sortie_id || ($praticien_sortie_id == $curr_line->praticien_id)}}
      <!-- Si la ligne ne possede pas d'enfant -->
	    {{include file="../../dPprescription/templates/inc_vw_line_medicament_lite.tpl"}}
    {{/if}}
  {{/foreach}}
  
	 {{foreach from=$prescription->_ref_prescription_line_mixes_by_type key=type_line item=_lines_mix_by_type}}
	   {{if $_lines_mix_by_type|@count}}
				<table class="tbl">
				  <tr>
				    <th colspan="7" class="title">{{tr}}CPrescription._chapitres.{{$type_line}}{{/tr}}</th>
				  </tr>
				  <tr>
				    <th style="width: 5%;" class="narrow"></th>
				    <th style="width: 45%;">Médicaments</th> 
				    
				    {{if $type_line == "aerosol"}}
  						<th style="width: 8%;">Fréquence</th>
	            <th style="width: 12%;">Interface</th>
	          {{else}}
		          <th style="width: 5%;">Débit</th>
		          <th style="width: 15%;">Type / Voie</th>					
						{{/if}}
	          {{if $prescription->object_id}}
					    <th style="width: 10%;">Début</th>
				      <th style="width: 10%;">Durée</th>
						{{else}}
						  <th style="20%">Dates</th>
						{{/if}}
						<th style="width: 10%;">Prat</th>
				  </tr>
				</table>
				
			  <!-- Parcours des prescription_line_mixes -->
			  {{foreach from=$_lines_mix_by_type item=_prescription_line_mix}}
			    {{if !$praticien_sortie_id || ($praticien_sortie_id == $_prescription_line_mix->praticien_id)}}
				     {{include file="../../dPprescription/templates/inc_vw_line_mix_lite.tpl"}} 
			    {{/if}}
			  {{/foreach}}
			{{/if}}
		{{/foreach}}
 

		<table class="tbl">
		  {{if $prescription->_ref_lines_med_comments.comment|@count}}
		  <tr>
		    <th colspan="7" class="title">Commentaires</th>
		  </tr>
		  {{/if}}
		  <!-- Parcours des commentaires --> 
		  {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line_comment}}
		    {{if !$praticien_sortie_id || ($praticien_sortie_id == $_line_comment->praticien_id)}}
		      {{include file="../../dPprescription/templates/inc_vw_line_comment_lite.tpl"}}
		    {{/if}}
		  {{/foreach}}
		 </table> 
		{{else}}
		  <div class="small-info"> 
		     Il n'y a aucun médicament dans cette prescription.
		  </div>
		{{/if}}
		
		{{if $prescription->object_id}}
		<!-- Affichage de l'historique des prescriptions precedentes -->
		<table class="tbl">
		{{foreach from=$historique key=type_prescription item=hist_prescription}}
		 {{if $hist_prescription->_ref_lines_med_comments.med|@count || $hist_prescription->_ref_lines_med_comments.comment|@count}}
		  <tr>
		    <th colspan="5" class="title">Historique {{tr}}CPrescription.type.{{$type_prescription}}{{/tr}}</th>
		  </tr>
		  {{/if}}
		  {{foreach from=$hist_prescription->_ref_lines_med_comments item=_type_hist_line}}
		    {{foreach from=$_type_hist_line item=_hist_line}}
		    <tr>
		      <!-- Affichage d'une ligne de medicament -->
		      {{if $_hist_line->_class == "CPrescriptionLineMedicament"}}
		        <td><a href="#1" onmouseover="ObjectTooltip.createEx(this, '{{$_hist_line->_guid}}')">{{$_hist_line->_view}}</a></td>
		        {{if !$_hist_line->fin}}
		          <td>
		            {{mb_label object=$_hist_line field="debut"}}: {{mb_value object=$_hist_line field="debut"}}
		          </td>
		          <td>
		            {{mb_label object=$_hist_line field="duree"}}: 
		              {{if $_hist_line->duree && $_hist_line->unite_duree}}
		                {{mb_value object=$_hist_line field="duree"}}  
		                {{mb_value object=$_hist_line field="unite_duree"}}
		              {{else}}
		              -
		              {{/if}}
		          </td>
		          <td>
		            {{mb_label object=$_hist_line field="_fin"}}: {{mb_value object=$_hist_line field="_fin"}}
		          </td>
		          {{else}}
		          <td colspan="3">
		            {{mb_label object=$_hist_line field="fin"}}: {{mb_value object=$_hist_line field="fin"}}
		          </td>
		        {{/if}}
		        <td>
		          Praticien: {{$_hist_line->_ref_praticien->_view}}
		        </td>
		      {{else}}
		      <!-- Affichage d'une ligne de commentaire -->
		        <td colspan="3" class="text">
		           {{$_hist_line->commentaire}}
		         </td>
		         <td>
		           {{mb_label object=$_hist_line field="ald"}}:
		          {{if $_hist_line->ald}}
		            Oui
		          {{else}}
		            Non
		          {{/if}}
		         </td>
		         <td>
		            Praticien: {{$_hist_line->_ref_praticien->_view}}
		         </td>
		      {{/if}}
		    </tr>
		    {{/foreach}}
		  {{/foreach}}
		{{/foreach}}
		</table>
{{/if}}