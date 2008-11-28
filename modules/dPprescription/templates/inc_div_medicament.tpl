{{if $prescription->object_id}}
  {{assign var=traitements value=$prescription->_ref_object->_ref_prescription_traitement->_ref_prescription_lines}}
{{else}}
  {{assign var=traitements value=""}}
{{/if}}

<script type="text/javascript">

// Initialisation des dates pour les calendars
var date = new Date().toDATE();
var dDate = Date.fromDATE(date); 
dDate.addDays(-1);
date = dDate.toDATE();

dates = {  
  limit: {
    start: date,
    stop: null
  }
}

// On met à jour les valeurs de praticien_id
Main.add( function(){
  if(document.selPraticienLine){
    changePraticienMed(document.selPraticienLine.praticien_id.value);
  }
  
  Prescription.refreshTabHeader("div_medicament","{{$prescription->_counts_by_chapitre.med}}");
  
  if(document.forms.addLine && document.forms.searchProd){
    var oFormProduit = document.forms.searchProd;
  
    // Preparation des formulaire
    prepareForm(document.forms.addLine);
    prepareForm(oFormProduit);
    
    // Autocomplete des medicaments
    urlAuto = new Url();
    urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
    urlAuto.addParam("produit_max", 40);
    
    // callback => methode pour ajouter en post des parametres
    // Faire un mini framework pour rajouter des elements du meme formulaire
    urlAuto.autoComplete("searchProd_produit", "produit_auto_complete", {
      minChars: 3,
      updateElement: updateFieldsMedicament,
      callback: 
        function(input, queryString){
          return (queryString + "&inLivret="+($V(oFormProduit._recherche_livret)?'1':'0')); 
        }
    } );
  }
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


</script>


<form name="transfertToTraitement" action="?" method="post">
  <input type="hidden" name="dosql" value="do_prescription_traitement_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prescription_line_id" value="" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
  <input type="hidden" name="object_id" value="{{$prescription->object_id}}" />
  
  <input type="hidden" name="_traitement" value="1" />
  <input type="hidden" name="_type" value="{{$prescription->type}}" />
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
  {{if $prescription->type=="pre_admission" && $prescription->object_id}}
  <input type="hidden" name="callback" value="transfertTraitement" />
  {{/if}}  
  <input type="hidden" name="substitute_for" value="" />
  <input type="hidden" name="substitution_active" value="1" />
</form>



<table class="form">
  <tr>
    <th class="category">Nouvelle ligne</th>
    <th class="category">Actions</th>
  </tr>
  <tr>
    <td>
    {{if $prescription->_can_add_line}}
			<!-- Affichage des div des medicaments et autres produits -->
			  <form action="?" method="get" name="searchProd" onsubmit="return false;">
			    <select name="favoris" onchange="Prescription.addLine(this.value); this.value = '';" style="width: 170px;">
			      <option value="">&mdash; les plus utilisés</option>
			      <optgroup label="Produits les plus utilisés"></optgroup>
			      {{if array_key_exists("medicament", $listFavoris)}}
			      {{foreach from=$listFavoris.medicament item=curr_prod}}
			      <option value="{{$curr_prod->code_cip}}">
			        {{$curr_prod->libelle}}
			      </option>
			      {{/foreach}}
			      {{/if}}
			      <optgroup label="Injectables les plus utilisés"></optgroup>
			      {{if array_key_exists("injectable", $listFavoris)}}
			      {{foreach from=$listFavoris.injectable item=curr_inj}}
			      <option value="{{$curr_inj->code_cip}}">
			        {{$curr_inj->libelle}}
			      </option>
			      {{/foreach}}
			      {{/if}}
			    </select>
			    <button class="new" onclick="$('add_line_comment_med').show();">Ajouter un commentaire</button>
			    
			    <br />
			    <input type="text" name="produit" value="" size="12" />
			    <input type="checkbox" name="_recherche_livret" {{if $prescription->type=="sejour"}}checked="checked"{{/if}} />
			    Livret Thérap.
			    
			    <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
			    <button type="button" class="search" onclick="MedSelector.init('produit');">Rechercher</button>
			    <input type="hidden" name="code_cip" onchange="Prescription.addLine(this.value);"/>
			
			    <script type="text/javascript">
			      MedSelector.init = function(onglet){
			        this.sForm = "searchProd";
			        this.sView = "produit";
			        this.sCode = "code_cip";
			        this.sRechercheLivret = document.searchProd._recherche_livret.value;
			        this.sSearch = document.searchProd.produit.value;
			        this.sOnglet = onglet;
			        this.selfClose = false;
			        this.pop();
			      }
			  </script>
			  </form>
			  
			  <br />
			  
			  <div id="add_line_comment_med" style="display: none">
			   <button class="cancel notext" type="button" onclick="$('add_line_comment_med').hide();">Cacher</button>
			   <form name="addLineCommentMed" method="post" action="" onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ Prescription.reload('{{$prescription->_id}}',null,'medicament')} } )">
			      <input type="hidden" name="m" value="dPprescription" />
			      <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
			      <input type="hidden" name="del" value="0" />
			      <input type="hidden" name="prescription_line_comment_id" value="" />
			      <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
			      <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
			      <input type="hidden" name="chapitre" value="medicament" />
			      <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
			      <input name="commentaire" type="text" size="98" />
			      <button class="submit notext" type="button" onclick="this.form.onsubmit();">Ajouter</button>
			    </form>
			 </div> 
			{{/if}} 
    </td>

    <td>  
      {{if $prescription->object_id}}
			  <select name="advAction" style="float: right">
			    <option value="">&mdash; Actions spécifiques</option>
			    <option value="stopPerso" onclick="Prescription.stopTraitementPerso(this.parentNode,'{{$prescription->_id}}','{{$mode_pharma}}')">Arret des traitements perso</option>
			    <option value="goPerso" onclick="Prescription.goTraitementPerso(this.parentNode,'{{$prescription->_id}}','{{$mode_pharma}}')">Reprise des traitements perso</option>
			  </select>
			{{/if}}
			{{if $prescription->object_id && ($prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.comment || $traitements || $prescription->_ref_perfusions)}}
			  <button class="{{if $readonly}}edit{{else}}lock{{/if}}" type="button" onclick="Prescription.reload('{{$prescription->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, {{if $readonly}}false{{else}}true{{/if}},{{if $readonly}}false{{else}}true{{/if}});">
			    {{if $readonly}}Modification
			    {{else}}Lecture seule
			    {{/if}}
			  </button>
			  
			  {{if $readonly}}
			  	<button class="lock" type="button" onclick="Prescription.reload('{{$prescription->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, true, {{if $lite}}false{{else}}true{{/if}});">
			    {{if $lite}}Vue complète
			    {{else}}Vue simplifiée
			    {{/if}}
	 		  </button>
			  {{/if}}
			       
		  {{/if}}
		  <br />
		  {{if $mode_pharma}}
		    <strong>
			    {{mb_label object=$prescription field=_score_prescription}} {{mb_value object=$prescription field=_score_prescription}}
		 	  </strong>
		  {{/if}}
		
		  {{if $mode_pharma && $prescription->_score_prescription == "2"}}
		    <strong>Validation auto. impossible</strong>
		  {{/if}}
		  
		  <!-- Ne pas donner la possibilite de signer les lignes d'un protocole -->
		  {{if $prescription->object_id && ($is_praticien || ($mode_pharma && $prescription->_score_prescription != "2"))}}
		  <button class="tick" type="button" onclick="submitValideAllLines('{{$prescription->_id}}', 'medicament', '{{$mode_pharma}}');">
		    {{if $mode_pharma}}
		      Valider toutes les lignes
		    {{else}}
		      Signer les lignes de médicaments
		    {{/if}}
		  </button>
		  {{/if}}
    </td>
  </tr>
