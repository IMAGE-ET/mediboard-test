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
  if(document.selPraticienLine){
    changePraticienMed(document.selPraticienLine.praticien_id.value);
  }
  
  Prescription.refreshTabHeader("div_medicament","{{$prescription->_counts_by_chapitre.med}}","{{if $prescription->object_id}}{{$prescription->_counts_by_chapitre_non_signee.med}}{{else}}0{{/if}}");
  
  if(document.forms.addLine && document.forms.searchProd){
    var oFormProduit = document.forms.searchProd;
    
    // Autocomplete des medicaments
    var urlAuto = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
    urlAuto.addParam("produit_max", 40);
    window.ac = urlAuto.autoComplete("searchProd_produit", "produit_auto_complete", {
      minChars: 3,
      updateElement: updateFieldsMedicament,
      callback: 
        function(input, queryString){
          return (queryString + "&inLivret="+($V(oFormProduit._recherche_livret)?'1':'0')); 
        }
    } );
  }
	
	{{if $app->user_prefs.easy_mode}}
	  toggleSearchOptions('searchProd', 'med');
	{{/if}}
	
} );

{{if $prescription->_praticiens|@count}}
  if(document.selPratForPresc){
		var praticiens = {{$prescription->_praticiens|smarty:nodefaults|escape:"htmlall"|@json}};
		var chps = document.selPratForPresc.selPraticien;
		chps.innerHTML = "";
		chps.insert('<option value="">Tous</option>');
		for(var prat in praticiens){
		  chps.insert('<option value='+prat+'>'+praticiens[prat]+'</option>');
		}
		var praticien_sortie_id = {{$praticien_sortie_id|json}};
		
		$A(chps).each( function(option) {
		  option.selected = option.value==praticien_sortie_id;
	  });
  }
{{/if}}  

transfertLineTP = function(line_id, sejour_id){
  var oForm = getForm("transfert_line_TP");
  var oFormDate = document.forms.selDateLine;
  
	$V(oForm.prescription_line_medicament_id, line_id);
  
  if(oFormDate){
    if(oFormDate.debut && oFormDate.debut.value){
      $V(oForm.debut, oFormDate.debut.value);  
    }
    if(oFormDate.time_debut && oFormDate.time_debut.value){
      $V(oForm.time_debut, oFormDate.time_debut.value);
    }
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
  <input type="hidden" name="debut" value="{{$today}}" />
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
  
	{{if ($is_praticien || $mode_protocole || @$operation_id || $can->admin || $mode_pharma || ($current_user->isInfirmiere() && $dPconfig.dPprescription.CPrescription.droits_infirmiers_med))}}  
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
  	<td>
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
		
    {{if ($is_praticien || $mode_protocole || @$operation_id || $can->admin || $mode_pharma || ($current_user->isInfirmiere() && $dPconfig.dPprescription.CPrescription.droits_infirmiers_med))}}  
    
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
			    <br />
					</span>
			    <input type="text" name="produit" value="" size="20" style="font-weight: bold; font-size: 1.3em; width: 300px;" class="autocomplete" 
					       onclick="headerPrescriptionTabs.setActiveTab('div_ajout_lignes');" />
			    <label title="Recherche dans le livret thérapeutique">
			      <input type="checkbox" value="1" name="_recherche_livret" {{if $prescription->type=="sejour" && $dPconfig.dPprescription.CPrescription.preselect_livret}}checked="checked"{{/if}} onchange="if($V(this.form.produit)) { ac.activate.bind(ac)() };" />
			      Livret Thérap.
			    </label>
			    
			    <div style="display:none; width: 350px;" class="autocomplete" id="produit_auto_complete"></div>
			    <button id="searchButton-med" type="button" class="search" onclick="MedSelector.init('produit'); headerPrescriptionTabs.setActiveTab('div_ajout_lignes');">Rechercher</button>
			    <input type="hidden" name="code_cip" onchange="Prescription.addLine(this.value);"/>
			
			    <script type="text/javascript">
			      MedSelector.init = function(onglet){
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
	    <form name="addLineCommentMed" method="post" action="" onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ Prescription.reload('{{$prescription->_id}}',null,'medicament')} } )">
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
    <th colspan="6" class="title">Médicaments</th>
  </tr>
  <tr>
    <th style="width: 5%;">Alertes</th> 
    <th style="width: 25%">Produit</th>
    <th style="width: 37%;">Posologie</th>
    <th style="width: 8%">Praticien</th>
		{{if $prescription->object_id}}
	    <th style="width: 15%">Début</th>
	    <th style="width: 10%">Durée</th>
		{{else}}
		  <th style="width: 25%">Dates</th>
		{{/if}}
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
	    {{if !$curr_line->child_id}}
	      {{if $full_line_guid == $curr_line->_guid}}
	        {{include file="../../dPprescription/templates/inc_vw_line_medicament.tpl" prescription_reelle=$prescription}} 
	      {{else}}
	         {{include file="../../dPprescription/templates/inc_vw_line_medicament_lite.tpl" prescription_reelle=$prescription}}
	      {{/if}}
	    {{/if}}
    {{/if}}
  {{/foreach}}
   
  {{if $prescription->_ref_prescription_line_mixes}}
	<table class="tbl">
	  <tr>
	    <th colspan="7" class="title">Perfusions</th>
	  </tr>
	  <tr>
	    <th style="width: 8%;">Type</th>
	    <th style="width: 44%;">Médicaments</th> 
	    <th style="width: 8%;">Prat</th>
	    <th style="width: 5%;">Débit</th>
	    <th style="width: 15%;">Voie</th>
			{{if $prescription->object_id}}
		    <th style="width: 10%;">Début</th>
	      <th style="width: 10%;">Durée</th>
			{{else}}
			  <th style="20%">Dates</th>
			{{/if}}
	  </tr>
	</table>
	{{/if}}

  <!-- Parcours des prescription_line_mixes -->
  {{foreach from=$prescription->_ref_prescription_line_mixes item=_prescription_line_mix}}
    {{if !$praticien_sortie_id || ($praticien_sortie_id == $_prescription_line_mix->praticien_id)}}
      {{if $full_line_guid == $_prescription_line_mix->_guid}}
        {{include file="../../dPprescription/templates/inc_vw_prescription_line_mix.tpl" prescription_reelle=$prescription}}
      {{else}}
	       {{include file="../../dPprescription/templates/inc_vw_prescription_line_mix_lite.tpl" prescription_reelle=$prescription}} 
	    {{/if}}
    {{/if}}
  {{/foreach}}

<table class="tbl">
  {{if $prescription->_ref_lines_med_comments.comment|@count}}
  <tr>
    <th colspan="6">Commentaires</th>
  </tr>
  {{/if}}
  <!-- Parcours des commentaires --> 
  {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line_comment}}
    {{if !$praticien_sortie_id || ($praticien_sortie_id == $_line_comment->praticien_id)}}
      {{if $full_line_guid == $_line_comment->_guid}}
        {{include file="../../dPprescription/templates/inc_vw_line_comment_elt.tpl" prescription_reelle=$prescription}}
      {{else}}
        {{include file="../../dPprescription/templates/inc_vw_line_comment_readonly.tpl" prescription_reelle=$prescription}}
      {{/if}}
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
      {{if $_hist_line->_class_name == "CPrescriptionLineMedicament"}}
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
        <td colspan="3">
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