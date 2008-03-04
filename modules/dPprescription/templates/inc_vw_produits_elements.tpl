<script type="text/javascript">

 
Main.add( function(){
  menuTabs = new Control.Tabs('main_tab_group');
  menuTabs.setActiveTab("div_{{$category}}");
} );

if($('alertes')){
  Prescription.reloadAlertes({{$prescription->_id}});
}
</script>

<form action="?m=dPprescription" method="post" name="addLine" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_aed" />
  <input type="hidden" name="prescription_line_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="code_cip" value=""/>
</form>

<form action="?m=dPprescription" method="post" name="addLineElement" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
  <input type="hidden" name="prescription_line_element_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="element_prescription_id" value=""/>
</form>

<div id="div_medicament">
  <form action="?" method="get" name="searchProd" onsubmit="return false;">
    <select name="favoris" onchange="Prescription.addLine(this.value); this.value = '';">
      <option value="">&mdash; produits les plus utilisés</option>
      {{foreach from=$listFavoris.medicament item=curr_prod}}
      <option value="{{$curr_prod->code_cip}}">
        {{$curr_prod->libelle}}
      </option>
      {{/foreach}}
    </select>
    <br />
	  <input type="text" name="produit" value=""/>
	  <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
	  <button type="button" class="search" onclick="MedSelector.init('produit');">Produits</button>
	  <button type="button" class="search" onclick="MedSelector.init('classe');">Classes</button>
	  <button type="button" class="search" onclick="MedSelector.init('composant');">Composants</button>
	  <button type="button" class="search" onclick="MedSelector.init('DC_search');">DCI</button>
	  <script type="text/javascript">
		  if (MedSelector.oUrl) {
		    MedSelector.close();
		  }
		  MedSelector.init = function(onglet){
		    this.sForm = "searchProd";
		    this.sView = "produit";
		    this.sSearch = document.searchProd.produit.value;
		    this.sOnglet = onglet;
		    this.selfClose = false;
		    this.pop();
		  }
		  MedSelector.set = function(nom, code){
		    Prescription.addLine(code);
		  }
	</script>
 </form>
</div>
   
<div id="div_dmi" style="display:none">
  <form action="?" method="get" name="searchDmi" onsubmit="return false;">
    <select name="favoris" onchange="Prescription.addLineElement(this.value); this.value = '';">
      <option value="">&mdash; produits les plus utilisés</option>
      {{foreach from=$listFavoris.dmi item=curr_dmi}}
      <option value="{{$curr_dmi->_id}}">
        {{$curr_dmi->libelle}}
      </option>
      {{/foreach}}
    </select>
   <br />
   <input type="text" name="dmi" value="" />
   <input type="hidden" name="element_id" onchange="Prescription.addLineElement(this.value);" />
   <div style="display:none;" class="autocomplete" id="dmi_auto_complete"></div>
   <button class="search" type="button" onclick="ElementSelector.initDmi('dmi')">Rechercher</button>
   <script type="text/javascript">   
     ElementSelector.initDmi = function(type){
       this.sForm = "searchDmi";
       this.sLibelle = "dmi";
       this.sElement_id = "element_id";
       this.sType = type;
       this.selfClose = false;
       this.pop();
     }
   </script>    
 </form>
</div>
   
<div id="div_labo" style="display:none">      
	<form action="?" method="get" name="searchLabo" onsubmit="return false;">
    <select name="favoris" onchange="Prescription.addLineElement(this.value); this.value = '';">
      <option value="">&mdash; produits les plus utilisés</option>
      {{foreach from=$listFavoris.labo item=curr_labo}}
      <option value="{{$curr_labo->_id}}">
        {{$curr_labo->libelle}}
      </option>
      {{/foreach}}
    </select>
	   <br />
	  <input type="text" name="labo" value="" />
	  <input type="hidden" name="element_id" onchange="Prescription.addLineElement(this.value);" />
	  <div style="display:none;" class="autocomplete" id="labo_auto_complete"></div>
	  <button class="search" type="button" onclick="ElementSelector.initLabo('labo')">Rechercher</button>
    <script type="text/javascript">   
     ElementSelector.initLabo = function(type){
       this.sForm = "searchLabo";
       this.sLibelle = "labo";
       this.sElement_id = "element_id";
       this.sType = type;
       this.pop();
     }
   </script>
	</form>
