{{assign var=typeDate value="mode_grille"}}
{{assign var=line value=$filter_line_element}}
{{assign var=type value="mode_grille"}} 
 
<script type="text/javascript">

// Initialisation des onglets
Main.add( function(){
  menuTabs = Control.Tabs.create('main_tab_group', false);  
} );


// Ajout d'un élément dans la prescription
function addElement(element_id, name_chap){
  var formDate = document.forms["editDates-{{$typeDate}}-"];

  // Divs
  var oDivMoment = $('momentmode_grille');
  var oDivFoisPar = $('foisParmode_grille');
  var oDivTousLes = $('tousLesmode_grille');

  // Forms
  var oFormMoment = document.addPriseMomentmode_grille;
  var oFormFoisPar = document.addPriseFoisParmode_grille;
  var oFormTousLes = document.addPriseTousLesmode_grille;

  // Formulaire d'ajout de prises
  var oFormPrise = window.opener.document.addPriseElement;

  var debut = "";
  var duree = "";
  var unite_duree = "";
  var callback = "";
  
  // Effacement des champs 
  oFormPrise.moment_unitaire_id.value = "";
  oFormPrise.quantite.value = "";
  oFormPrise.nb_fois.value = "";
  oFormPrise.unite_fois.value = "";
  oFormPrise.nb_tous_les.value = "";
  oFormPrise.unite_tous_les.value = "";
  
  if(name_chap != "dmi"){
    if(name_chap != "dm"){
		  if(oDivMoment.visible() && oFormMoment.moment_unitaire_id.value && oFormMoment.quantite.value){
		    oFormPrise.moment_unitaire_id.value = oFormMoment.moment_unitaire_id.value;
		    oFormPrise.quantite.value = oFormMoment.quantite.value;
		    callback = "Prescription.submitPriseElement";
		  }
		  if(oDivFoisPar.visible() && oFormFoisPar.nb_fois.value && oFormFoisPar.unite_fois.value && oFormFoisPar.quantite.value){
		    oFormPrise.nb_fois.value = oFormFoisPar.nb_fois.value;
		    oFormPrise.unite_fois.value = oFormFoisPar.unite_fois.value;
		    oFormPrise.quantite.value = oFormFoisPar.quantite.value;
		    callback = "Prescription.submitPriseElement";
		  }
		  if(oDivTousLes.visible() && oFormTousLes.nb_tous_les.value && oFormTousLes.unite_tous_les.value && oFormTousLes.quantite.value){
		    oFormPrise.nb_tous_les.value = oFormTousLes.nb_tous_les.value;
		    oFormPrise.unite_tous_les.value = oFormTousLes.unite_tous_les.value;
		    oFormPrise.quantite.value = oFormTousLes.quantite.value;
		    callback = "Prescription.submitPriseElement";
		  }
    }
  
    oFormPrise.category_name.value = name_chap; 
    debut = formDate.debut.value;
    duree = formDate.duree.value;
    unite_duree = formDate.unite_duree.value;
  }

  window.opener.Prescription.addLineElement(element_id, name_chap, debut, duree, unite_duree, callback);
  $('elt-'+element_id).setOpacity(0.3);
}


// Ajout de tous les elements d'une catégorie dans la prescription
function addCategorie(categorie_id, name_chap){
  var formDate = document.forms["editDates-{{$typeDate}}-"];

  // debut
    // Divs
  var oDivMoment = $('momentmode_grille');
  var oDivFoisPar = $('foisParmode_grille');
  var oDivTousLes = $('tousLesmode_grille');

  // Forms
  var oFormMoment = document.addPriseMomentmode_grille;
  var oFormFoisPar = document.addPriseFoisParmode_grille;
  var oFormTousLes = document.addPriseTousLesmode_grille;

  // Formulaire d'ajout de prises
  var oFormPrise = window.opener.document.addPriseElement;

  var debut = "";
  var duree = "";
  var unite_duree = "";
  var callback = "";
  
  var oFormElementCat = document.addElementsCat;
  
  
  // Effacement des champs 
  oFormElementCat.moment_unitaire_id.value = "";
  oFormElementCat.quantite.value = "";
  oFormElementCat.nb_fois.value = "";
  oFormElementCat.unite_fois.value = "";
  oFormElementCat.nb_tous_les.value = "";
  oFormElementCat.unite_tous_les.value = "";
  
  if(name_chap != "dmi"){
    if(name_chap != "dm"){
		  if(oDivMoment.visible() && oFormMoment.moment_unitaire_id.value && oFormMoment.quantite.value){
		    oFormElementCat.moment_unitaire_id.value = oFormMoment.moment_unitaire_id.value;
		    oFormElementCat.quantite.value = oFormMoment.quantite.value;
		  }
		  if(oDivFoisPar.visible() && oFormFoisPar.nb_fois.value && oFormFoisPar.unite_fois.value && oFormFoisPar.quantite.value){
		    oFormElementCat.nb_fois.value = oFormFoisPar.nb_fois.value;
		    oFormElementCat.unite_fois.value = oFormFoisPar.unite_fois.value;
		    oFormElementCat.quantite.value = oFormFoisPar.quantite.value;
		  }
		  if(oDivTousLes.visible() && oFormTousLes.nb_tous_les.value && oFormTousLes.unite_tous_les.value && oFormTousLes.quantite.value){
		    oFormElementCat.nb_tous_les.value = oFormTousLes.nb_tous_les.value;
		    oFormElementCat.unite_tous_les.value = oFormTousLes.unite_tous_les.value;
		    oFormElementCat.quantite.value = oFormTousLes.quantite.value;
		  }
	  }
    oFormElementCat.debut.value = formDate.debut.value;
    oFormElementCat.duree.value = formDate.duree.value;
    oFormElementCat.unite_duree.value = formDate.unite_duree.value;
  }
  
  
  oFormElementCat.category_id.value = categorie_id;
  oFormElementCat.category_name.value = name_chap;
  oFormElementCat.prescription_id.value = window.opener.document.addLineElement.prescription_id.value;
  submitFormAjax(oFormElementCat, "systemMsg");
  
  // fin
  /*
  if(category_name != "dmi"){
    debut = formDate.debut.value;
        duree = formDate.duree.value;
    unite_duree = formDate.unite_duree.value;
  }
  */
  // Parcours des elements
  /*
  $$('button.cat-'+categorie_id).each( function(button) {
    elt = button.id
    elts = elt.split("-");
    var elements = new Array();
 
      window.opener.Prescription.addLineElementWithoutRefresh(elts[1], debut, duree, unite_duree, callback);
    
    $('elt-'+elts[1]).setOpacity(0.3);
  });
  // refresh de la prescription
  var oForm = window.opener.document.addLineElement;
  window.opener.Prescription.reload(oForm.prescription_id.value, elts[1], name_chap);
  */
}



