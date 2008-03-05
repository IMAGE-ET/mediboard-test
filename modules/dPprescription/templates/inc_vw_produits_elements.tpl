<script type="text/javascript">

// Initialisation des onglets
Main.add( function(){
  menuTabs = new Control.Tabs('main_tab_group');
  menuTabs.setActiveTab("div_{{$category}}");
} );

// Initialisation des alertes
if($('alertes')){
  Prescription.reloadAlertes({{$prescription->_id}});
}

</script>

<!-- Formulaire d'ajout de ligne dans la prescription -->
<form action="?m=dPprescription" method="post" name="addLine" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_aed" />
  <input type="hidden" name="prescription_line_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="code_cip" value=""/>
</form>

<!-- Formulaire d'ajout de ligne d'element dans la prescription -->
<form action="?m=dPprescription" method="post" name="addLineElement" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
  <input type="hidden" name="prescription_line_element_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="element_prescription_id" value=""/>
</form>

<!-- Affichage des div des medicaments et autres produits -->
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
   
{{include file="inc_div_element.tpl" element="dmi"}}
{{include file="inc_div_element.tpl" element="labo"}}
{{include file="inc_div_element.tpl" element="imagerie"}}
{{include file="inc_div_element.tpl" element="consult"}}
{{include file="inc_div_element.tpl" element="kine"}}
{{include file="inc_div_element.tpl" element="soin"}}

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
    {{assign var="color" value=#ccc}}
      {{if $curr_line->_nb_alertes}}
        
        {{if $curr_line->_ref_alertes.IPC || $curr_line->_ref_alertes.profil}}
          {{assign var="image" value="note_orange.png"}}
          {{assign var="color" value=#fff288}}
        {{/if}}  
        {{if $curr_line->_ref_alertes.allergie || $curr_line->_ref_alertes.interaction}}
          {{assign var="image" value="note_red.png"}}
          {{assign var="color" value=#ff7474}}
        {{/if}}  
        <img src="images/icons/{{$image}}" title="" alt="" 
             onmouseover="$('line-{{$curr_line->_id}}').show();"
             onmouseout="$('line-{{$curr_line->_id}}').hide();" />
      {{/if}}
      <div id="line-{{$curr_line->_id}}" class="tooltip" style="display: none; background-color: {{$color}}; border-style: ridge; padding-right:5px; ">
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
      <form name="addCommentMedicament-{{$curr_line->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this);">
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
      <form name="addCommentElement-{{$curr_line_element->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this);">
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
	updateFieldsProduit = function(selected) {
	  Element.cleanWhitespace(selected);
	  dn = selected.childNodes;
	  Prescription.addLine(dn[0].firstChild.nodeValue);
	  $('searchProd_produit').value = "";
	}
	
	// UpdateFields de l'autocomplete des elements
	updateFieldsElement = function(selected, formElement, element) {
	  Element.cleanWhitespace(selected);
	  dn = selected.childNodes;
	  Prescription.addLineElement(dn[0].firstChild.nodeValue);
	  $(formElement+'_'+element).value = "";
	}

  // Preparation des formulaire
  prepareForm(document.addLine);
  prepareForm(document.searchProd);
 
  // Autocomplete des medicaments
  urlAuto = new Url();
  urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
  urlAuto.addParam("produit_max", 10);
  urlAuto.autoComplete("searchProd_produit", "produit_auto_complete", {
      minChars: 3,
      updateElement: updateFieldsProduit
  } );
     
</script>