</div>

   
<div id="div_imagerie" style="display:none">
 <form action="?" method="get" name="searchImagerie" onsubmit="return false;">
    <select name="favoris" onchange="Prescription.addLineElement(this.value); this.value = '';">
      <option value="">&mdash; produits les plus utilisés</option>
      {{foreach from=$listFavoris.imagerie item=curr_imagerie}}
      <option value="{{$curr_imagerie->_id}}">
        {{$curr_imagerie->libelle}}
      </option>
      {{/foreach}}
    </select>
   <br />
   <input type="text" name="imagerie" value="" />
   <input type="hidden" name="element_id" onchange="Prescription.addLineElement(this.value);" />
   <div style="display:none;" class="autocomplete" id="imagerie_auto_complete"></div>
   <button class="search" type="button" onclick="ElementSelector.initImagerie('imagerie')">Rechercher</button>
   <script type="text/javascript">   
     ElementSelector.initImagerie = function(type){
       this.sForm = "searchImagerie";
       this.sLibelle = "imagerie";
       this.sElement_id = "element_id";
       this.sType = type;
       this.selfClose = false;
       this.pop();
     }
   </script>
 </form>
</div>
   
<div id="div_consult" style="display:none">
  <form action="?" method="get" name="searchConsult" onsubmit="return false;">
    <select name="favoris" onchange="Prescription.addLineElement(this.value); this.value = '';">
      <option value="">&mdash; produits les plus utilisés</option>
      {{foreach from=$listFavoris.consult item=curr_consult}}
      <option value="{{$curr_consult->_id}}">
        {{$curr_consult->libelle}}
      </option>
      {{/foreach}}
    </select>
   <br />
   <input type="text" name="consult" value="" />
   <input type="hidden" name="element_id" onchange="Prescription.addLineElement(this.value);" />
   <div style="display:none;" class="autocomplete" id="consult_auto_complete"></div>
   <button class="search" type="button" onclick="ElementSelector.initCons('consult')">Rechercher</button>
   <script type="text/javascript">   
     ElementSelector.initCons = function(type){
       this.sForm = "searchConsult";
       this.sLibelle = "consult"; 
       this.sElement_id = "element_id";
       this.sType = type;
       this.selfClose = false;
       this.pop();
     }
   </script>
 </form>
</div>
   
<div id="div_kine" style="display:none">
  <form action="?" method="get" name="searchKine" onsubmit="return false;">
    <select name="favoris" onchange="Prescription.addLineElement(this.value); this.value = '';">
      <option value="">&mdash; produits les plus utilisés</option>
      {{foreach from=$listFavoris.kine item=curr_kine}}
      <option value="{{$curr_kine->_id}}">
        {{$curr_kine->libelle}}
      </option>
      {{/foreach}}
    </select>
   <br />
   <input type="text" name="kine" value="" />
   <input type="hidden" name="element_id" onchange="Prescription.addLineElement(this.value);" />
   <div style="display:none;" class="autocomplete" id="kine_auto_complete"></div>
   <button class="search" type="button" onclick="ElementSelector.initKine('kine')">Rechercher</button>
   <script type="text/javascript">   
     ElementSelector.initKine = function(type){
       this.sForm = "searchKine";
       this.sLibelle = "kine";
       this.sElement_id = "element_id";
       this.sType = type;
       this.selfClose = false;
       this.pop();
     }
   </script>
 </form>
</div>
   
<div id="div_soin" style="display:none">
  <form action="?" method="get" name="searchSoin" onsubmit="return false;">
    <select name="favoris" onchange="Prescription.addLineElement(this.value); this.value = '';">
      <option value="">&mdash; produits les plus utilisés</option>
      {{foreach from=$listFavoris.soin item=curr_soin}}
      <option value="{{$curr_soin->_id}}">
        {{$curr_soin->libelle}}
      </option>
      {{/foreach}}
    </select>
    <br />
    <input type="text" name="soin" value="" />
    <input type="hidden" name="element_id" onchange="Prescription.addLineElement(this.value);" />
    <div style="display:none;" class="autocomplete" id="soin_auto_complete"></div>
   <button class="search" type="button" onclick="ElementSelector.initSoin('soin')">Rechercher</button>
   <script type="text/javascript">   
     ElementSelector.initSoin = function(type){
       this.sForm = "searchSoin";
       this.sLibelle = "soin";
       this.sElement_id = "element_id";
       this.sType = type;
       this.selfClose = false;
       this.pop();
     }
   </script>
  </form>