</script>



<table class="form">
  <tr>
    <th class="category">Dates</th>
    <th class="category">Fréquence</th>
  </tr>
  <tr>
    <td>
	    {{include file="../../dPprescription/templates/line/inc_vw_dates.tpl" 
	              perm_edit=1
	              dosql=CPrescriptionLineElement}}
	 
	     <script type="text/javascript">
	       prepareForm(document.forms["editDates-{{$typeDate}}-"]);    
	       regFieldCalendar("editDates-{{$typeDate}}-", "debut");
	       regFieldCalendar("editDates-{{$typeDate}}-", "_fin");       
	     </script>
	     
	     
	     <form name="addElementsCat" method="post" action="?">
	       <input type="hidden" name="m" value="dPprescription" />
	       <input type="hidden" name="del" value="0" />
	       <input type="hidden" name="dosql" value="do_add_elements_easy_aed" />
	       <input type="hidden" name="category_id" value="" />
	       <input type="hidden" name="category_name" value=""/>
	       <input type="hidden" name="prescription_id" value="" />
	       <input type="hidden" name="debut" value="" />
	       <input type="hidden" name="duree" value="" />
	       <input type="hidden" name="unite_duree" value="" />
	       <input type="hidden" name="quantite" value="" />
			   <input type="hidden" name="nb_fois" value="" />
			   <input type="hidden" name="unite_fois" value="" />
			   <input type="hidden" name="moment_unitaire_id" value="" />
			   <input type="hidden" name="nb_tous_les" value="" />
			   <input type="hidden" name="unite_tous_les" value="" />
	     </form>
	  </td>
	  <td>
	    {{include file="../../dPprescription/templates/line/inc_vw_add_posologies.tpl"}}
	  </td>
  </tr>
</table>


<!-- Tabulations -->
<ul id="main_tab_group" class="control_tabs">
  {{assign var=specs_chapitre value=$class_category->_specs.chapitre}}
  {{foreach from=$specs_chapitre->_list item=_nom_chapitre}}
  <li><a href="#div_{{$_nom_chapitre}}">{{tr}}CCategoryPrescription.chapitre.{{$_nom_chapitre}}{{/tr}}</a></li>
  {{/foreach}}
</ul>
<hr class="control_tabs" />

<!-- Affichage des elements -->
{{assign var=numCols value=4}}
<table class="main">
  <tr>
  <td>
		<!-- Affichage des divs -->
		{{foreach from=$chapitres key=name_chap item=chapitre}}
		  <div id="div_{{$name_chap}}" style="display: none">
		    <table class="tbl">
		    {{foreach from=$chapitre item=categorie}}
		      <tr>
		        <th colspan="{{$numCols}}">{{$categorie->_view}}
		          {{assign var=categorie_id value=$categorie->_id}}
		          <button  id="cat-{{$categorie->_id}}" class="tick"  style="position: absolute; right: 12px; margin-top: -2px;" onclick="addCategorie('{{$categorie->_id}}','{{$name_chap}}');" title="Ajouter cet élément">
		          Ajouter tous les éléments de la catégorie
		          </button>
		        </th>
		       </tr>
		      <tr>
		      {{foreach from=$categorie->_ref_elements_prescription item=element name=elements}}
		        {{assign var=i value=$smarty.foreach.elements.iteration}}
		        <td>
		        <button  id="elt-{{$element->_id}}" class="cat-{{$categorie->_id}} tick notext" onclick="addElement('{{$element->_id}}','{{$name_chap}}');" title="Ajouter cet élément"></button>
		        {{$element->_view}}</td>
		        {{if ((($i % $numCols) == 0) && $i != 1)}}</tr><tr>{{/if}}
		      {{/foreach}}
		    {{/foreach}}
		    </table>
		  </div>
      {{/foreach}}
    </td>
  </tr>
</table>  