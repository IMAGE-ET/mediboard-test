{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">      

// On vide toutes les valeurs du formulaire d'ajout d'element
var oForm = document.addLineElement;
oForm.prescription_line_element_id.value = "";
oForm.del.value = "0";
oForm.element_prescription_id.value = "";

// On met à jour les valeurs de praticien_id
Main.add( function(){
  if(getForm("selPraticienLine")){
	  changePraticienElt(getForm("selPraticienLine").praticien_id.value, '{{$element}}');
  }
  Prescription.refreshTabHeader('div_{{$element}}','{{$prescription->_counts_by_chapitre.$element}}','{{if $prescription->object_id}}{{$prescription->_counts_by_chapitre_non_signee.$element}}{{else}}0{{/if}}');

  var form = getForm("search{{$element}}");
  if(form){
    var url = new Url("dPprescription", "httpreq_do_element_autocomplete");
    url.addParam("category", "{{$element}}");
		{{if !$is_praticien && !$operation_id}}
		url.addParam("user_id", $V(getForm("addLineElement").praticien_id));
		{{/if}}
    url.autoComplete(form.libelle, "{{$element}}_auto_complete", {
      minChars: 2,
      updateElement: function(element) { updateFieldsElement(element, form, '{{$element}}') }
    } );
  }
	
	{{if $app->user_prefs.easy_mode}}
    toggleSearchOptions('search{{$element}}', '{{$element}}');
  {{/if}}
} );

</script>

<table class="form">
	{{assign var=variable_droits value="droits_infirmiers_$element"}}

  {{assign var=perm_add_line value=0}}  
	{{if $is_praticien || $mode_protocole || @$operation_id || $can->admin || ($current_user->isExecutantPrescription() && $conf.dPprescription.CPrescription.$variable_droits)}}
	  {{assign var=perm_add_line value=1}}
	{{/if}}

  {{if !$prescription->_protocole_locked && $perm_add_line}}
  <tr>
    <th class="title">
    	{{if $app->user_prefs.easy_mode}}
      <button type="button" class="add notext" onclick="toggleSearchOptions('search{{$element}}','{{$element}}');" style="float: left">Détails</button>
      {{/if}}
			Nouvelle ligne de prescription - {{tr}}CCategoryPrescription.chapitre.{{$element}}{{/tr}}
		</th>
  </tr>
  {{/if}}
  
	<tr>
  	<td>
			<!-- Ne pas donner la possibilite de signer les lignes d'un protocole -->
      {{if $prescription->object_id && $is_praticien}}
      <button class="tick" type="button" onclick="submitValideAllLines('{{$prescription->_id}}', '{{$element}}');" style="float: right;">
        Signer les lignes "{{tr}}CCategoryPrescription.chapitre.{{$element}}{{/tr}}"
      </button>
      {{/if}}
 
      {{if $prescription->type == "sejour" && $prescription->object_id && !$prescription->_ref_object->sortie_reelle}}
        {{if $hide_old_lines}}
          <button type="button" class="search" style="float: right;" onclick="refreshElementPrescription('{{$element}}', true, '0')">Afficher les prescriptions terminées ({{$hidden_lines_count}})</button>
        {{else}}
          <button type="button" class="search" style="float: right;" onclick="refreshElementPrescription('{{$element}}', true, '1')">Masquer les prescriptions terminées</button>
        {{/if}}
		 {{/if}}
     {{if !$prescription->_protocole_locked && $perm_add_line}}
      <!-- Formulaire d'elements les plus utilisés -->
			<form action="?" method="get" name="search{{$element}}" onsubmit="return false;">
			  <!-- Affichage des produits les plus utilises -->
				{{if $is_praticien || $mode_protocole || @$operation_id || $can->admin || ($current_user->isExecutantPrescription() && !$conf.dPprescription.CPrescription.role_propre)}}
				  <button type="button" class="add" onclick="Prescription.showFavoris('{{$favoris_praticien_id}}','{{$element}}','{{$prescription->_id}}','{{$mode_protocole}}','{{$mode_pharma}}');">Les plus utilisés</button>
				{{/if}}
			  
				{{if $is_praticien || $mode_protocole || @$operation_id || $can->admin || ($current_user->isExecutantPrescription() && !$conf.dPprescription.CPrescription.role_propre)}}
				<!-- Boutons d'ajout d'elements et de commentaires -->
				<span id="addComment-{{$element}}">
				  {{if $conf.dPprescription.CPrescription.add_element_category}}
				  <button class="new" onclick="toggleFieldComment(this, $('add_{{$element}}'), 'élément'); headerPrescriptionTabs.setActiveTab('div_ajout_lignes');">Ajouter élément</button>
				  {{/if}}
				  <button class="new" onclick="toggleFieldComment(this, $('add_line_comment_{{$element}}'), 'commentaire');" type="button">Ajouter commentaire</button>
				  <br />
			  </span>
			  {{/if}}
				
				<!-- Selecteur d'elements -->
			  <input type="text" name="libelle" value="&mdash; {{tr}}CPrescription.select_element{{/tr}}" class="autocomplete"
               onclick="this.value = ''; headerPrescriptionTabs.setActiveTab('div_ajout_lignes');" style="font-weight: bold; font-size: 1.3em; width: 300px;"/>
			  <input type="hidden" name="element_id" onchange="Prescription.addLineElement(this.value,'{{$element}}');" />
			  <div style="display:none;" class="autocomplete" id="{{$element}}_auto_complete"></div>
			  <button id="searchButton-{{$element}}" class="search" type="button" onclick="ElementSelector.init{{$element}}('{{$element}}')">Rechercher</button>
			  <script type="text/javascript">   
			    ElementSelector.init{{$element}} = function(type){
			      this.sForm = "search{{$element}}";
			      this.sLibelle = "libelle";
			      this.sElement_id = "element_id";
			      this.sType = type;
			      this.selfClose = false;
						{{if !$is_praticien && !$operation_id}}
				      this.sUserId = $V(getForm("addLineElement").praticien_id);
				    {{/if}}
			      this.pop();
			    }
			  </script>
			</form>
   
		{{/if}}
   </td>
  </tr>
  {{if $perm_add_line}}
  <tbody id="add_{{$element}}" style="display: none">
    <tr>
      <th colspan="2" class="category">
        Ajout d'un element dans la nomenclature et dans la prescription
      </th>
    </tr>
		<tr>
	    <td colspan="2">
		    <!-- Div d'ajout d'element dans la prescription (et dans la nomenclature) -->
				{{if $conf.dPprescription.CPrescription.add_element_category}}
					<div>
					  {{if !$categories.$element|@count}}
					    <div class="small-info">
					      Impossible de rajouter des éléments de prescription car cette section ne possède pas de catégorie
					    </div>
					  {{else}}
					    <form name="add{{$element}}" method="post" action="" onsubmit="document.addLineElement._chapitre.value='{{$element}}'; return onSubmitFormAjax(this);">
					      <input type="hidden" name="m" value="dPprescription" />
					      <input type="hidden" name="dosql" value="do_element_prescription_aed" />
					      <input type="hidden" name="del" value="0" />
					      <input type="hidden" name="element_prescription_id" value="" />
					      <input type="hidden" name="callback" value="Prescription.addLineElement" />
					      <select name="category_prescription_id">
					      {{foreach from=$categories.$element item=cat}}
					        <option value="{{$cat->_id}}">{{$cat->_view}}</option>
					      {{/foreach}}
					      </select>
					      <input name="libelle" type="text" size="80" />
					      <button class="submit notext" type="button" 
					              onclick="this.form.onsubmit()">Ajouter</button>
				      </form>
					  {{/if}}
					</div>
				{{/if}}
		</td>
		</tr>
		</tbody>
		<tbody id="add_line_comment_{{$element}}" style="display: none;">
		<tr>
      <th colspan="2" class="category">
        Ajout d'un commentaire
      </th>
    </tr>
		<tr>
		  <td colspan="2">
				<div>
				  {{if !$categories.$element|@count}}
				    <div class="small-info">
				      Impossible de rajouter des commentaires car cette section ne possède pas de catégorie
				    </div>
				  {{else}}
				    <form name="addLineComment{{$element}}" method="post" action="" 
				          onsubmit="return Prescription.onSubmitCommentaire(this,'{{$prescription->_id}}','{{$element}}');">
				      <input type="hidden" name="m" value="dPprescription" />
				      <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
				      <input type="hidden" name="del" value="0" />
				      <input type="hidden" name="prescription_line_comment_id" value="" />
				      <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
				      <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
				      <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
				      <select name="category_prescription_id">
				        {{foreach from=$categories.$element item=cat}}
				        <option value="{{$cat->_id}}">{{$cat->_view}}</option>
				        {{/foreach}}
				      </select>
				      <br />
				      {{mb_field class="CPrescriptionLineComment" field="commentaire" form="addLineComment`$element`"
                aidesaisie="resetSearchField: 0, validateOnBlur: 0, strict: 0"}}
				      <br />
				      <div style="text-align: center;">
					      <button class="submit" type="button" 
					              onclick="if(document.forms.selPraticienLine){
					                         this.form.praticien_id.value = document.forms.selPraticienLine.praticien_id.value;
					                       }                        
					                       this.form.onsubmit();">Ajouter</button>
				      </div>
				    </form>
				  {{/if}}
				</div>
      </td>
	  </tr>
  {{/if}}