</table>

{{if !$mode_pharma && !$prescription->_can_add_line}}
  <div class="big-info">
    L'ajout de lignes dans la prescription est réservé aux praticiens ou aux infirmières 
    entre {{$dPconfig.dPprescription.CPrescription.infirmiere_borne_start}} heures et {{$dPconfig.dPprescription.CPrescription.infirmiere_borne_stop}} heures
  </div>
{{/if}}

<!-- Declaration des tableaux permettant de stocker toutes les lignes -->
{{if $lite && $prescription->_ref_lines_med_comments.med}}
<table class="tbl">

	 
  
  
</table>
  {{/if}}

{{if $lite && ($prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.comment || $traitements) && $readonly}}
<table class="tbl">
  <tr>
    <th colspan="6">Médicaments</th>
  </tr>
  <tr>
    <th style="width: 5%;">Alertes</th> 
    <th style="width: 25%">Produit</th>
    <th style="width: 20%">Praticien</th>
    <th style="width: 15%">Début</th>
    <th style="width: 10%">Durée</th>
    <th style="width: 25%;">Posologie</th>
  </tr>
</table>
{{/if}}


<table class="tbl" id="med">
</table>

<table class="tbl" id="med_art">
</table>

<table class="tbl" id="traitement">
</table>

<table class="tbl" id="traitement_art">
</table>

