{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=typeDate value="mode_grille"}}
{{assign var=line value=$filter_line_element}}
{{assign var=type value="mode_grille"}} 
 
{{include file="../../dPprescription/templates/js_functions.tpl"}}
 
<script type="text/javascript">
// Ajout de tous les elements d'une categorie
function addCategorie(categorie_id, oTokenField){
  // Parcours de tous les boutons
  $$('input.'+categorie_id).each( function(oCheckbox) {
    elt = oCheckbox.id;
    elts = elt.split("-");
    id  = elts[1];
    
    // Si element pas encore selectionné
    if(!oCheckbox.checked){
      oCheckbox.checked = true;
      oTokenField.add(id);
    }
  }); 
}

function resetModeEasy(){
  $$('input').each( function(oCheckbox) {
    if(oCheckbox.checked){
      if(!oCheckbox.hasClassName("med")){
	      elt = oCheckbox.id;
	      elts = elt.split("-");
	      id = elts[1];
	      if($('label-'+id)){
	        $('label-'+id).setStyle("color: #070");
		    }
		  }
		  oCheckbox.checked = false;
    }
  });
  
  
  var oFormToken = document.add_med_element;
  oFormToken.token_elt.value = '';

  $$('input.valeur').each( function(input) {
	   input.value = '';
	});
  
  $(document.forms.addPrisemode_grille).hide();
}

function submitAllElements(){
  // Divs
  var oDivFoisPar = $('foisParmode_grille');
  var oDivTousLes = $('tousLesmode_grille');

  // Forms
  var oForm      = document.forms.add_med_element;
  var oFormPrise = document.forms.addPrisemode_grille;
  var oFormChoix = document.forms["ChoixPrise-"];
  
  $(oFormPrise).show();
  
  // Formulaire par defaut
  var oFormDate;
  if(oFormDate = document.forms["editDates-{{$typeDate}}-"]){
    oForm.debut.value = oFormDate.debut.value;
    oForm.duree.value = oFormDate.duree.value;
    oForm.unite_duree.value = oFormDate.unite_duree.value;
    if(oForm.time_debut){
      oForm.time_debut.value = oFormDate.time_debut.value;
    }
  }
  // Formulaire dans le cas d'un protocole
  if(oFormDate = document.forms["editDuree-{{$typeDate}}-"]){
    oForm.duree.value = oFormDate.duree.value;
    if(oFormDate.jour_decalage){
      oForm.jour_decalage.value = oFormDate.jour_decalage.value;
    }
    oForm.decalage_line.value = oFormDate.decalage_line.value;
    oForm.time_debut.value = oFormDate.time_debut.value;
    if(oFormDate.jour_decalage_fin){
      oForm.jour_decalage_fin.value = oFormDate.jour_decalage_fin.value;
      oForm.decalage_line_fin.value = oFormDate.decalage_line_fin.value;
      oForm.time_fin.value = oFormDate.time_fin.value;
    }
  }

  if($V(oFormChoix.typePrise) == "momentmode_grille" && 
      $V(oFormPrise.moment_unitaire_id) && 
      oFormPrise.quantite.value){
    $V(oForm.moment_unitaire_id, $V(oFormPrise.moment_unitaire_id));
    $V(oForm.quantite          , oFormPrise.quantite.value);
  }
  else if($V(oFormChoix.typePrise) == "foisParmode_grille" && 
      oFormPrise.nb_fois.value && 
      oFormPrise.unite_fois.value && 
      oFormPrise.quantite.value){
    $V(oForm.nb_fois   , oFormPrise.nb_fois.value);
    $V(oForm.unite_fois, oFormPrise.unite_fois.value);
    $V(oForm.quantite  , oFormPrise.quantite.value);
  }
  else if($V(oFormChoix.typePrise) == "tousLesmode_grille" && 
      oFormPrise.nb_tous_les.value && 
      oFormPrise.unite_tous_les.value && 
      oFormPrise.quantite.value){
    $V(oForm.nb_tous_les       , oFormPrise.nb_tous_les.value);
    $V(oForm.unite_tous_les    , oFormPrise.unite_tous_les.value);
    $V(oForm.quantite          , oFormPrise.quantite.value);
    $V(oForm.moment_unitaire_id, oFormPrise.moment_unitaire_id.value);
    $V(oForm.decalage_prise    , oFormPrise.decalage_prise.value);
  }
  
  if (!oForm.token_elt.value) {
    return false;
  }
  $V(oForm.commentaire, $V(document.addCommentaire.commentaire));
  
  onSubmitFormAjax(oForm);
  resetModeEasy();
  return false;
}


Main.add( function(){
  // Initialisation des onglets
  menuTabs = Control.Tabs.create('main_prescription_easy_group', false);
  menuTabs.setActiveTab('{{$chapitre}}');
  
  // Initialisation des TokenFields
  oEltField = new TokenField(document.add_med_element.token_elt); 
  
  // Modification du praticien_id si celui-ci est spécifié
  if(window.opener.document.selPraticienLine){
    var oFormPraticien = window.opener.document.selPraticienLine;
    var oForm = document.add_med_element;
    oForm.praticien_id.value = oFormPraticien.praticien_id.value;
  }
  
  // Elements deja dans la prescription
	var elements = {{$elements|@json}};
	$$('input').each( function(oCheckbox) {
	  if(!oCheckbox.hasClassName("cat")){
		  var _id = oCheckbox.id;
		  var elts = _id.split("-");
		  var id = elts[1];
		  if(elements.include(id)){
		    $('label-'+id).setStyle("color: #070");
		  }
	  }
	});
	 
} );