</table>

<!-- Formulaire d'ajout de ligne d'elements et de commentaires -->
{{if is_array($prescription->_ref_lines_elements_comments) && array_key_exists($element, $prescription->_ref_lines_elements_comments)}}
 <table class="tbl">
   <th style="width:25%;">Libellé</th>
   <th style="width:35%;">
	   {{if $conf.dPprescription.CCategoryPrescription.$element.unite_prise}}
		   {{$conf.dPprescription.CCategoryPrescription.$element.unite_prise}}
		 {{else}}
		  Prises
		 {{/if}}
	</th>
   
	 {{if $prescription->object_id}}
	   <th style="width:10%;">Début</th>
	   <th style="width:10%;">Durée</th>
	 {{else}}
	   <th style="width: 20%">Dates</th>
	 {{/if}}
   <th style="width:10%;">Exécutant</th>
	 <th style="width:10%;">Prat.</th>
 </table>
{{/if}}
	  
{{if is_array($prescription->_ref_lines_elements_comments) && array_key_exists($element, $prescription->_ref_lines_elements_comments)}}

  {{assign var=lines value=$prescription->_ref_lines_elements_comments.$element}}
  {{assign var=nb_lines value=0}}
  
  <!-- Parcours des elements de type $element -->
  {{foreach from=$lines item=lines_cat key=category_id}}
	  {{assign var=category value=$categories.$element.$category_id}}
		
		{{if $lines_cat.element || $lines_cat.comment}}
      <table class="tbl">
        <tr>
          <th class="title" colspan="9">{{$category->_view}}</th>
        </tr>
      </table>	  
    {{/if}}
	  <!-- Div permettant de classer les elements suivantes la date d'arret -->
	  <div id="elt_{{$category->_id}}"></div>
	  <div id="elt_art_{{$category->_id}}"></div>
    
    {{foreach from=$lines_cat.element item=line_element}}
	    {{if !$praticien_sortie_id || ($praticien_sortie_id == $line_element->praticien_id)}}
        {{include file="inc_vw_line_element_lite.tpl" _line_element=$line_element nodebug=true}}
      {{/if}}
	  {{/foreach}}
	  
	  <!-- Commentaires d'une categorie -->
	  <table class="tbl">
  	  {{foreach from=$lines_cat.comment item=line_comment}}
  	    {{if !$praticien_sortie_id || ($praticien_sortie_id == $line_comment->praticien_id)}}
          {{include file="inc_vw_line_comment_lite.tpl" _line_comment=$line_comment nodebug=true}}
        {{/if}}
  	  {{/foreach}}
	  </table>
	  {{/foreach}}
  {{else}}
  <div class="small-info"> 
     Il n'y a aucun élément de type "{{tr}}CCategoryPrescription.chapitre.{{$element}}{{/tr}}" dans cette prescription.
  </div>
  {{/if}}
  <br />
  
  {{if $prescription->object_id}}
  <!-- Affichage de l'historique -->
  <table class="tbl">
		{{foreach from=$historique key=type_prescription item=hist_prescription}}
		  {{if is_array($hist_prescription->_ref_lines_elements_comments) && array_key_exists($element, $hist_prescription->_ref_lines_elements_comments)}}
	    {{foreach from=$hist_prescription->_ref_lines_elements_comments.$element item=_hist_lines name="foreach_hist_elt"}}
		    {{if $smarty.foreach.foreach_hist_elt.first}}
			    <tr>
			      <th colspan="7" class="title">Historique {{tr}}CPrescription.type.{{$type_prescription}}{{/tr}}</th>
			    </tr>
	      {{/if}}
			  {{foreach from=$_hist_lines item=_hist_line}}
			    {{foreach from=$_hist_line item=_line}}
			      <tr>
			        <!-- Affichage d'une ligne de commentaire -->
			        {{if $_line->_class == "CPrescriptionLineComment"}}
			          <td colspan="4" class="text">{{$_line->commentaire}}</td>
			        {{else}}
			          {{assign var=chapitre value=$_line->_ref_element_prescription->_ref_category_prescription->chapitre}}
			          <!-- Affichage d'une ligne d'element -->
					      <td {{if $chapitre == "dmi"}}colspan="4"{{/if}}>
					      	<a href="#1" onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}')">{{$_line->_view}}</a>
					      </td>
					      
					      {{if $chapitre != "dmi"}}
						      {{if !$_line->fin}}
							    <td>{{mb_label object=$_line field="debut"}}: {{mb_value object=$_line field="debut"}}</td>
							    <td>
							      {{mb_label object=$_line field="duree"}}: 
							        {{if $_line->duree && $_line->unite_duree}}
							          {{mb_value object=$_line field="duree"}}  
							          {{mb_value object=$_line field="unite_duree"}}
							        {{else}}
							        -
							        {{/if}}
							    </td>
							    <td>{{mb_label object=$_line field="_fin"}}: {{mb_value object=$_line field="_fin"}}</td>
							  
							    {{else}}
							    <td colspan="3">
							      {{mb_label object=$_line field="fin"}}: {{mb_value object=$_line field="fin"}}
							    </td>
						      {{/if}}
					      {{/if}}
				      {{/if}}
				        <td>Praticien: {{$_line->_ref_praticien->_view}}</td>
                <td>
				      		{{mb_label object=$_line field="ald"}}:
						      {{if $_line->ald}}Oui{{else}}Non{{/if}}
				        </td>
				        <td> Exécutant: {{if $_line->_ref_executant}}{{$_line->_ref_executant->_view}}{{else}}Aucun{{/if}}</td>
				    </tr>
			    {{/foreach}}
			  {{/foreach}}
			{{/foreach}}
			{{/if}}
		{{/foreach}}
	</table>
{{/if}}