{{if $lite && $prescription->_ref_perfusions && $readonly}}
<table class="tbl">
  <tr>
    <th colspan="6">Perfusions</th>
  </tr>
  <tr>
    <th style="width: 10%;">Type</th>
    <th style="width: 10%;">Vitesse</th>
    <th style="width: 15%;">Voie</th>
    <th style="width: 10%;">Début</th>
    <th style="width: 10%;">Durée</th>
    <th style="width: 55%;">Médicaments</th> 
  </tr>
</table>
{{/if}}

{{if $prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.comment || $traitements || $prescription->_ref_perfusions}}
<table class="tbl">
  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
    {{if !$praticien_sortie_id || ($praticien_sortie_id == $curr_line->praticien_id)}}
    <!-- Si la ligne ne possede pas d'enfant -->
    {{if !$curr_line->child_id}}
      {{if $readonly}}
        {{if $lite}}
          {{include file="../../dPprescription/templates/inc_vw_line_medicament_lite.tpl" prescription_reelle=$prescription}}
        {{else}}
          {{include file="../../dPprescription/templates/inc_vw_line_medicament_readonly.tpl" prescription_reelle=$prescription}}
        {{/if}}
      {{else}}
        {{include file="../../dPprescription/templates/inc_vw_line_medicament.tpl" prescription_reelle=$prescription}} 
      {{/if}}
    {{/if}}
    {{/if}}
  {{/foreach}}
   
  <!-- Affichage des traitements -->
  {{if $prescription->object_id && $traitements}}
    {{foreach from=$traitements item=traitement}}
      {{if $readonly}}
        {{if $lite}}
           {{include file="../../dPprescription/templates/inc_vw_line_medicament_lite.tpl" curr_line=$traitement prescription=$prescription->_ref_object->_ref_prescription_traitement prescription_reelle=$prescription}}
        {{else}}
          {{include file="../../dPprescription/templates/inc_vw_line_medicament_readonly.tpl" curr_line=$traitement prescription=$prescription->_ref_object->_ref_prescription_traitement prescription_reelle=$prescription}}
        {{/if}}
      {{else}}
        {{include file="../../dPprescription/templates/inc_vw_line_medicament.tpl" curr_line=$traitement prescription=$prescription->_ref_object->_ref_prescription_traitement prescription_reelle=$prescription}}
      {{/if}}
    {{/foreach}}
  {{/if}}
  
  <!-- Parcours des perfusions -->
  {{foreach from=$prescription->_ref_perfusions item=_perfusion}}
    {{if !$praticien_sortie_id || ($praticien_sortie_id == $_perfusion->praticien_id)}}
	    {{if $readonly}}
	      {{if $lite}}
	        {{include file="../../dPprescription/templates/inc_vw_perfusion_lite.tpl" prescription_reelle=$prescription}} 
	      {{else}}
	        {{include file="../../dPprescription/templates/inc_vw_perfusion_readonly.tpl" prescription_reelle=$prescription}}    
	      {{/if}}
	    {{else}}
	      {{include file="../../dPprescription/templates/inc_vw_perfusion.tpl" prescription_reelle=$prescription}}
	    {{/if}}
    {{/if}}
  {{/foreach}}

  {{if $prescription->_ref_lines_med_comments.comment|@count}}
  <tr>
    <th colspan="6">Commentaires</th>
  </tr>
  {{/if}}
  
  <!-- Parcours des commentaires --> 
  {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line_comment}}
    {{if !$praticien_sortie_id || ($praticien_sortie_id == $_line_comment->praticien_id)}}
      {{if $readonly}}
        {{include file="../../dPprescription/templates/inc_vw_line_comment_readonly.tpl" prescription_reelle=$prescription}}
      {{else}}
        {{include file="../../dPprescription/templates/inc_vw_line_comment_elt.tpl" prescription_reelle=$prescription}}
      {{/if}}
    {{/if}}
  {{/foreach}}
  
 </table> 
{{else}}
  <div class="big-info"> 
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
        <td><a href="#" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$_hist_line->_class_name}}', object_id: {{$_hist_line->_id}} } })">{{$_hist_line->_view}}</a></td>
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