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
  if(document.selPraticienLine){
	  changePraticienElt(document.selPraticienLine.praticien_id.value, '{{$element}}');
  }
  Prescription.refreshTabHeader('div_{{$element}}','{{$prescription->_counts_by_chapitre.$element}}','{{if $prescription->object_id}}{{$prescription->_counts_by_chapitre_non_signee.$element}}{{else}}0{{/if}}');
  if(document.search{{$element}}){
    var url = new Url("dPprescription", "httpreq_do_element_autocomplete");
    url.addParam("category", "{{$element}}");
    url.autoComplete("search{{$element}}_libelle", "{{$element}}_auto_complete", {
      minChars: 2,
      updateElement: function(element) { updateFieldsElement(element, 'search{{$element}}', '{{$element}}') }
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

<table class="form">
	{{assign var=variable_droits value="droits_infirmiers_$element"}}

	{{if ($is_praticien || $mode_protocole || @$operation_id || $can->admin || ($current_user->isInfirmiere() && $dPconfig.dPprescription.CPrescription.$variable_droits))}}
	  {{assign var=perm_add_line value=1}}
	{{else}}
    {{assign var=perm_add_line value=0}}  
	{{/if}}

  <tr>
    {{if $perm_add_line}}
      <th class="title">Nouvelle ligne</th>
    {{/if}}
    <th class="title" style="width: 1%;">Affichage</th>
  </tr>
  <tr>
    {{if $perm_add_line}}
    <td>
      <!-- Formulaire d'elements les plus utilisés -->
			<form action="?" method="get" name="search{{$element}}" onsubmit="return false;">
			  <!-- Affichage des produits les plus utilises -->
        <select name="favoris" onchange="Prescription.addLineElement(this.value,'{{$element}}'); this.value = '';" 
				        style="width: 140px;" onclick="updateFavoris('{{$favoris_praticien_id}}','{{$element}}', this); headerPrescriptionTabs.setActiveTab('div_ajout_lignes');">
          <option value="">&mdash; les plus utilisés</option>
        </select>
				
			  <!-- Boutons d'ajout d'elements et de commentaires -->
			  {{if $dPconfig.dPprescription.CPrescription.add_element_category}}
			  <button class="new" onclick="toggleFieldComment(this, $('add_{{$element}}'), 'élément'); headerPrescriptionTabs.setActiveTab('div_ajout_lignes');">Ajouter élément</button>
			  {{/if}}
			  <button class="new" onclick="toggleFieldComment(this, $('add_line_comment_{{$element}}'), 'commentaire');" type="button">Ajouter commentaire</button>
			 <br />
			 
			  <!-- Selecteur d'elements -->
			  <input type="text" name="libelle" value="" class="autocomplete" onclick="headerPrescriptionTabs.setActiveTab('div_ajout_lignes');" />
			  <input type="hidden" name="element_id" onchange="Prescription.addLineElement(this.value,'{{$element}}');" />
			  <div style="display:none;" class="autocomplete" id="{{$element}}_auto_complete"></div>
			  <button class="search" type="button" onclick="ElementSelector.init{{$element}}('{{$element}}')">Rechercher</button>
			  <script type="text/javascript">   
			    ElementSelector.init{{$element}} = function(type){
			      this.sForm = "search{{$element}}";
			      this.sLibelle = "libelle";
			      this.sElement_id = "element_id";
			      this.sType = type;
			      this.selfClose = false;
			      this.pop();
			    }
			  </script>
			</form>
    </td>
		{{/if}}
    <td>
		  {{if is_array($prescription->_ref_lines_elements_comments) && array_key_exists($element, $prescription->_ref_lines_elements_comments)}}
      {{if $readonly}}
	        <button class="lock" type="button" onclick="Prescription.reload('{{$prescription->_id}}', '', '{{$element}}', '', '{{$mode_pharma}}', null, true, {{if $lite}}false{{else}}true{{/if}});">
	          {{if $lite}}Vue complète
	          {{else}}Vue simplifiée
	          {{/if}}
	        </button>
      {{else}}
			  <button class="lock" type="button" onclick="Prescription.reload('{{$prescription->_id}}', '', '{{$element}}', '', '{{$mode_pharma}}', null, true, {{if $app->user_prefs.mode_readonly}}false{{else}}true{{/if}},'');");">
			    Lecture seule
			  </button>
		  {{/if}}
			{{/if}}
		  

			   
		  <!-- Ne pas donner la possibilite de signer les lignes d'un protocole -->
		  {{if $prescription->object_id && $is_praticien}}
		  <button class="tick" type="button" onclick="submitValideAllLines('{{$prescription->_id}}', '{{$element}}');">
		  	Signer les lignes "{{tr}}CCategoryPrescription.chapitre.{{$element}}{{/tr}}"
		  </button>
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
				{{if $dPconfig.dPprescription.CPrescription.add_element_category}}
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
				      {{mb_field class="CPrescriptionLineComment" field="commentaire"}}
				      <br />
				      <div style="text-align: center;">
					      <button class="submit" type="button" 
					              onclick="if(document.selPraticienLine){
					                         this.form.praticien_id.value = document.selPraticienLine.praticien_id.value;
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
{{if $lite && is_array($prescription->_ref_lines_elements_comments) && array_key_exists($element, $prescription->_ref_lines_elements_comments) && $readonly}}
 <table class="tbl">
   <th style="width:22%;">Libellé</th>
   <th style="width:35%;">Prises</th>
   <th style="width:8%;">Prat.</th>
	 {{if $prescription->object_id}}
	   <th style="width:15%;">Début</th>
	   <th style="width:10%;">Durée</th>
	 {{else}}
	   <th style="width: 25%">Dates</th>
	 {{/if}}
   <th style="width:10%;">Exécutant</th>
 </table>
{{/if}}
	  
{{if is_array($prescription->_ref_lines_elements_comments) && array_key_exists($element, $prescription->_ref_lines_elements_comments)}}

  {{assign var=lines value=$prescription->_ref_lines_elements_comments.$element}}
  {{assign var=nb_lines value=0}}
  
  <!-- Parcours des elements de type $element -->
  {{foreach from=$lines item=lines_cat key=category_id}}
	  {{assign var=category value=$categories.$element.$category_id}}
	
        <table class="tbl">
        <tr>
          <th class="title" colspan="9">{{$category->_view}}</th>
        </tr>
      </table>	  

      
	  <!-- Div permettant de classer les elements suivantes la date d'arret -->
	  <div id="elt_{{$category->_id}}"></div>
	  <div id="elt_art_{{$category->_id}}"></div>
    
    {{foreach from=$lines_cat.element item=line_element}}
	    {{if !$praticien_sortie_id || ($praticien_sortie_id == $line_element->praticien_id)}}
	      {{if $readonly}}
	        {{if $full_line_guid == $line_element->_guid}}
	          {{include file="inc_vw_line_element_elt.tpl" _line_element=$line_element prescription_reelle=$prescription nodebug=true}}
	        {{else}}
		        {{if $lite}}
		          {{include file="inc_vw_line_element_lite.tpl" _line_element=$line_element prescription_reelle=$prescription nodebug=true}}
		        {{else}}
	            {{include file="inc_vw_line_element_readonly.tpl" _line_element=$line_element prescription_reelle=$prescription nodebug=true}}
	          {{/if}}
          {{/if}}
        {{else}}
          {{include file="inc_vw_line_element_elt.tpl" _line_element=$line_element prescription_reelle=$prescription nodebug=true}}
	      {{/if}}
      {{/if}}
	  {{/foreach}}
	  
	  <!-- Commentaires d'une categorie -->
	  <table class="tbl">
  	  {{if $lines_cat.comment|@count}}
  	  {{if !$lite}}
  	  <tr>
  	    <th colspan="9" class="element">Commentaires</th>
  	  </tr>
  	  {{/if}}
  	  {{/if}}
  	  {{foreach from=$lines_cat.comment item=line_comment}}
  	    {{if !$praticien_sortie_id || ($praticien_sortie_id == $line_comment->praticien_id)}}
          {{if $readonly}}
            {{if $full_line_guid == $line_comment->_guid}}
              {{include file="inc_vw_line_comment_elt.tpl" _line_comment=$line_comment prescription_reelle=$prescription nodebug=true}}            
            {{else}}
	              {{include file="inc_vw_line_comment_readonly.tpl" _line_comment=$line_comment prescription_reelle=$prescription nodebug=true}}
	          {{/if}}
          {{else}}
            {{include file="inc_vw_line_comment_elt.tpl" _line_comment=$line_comment prescription_reelle=$prescription nodebug=true}}
  	      {{/if}}
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
			        {{if $_line->_class_name == "CPrescriptionLineComment"}}
			          <td colspan="4">{{$_line->commentaire}}</td>
			        {{else}}
			          {{assign var=chapitre value=$_line->_ref_element_prescription->_ref_category_prescription->chapitre}}
			          <!-- Affichage d'une ligne d'element -->
					      <td {{if $chapitre == "dmi"}}colspan="4"{{/if}}>
					      	<a href="#1" onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}')">{{$_line->_view}}</a>
					      </td>
					      
					      {{if $chapitre != "dmi"}}
						      {{if !$_line->fin}}
							    <td>{{mb_label object=$_line field="debut"}}: {{mb_value object=$_line field="debut"}}</td>
							    {{if $chapitre != "anapath" && $chapitre != "imagerie" && $chapitre != "consult"}}
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
							    {{/if}}
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