</div>  
	    
<table class="tbl">
{{if $prescription->_ref_prescription_lines|@count}}
  <tr>
    <th colspan="4">Médicaments</th>
  </tr>
  {{/if}}
  {{foreach from=$prescription->_ref_prescription_lines item=curr_line}}
  <tbody class="hoverable">
  <tr>
    <td>
      <button type="button" class="trash notext" onclick="Prescription.delLine({{$curr_line->_id}})">
        {{tr}}Delete{{/tr}}
      </button>
    </td>
    <td>
      {{if $curr_line->_nb_alertes}}
      
      {{if $curr_line->_ref_alertes.IPC || $curr_line->_ref_alertes.profil}}
        {{assign var="image" value="note_orange.png"}}
      {{/if}}  
      {{if $curr_line->_ref_alertes.allergie || $curr_line->_ref_alertes.interaction}}
        {{assign var="image" value="note_red.png"}}
      {{/if}}  
      <img src="images/icons/{{$image}}" title="aucune" alt="aucune" onclick="viewFullAlertes()" />
      
      {{/if}}
      <div style="display : none;">
      {{foreach from=$curr_line->_ref_alertes_text key=type item=curr_type}}
        {{if $curr_type|@count}}
          <ul>
          {{foreach from=$curr_type item=curr_alerte}}
            <li>
              <strong>{{tr}}CPrescriptionLine-alerte-{{$type}}-court{{/tr}} :</strong>
              {{$curr_alerte}}
            </li>
          {{/foreach}}
          </ul>
        {{/if}}
      {{/foreach}}
      </div>
    </td>
    <td>
      <a href="#produit{{$curr_line->_id}}" onclick="viewProduit({{$curr_line->_ref_produit->code_cip}})">
        <strong>{{$curr_line->_view}}</strong>
      </a>
      
      <form action="?m=dPprescription" method="post" name="editLine-{{$curr_line->_id}}" onsubmit="return checkForm(this);">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_aed" />
        <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}"/>
        <input type="hidden" name="del" value="0" />
        <select name="no_poso" onchange="submitFormAjax(this.form, 'systemMsg')">
          <option value="">&mdash; Choisir une posologie</option>
          {{foreach from=$curr_line->_ref_produit->_ref_posologies item=curr_poso}}
          <option value="{{$curr_poso->code_posologie}}"
            {{if $curr_poso->code_posologie == $curr_line->no_poso}}selected="selected"{{/if}}>
            {{$curr_poso->_view}}
          </option>
          {{/foreach}}
        </select>
      </form>
      
    </td>
    <td>
    <div style="float: right;">
        <button type="button" class="change notext" onclick="EquivSelector.init('{{$curr_line->_id}}','{{$curr_line->_ref_produit->code_cip}}');">
          Equivalents
        </button>
        <script type="text/javascript">
          if(EquivSelector.oUrl) {
            EquivSelector.close();
          }
          EquivSelector.init = function(line_id, code_cip){
            this.sForm = "searchProd";
            this.sView = "produit";
            this.sCodeCIP = code_cip
            this.sLine = line_id;
            this.selfClose = false;
            this.pop();
          }
          EquivSelector.set = function(code, line_id){
            Prescription.addEquivalent(code, line_id);
          }
        </script>
      </div>
    <form name="addCommentMedicament-{{$curr_line->_id}}" method="post" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
        <input type="text" name="commentaire" value="{{$curr_line->commentaire}}" onchange="this.form.onsubmit();" />
      </form>
    
    </td>
  </tr>
  </tbody>
  {{/foreach}}
  
  <!-- Affichage des lignes de prescriptions hors medicaments -->
  {{foreach from=$prescription->_ref_prescription_lines_element_by_cat key=chap item=curr_chap}}
  <tr>
    <th colspan="4">
      {{tr}}CCategoryPrescription.chapitre.{{$chap}}{{/tr}}
    </th>
  </tr>  
  {{foreach from=$curr_chap item=curr_line_element}}
  <tbody class="hoverable">
  <tr>
    <td>
      <button type="button" class="trash notext" onclick="Prescription.delLineElement({{$curr_line_element->_id}})">
        {{tr}}Delete{{/tr}}
      </button>
    </td>
    <td colspan="2">
     {{$curr_line_element->_ref_element_prescription->_view}}
    </td>
    <td>
      <form name="addCommentElement-{{$curr_line_element->_id}}" method="post" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_line_element_id" value="{{$curr_line_element->_id}}" />
        <input type="text" name="commentaire" value="{{$curr_line_element->commentaire}}" onchange="this.form.onsubmit();" />
      </form>
    </td>
  </tr>
  </tbody>
  {{/foreach}}
  {{/foreach}}
