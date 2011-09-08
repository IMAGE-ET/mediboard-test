{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="dPmedicament" script="medicament_selector"}}
{{mb_script module="dPmedicament" script="equivalent_selector"}}
{{mb_script module="dPprescription" script="element_selector"}}
{{mb_script module="dPprescription" script="prescription"}}
{{mb_script module="dPprescription" script="protocole"}}
{{mb_script module="dPcabinet" script="file"}}

<script type="text/javascript">

Main.add( function(){
  // Refesh de la liste des protocoles
  Protocole.refreshList('{{$protocole_id}}');
  {{if $protocole_id}}
  Prescription.reload('{{$protocole_id}}', '', '', '1');
  {{/if}}
	
	window.tabProtocoles = Control.Tabs.create('tab_edit_protocoles', true);
	
  // Autocomplete des medicaments
  var urlAutoMed = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
  urlAutoMed.addParam("produit_max", 40);
  urlAutoMed.autoComplete(getForm("searchProt").produit, "produit_prot_auto_complete", {
    minChars: 3,
    updateElement: updateFieldsMedicamentProt
  } );
	
	// Autocomplete des elements
  var urlAutoElt = new Url("dPprescription", "httpreq_do_element_autocomplete");
  urlAutoElt.autoComplete(getForm("searchProt").libelle, "element_prot_auto_complete", {
    minChars: 2,
		updateElement: updateFieldsElementProt
	} );
} );

updateFieldsMedicamentProt = function(selected) {
  var oFormProt = getForm("searchProt");
  Element.cleanWhitespace(selected);
	$V(oFormProt.produit, selected.down('.libelle_ucd').innerHTML.stripTags().strip());
	$V(oFormProt.code_cis, selected.down('.code-cis').innerHTML);
}

updateFieldsElementProt = function(selected) {
  var oFormProt = getForm("searchProt");
  Element.cleanWhitespace(selected);
  var dn = selected.childNodes;
	$V(oFormProt.libelle, dn[2].down().innerHTML);
	$V(oFormProt.element_prescription_id, dn[0].firstChild.nodeValue);
}

resetOwnerForm = function(){
  var oForm = getForm("selPrat");
	$V(oForm.praticien_id, "");
	$V(oForm.function_id, "");
  $V(oForm.group_id, "");
}

resetSearchForm = function(){
  var oForm = getForm("searchProt");
	$V(oForm.libelle_protocole, "");
	$V(oForm.produit, "");
  $V(oForm.libelle, "");
	$V(oForm.code_cis, "");
	$V(oForm.element_prescription_id, "");
}

</script>

<table class="main">
	<tr>
    <!-- Affichage de la liste des protocoles pour le praticien selectionné -->
    <td class="halfPane" style="width: 23em;" id="list_protocoles">
		
			<ul id="tab_edit_protocoles" class="control_tabs">
	      <li onmousedown="resetSearchForm(); Protocole.refreshList('');"><a href="#creation_prot">Création</a></li>
			  <li onmousedown="resetOwnerForm(); Protocole.refreshList('');"><a href="#recherche_prot">Recherche</a></li>
			</ul>
			<hr class="control_tabs" />
	
			<div id="creation_prot" style="display: none;">
		    <form name="selPrat" action="?" method="get">
		      <input type="hidden" name="tab" value="vw_edit_protocole" />
	        <input type="hidden" name="m" value="dPprescription" />
	        <select name="praticien_id" onchange="this.form.function_id.value=''; this.form.group_id.value=''; Protocole.refreshListProt();" style="width: 23em;">
	          <option value="">&mdash; Praticien</option>
		        {{foreach from=$praticiens item=praticien}}
		        <option class="mediuser" 
		                style="border-color: #{{$praticien->_ref_function->color}};" 
		                value="{{$praticien->_id}}"
		                {{if $praticien->_id == $praticien_id}}selected="selected"{{/if}}>{{$praticien->_view}}
		        </option>
		        {{/foreach}}
		      </select>
		      <select name="function_id" onchange="this.form.praticien_id.value=''; this.form.group_id.value=''; Protocole.refreshListProt();" style="width: 23em;">
	          <option value="">&mdash; Cabinet</option>
	          {{foreach from=$functions item=_function}}
	          <option class="mediuser" style="border-color: #{{$_function->color}}" value="{{$_function->_id}}" {{if $function_id == $_function->_id}}selected=selected{{/if}} title="{{$_function->_view}}">{{$_function->_view|spancate:40}}</option>
	          {{/foreach}}
	        </select>
	        <select name="group_id" onchange="this.form.function_id.value=''; this.form.praticien_id.value=''; Protocole.refreshListProt();" style="width: 23em;">
	          <option value="">&mdash; Etablissement</option>
	          {{foreach from=$groups item=_group}}
	          <option value="{{$_group->_id}}" {{if $group_id == $_group->_id}}selected=selected{{/if}}>{{$_group->_view}}</option>
	          {{/foreach}}
	        </select>
					<br />
		      <button type="button" class="submit" onclick="this.form.submit();">
		        Créer un protocole
		      </button>
	        {{if $can->admin}}
	          <button type="button" class="new" type="button" onclick="Protocole.importProtocole('selPrat');">
	          {{tr}}CPrescription.import_protocole{{/tr}}
	          </button>
	        {{/if}}
		    </form>
		  </div>
			<div id="recherche_prot" style="display: none;">
				<form name="searchProt" method="get" action="?" onsubmit="Protocole.refreshList(null, $V(this.libelle_protocole), $V(this.code_cis), $V(this.element_prescription_id)); return false;">
					<table class="form">
						<tr>
							<th class="category">Libelle du protocole</th>
						</tr>
						<tr>
							<td>
								<input type="text" name="libelle_protocole" value="" size="20" style="font-weight: bold; font-size: 1.3em; width: 216px;" />
                 <button type="submit" class="search notext">{{tr}}Filter{{/tr}}</button>
							</td>
						</tr>
            <tr>
              <th class="category">Produit</th>
            </tr>
            <tr>
              <td>
                <input type="text" name="produit" value="&mdash; {{tr}}CPrescription.select_produit{{/tr}}" size="20" style="font-weight: bold; font-size: 1.3em; width: 200px;" class="autocomplete" onclick="this.value = ''; this.form.code_cis.value='';" />
			          <div style="display:none; width: 350px;" class="autocomplete" id="produit_prot_auto_complete"></div>
			          <input type="hidden" name="code_cis" onchange="this.form.onsubmit();"/>
              </td>
            </tr>
            <tr>
              <th class="category">Element</th>
            </tr>
            <tr>
              <td>
                <input type="text" name="libelle" value="&mdash; Element" size="20" style="font-weight: bold; font-size: 1.3em; width: 200px;" class="autocomplete" onclick="this.value = ''; this.form.element_prescription_id.value='';" />
			          <div style="display:none; width: 350px;" class="autocomplete" id="element_prot_auto_complete"></div>
			          <input type="hidden" name="element_prescription_id" onchange="this.form.onsubmit();"/>
              </td>
            </tr>
					</table>
				</form>
			</div>
	    <div id="protocoles"></div>
		</td>
    <!-- Affichage du protocole sélectionné-->
    <td>
      <div id="vw_protocole">
        {{if !$protocole_id}}
		    {{include file="inc_vw_prescription.tpl" httpreq=1 mode_protocole=1 prescription=$protocole category="medicament"}}
		    {{/if}}
		  </div>  
    </td>
  </tr>
</table>