</script>

<table class="form">
  <tr>
    <th class="category" colspan="2">Dates</th>
  </tr>
  <tr>
    <td colspan="2">
	    {{include file="../../dPprescription/templates/line/inc_vw_dates.tpl" 
	              perm_edit=1
	              dosql=CPrescriptionLineElement}}	      
	              
	     <script type="text/javascript">
	     if(document.forms["editDates-{{$typeDate}}-"]){
	       prepareForm("editDates-{{$typeDate}}-");  
				 {{if !$line->fin}} 
	         Calendar.regField("editDates-{{$typeDate}}-", "debut");
	         Calendar.regField("editDates-{{$typeDate}}-", "_fin");     
	       {{else}}
	         Calendar.regField("editDates-{{$typeDate}}-", "fin");     
	       {{/if}}
	     }
	     </script>
	  </td>
	</tr>
	<tr>
	  <th class="category">Fréquence</th>
	  <th class="category">Commentaire</th>
	</tr>
	<tr>
	  <td>
	    {{include file="../../dPprescription/templates/line/inc_vw_add_posologies.tpl"}}
	  </td>
	  <td style="text-align: center">
      <form name="addCommentaire" action="?" method="get">
        {{mb_field object=$line field="commentaire" size="50"}}
      </form>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center">
		  <form name="add_med_element" action="?" method="post" onsubmit="return submitAllElements();">
			  <input type="hidden" name="m" value="dPprescription" />
			  <input type="hidden" name="dosql" value="do_add_elements_easy_aed" />
			  <input type="hidden" name="token_elt" value="" />
			  <input type="hidden" name="prescription_id" value="{{$prescription_id}}" />
			  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
			  <input type="hidden" name="debut" value="" />
			  <input type="hidden" name="duree" value="" />
			  <input type="hidden" name="unite_duree" value="jour" />
			  <input type="hidden" name="mode_protocole" value="{{$mode_protocole}}" />
			  <input type="hidden" name="mode_pharma" value="{{$mode_pharma}}" />
			  <input type="hidden" name="decalage_line" value="" />
			  <input type="hidden" name="jour_decalage" value="" />
			  <input type="hidden" name="time_debut" value="" />
			  <input type="hidden" name="jour_decalage_fin" value="" />
			  <input type="hidden" name="decalage_line_fin" value="" />
			  <input type="hidden" name="time_fin" value="" />
			  <input type="hidden" name="commentaire" value="" />
			  <input class="valeur" type="hidden" name="quantite" value="" />
			  <input class="valeur" type="hidden" name="nb_fois" value="" />
			  <input class="valeur" type="hidden" name="unite_fois" value="" />
			  <input class="valeur" type="hidden" name="moment_unitaire_id" value="" />
			  <input class="valeur" type="hidden" name="nb_tous_les" value="" />
			  <input class="valeur" type="hidden" name="unite_tous_les" value="" />
			  <input class="valeur" type="hidden" name="decalage_prise" value="" />
			  <button type="button" 
			          class="submit" 
			          onclick="this.form.onsubmit()">Ajouter les éléments sélectionnés à la prescription</button>
			</form>
    </td>
  </tr>
</table>




<!-- Tabulations -->
<ul id="main_prescription_easy_group" class="control_tabs">
  {{assign var=specs_chapitre value=$class_category->_specs.chapitre}}
  {{foreach from=$specs_chapitre->_list item=_nom_chapitre}}
  <li><a href="#div_{{$_nom_chapitre}}">{{tr}}CCategoryPrescription.chapitre.{{$_nom_chapitre}}{{/tr}}</a></li>
  {{/foreach}}
</ul>
<hr class="control_tabs" />

<form action="" method="get" onsubmit="return false;">
<!-- Affichage des elements -->
{{assign var=numCols value=4}}
<table class="main">
  <tr>
  <td>
		{{foreach from=$chapitres key=name_chap item=chapitre}}
    <div id="div_{{$name_chap}}" style="display: none;">
	    <table class="tbl">
	    {{foreach from=$chapitre item=categorie}}
	      <tr>
	        <th colspan="{{$numCols*2}}">{{$categorie->_view}}
	          {{assign var=categorie_id value=$categorie->_id}}
	          <button  id="{{$categorie->_id}}" class="cat tick"  style="position: absolute; right: 12px; margin-top: -2px;" onclick="addCategorie('{{$categorie->_id}}',oEltField);" title="Ajouter cet élément">
	          Ajouter tous les éléments de la catégorie
	          </button>
	        </th>
	       </tr>
	      {{if $categorie->_ref_elements_prescription|@count}}
	      <tr>
	      {{/if}}
	      {{foreach from=$categorie->_ref_elements_prescription item=element name=elements}}
	        {{assign var=i value=$smarty.foreach.elements.iteration}}
	        <td style="width: 1%;">
	          <input type="checkbox" name="elt-{{$element->_id}}" 
	                  class="{{$categorie->_id}}" 
	                  onclick="oEltField.toggle('{{$element->_id}}', this.checked);" />    
          </td>
          <td>		         
            <label id="label-{{$element->_id}}" for="elt-{{$element->_id}}">{{$element->_view}}</label>
	        </td>
	        {{if (($i % $numCols) == 0)}}</tr>{{if !$smarty.foreach.elements.last}}<tr>{{/if}}{{/if}}
	      {{/foreach}}
	    {{foreachelse}}
			  <div class="big-info">
			    Aucun élément dans la catégorie {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}}
			  </div>
	    {{/foreach}}
	    </table>
    </div>
    {{/foreach}}
    </td>
  </tr>
</table>  
</form>