</table>
    
<script type="text/javascript">
	    
  // UpdateFields de l'autocompete de medicaments
	function updateFieldsProduit(selected) {
	  Element.cleanWhitespace(selected);
	  dn = selected.childNodes;
	  Prescription.addLine(dn[0].firstChild.nodeValue);
	  $('searchProd_produit').value = "";
	}
	
	// UpdateFields de l'autocomplete des elements
	function updateFieldsElement(selected, formElement, element) {
	  Element.cleanWhitespace(selected);
	  dn = selected.childNodes;
	  Prescription.addLineElement(dn[0].firstChild.nodeValue);
	  $(formElement+'_'+element).value = "";
	}

  // Preparation des formulaire
  prepareForm(document.addLine);
  prepareForm(document.searchProd);
  prepareForm(document.searchDmi);
  prepareForm(document.searchLabo);
  prepareForm(document.searchImagerie);
  prepareForm(document.searchConsult);
  prepareForm(document.searchKine);
  prepareForm(document.searchSoin);
  
  // Autocomplete des medicaments
  urlAuto = new Url();
  urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
  urlAuto.addParam("produit_max", 10);
  urlAuto.autoComplete("searchProd_produit", "produit_auto_complete", {
      minChars: 3,
      updateElement: updateFieldsProduit
  } );
  
  // Autocomplete Dmi
  url = new Url();
  url.setModuleAction("dPprescription", "httpreq_do_element_autocomplete");
  url.addParam("category", "dmi");
  url.autoComplete("searchDmi_dmi", "dmi_auto_complete", {
      minChars: 3,
      updateElement: function(element) { updateFieldsElement(element, 'searchDmi', 'dmi') }
  } );
  
  // Autocomplete Laboratoire
  url = new Url();
  url.setModuleAction("dPprescription", "httpreq_do_element_autocomplete");
  url.addParam("category", "labo");
  url.autoComplete("searchLabo_labo", "labo_auto_complete", {
      minChars: 3,
      updateElement: function(element) { updateFieldsElement(element, 'searchLabo', 'labo') }
  } );
    
  // Autocomplete Imagerie
  url = new Url();
  url.setModuleAction("dPprescription", "httpreq_do_element_autocomplete");
  url.addParam("category", "imagerie");
  url.autoComplete("searchImagerie_imagerie", "imagerie_auto_complete", {
      minChars: 3,
      updateElement: function(element) { updateFieldsElement(element, 'searchImagerie', 'imagerie') }
  } );
  
  // Autocomplete consultation
  url = new Url();
  url.setModuleAction("dPprescription", "httpreq_do_element_autocomplete");
  url.addParam("categroy", "consult");
  url.autoComplete("searchConsult_consult", "consult_auto_complete", {
      minChars: 3,
      updateElement: function(element) { updateFieldsElement(element, 'searchConsult', 'consult') }
  } );
  
  // Autocomplete kine
  url = new Url();
  url.setModuleAction("dPprescription", "httpreq_do_element_autocomplete");
  url.addParam("category", "kine");
  url.autoComplete("searchKine_kine", "kine_auto_complete", {
      minChars: 3,
      updateElement: function(element) { updateFieldsElement(element, 'searchKine', 'kine') }
  } );

  // Autocomplete soin
  url = new Url();
  url.setModuleAction("dPprescription", "httpreq_do_element_autocomplete");
  url.addParam("category", "soin");
  url.autoComplete("searchSoin_soin", "soin_auto_complete", {
      minChars: 3,
      updateElement: function(element) { updateFieldsElement(element, 'searchSoin', 'soin') }
  } );
	    
  
  
</script>

	    