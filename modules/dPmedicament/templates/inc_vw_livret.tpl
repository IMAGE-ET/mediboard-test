{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
// UpdateFields de l'autocomplete
function updateFields(selected) {
  Element.cleanWhitespace(selected);
  var dn = selected.childElements();
  if (dn[1]) {
    Livret.addProduit(dn[0].innerHTML, dn[3].innerHTML.strip());
  }
  getForm("searchProd").produit.value = "";
}

Main.add(function(){
  var url = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
  url.autoComplete(getForm("searchProd").produit, "produit_auto_complete", {
      minChars: 3,
      updateElement: updateFields,
      width: "350px",
      callback: function(input, queryString){
        return (queryString + "&search_by_cis=0"); 
      }
  } );
});
</script>

<form action="?m=dPmedicament" method="post" name="addProduit" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPmedicament" />
  <input type="hidden" name="dosql" value="do_produit_livret_aed" />
  <input type="hidden" name="del" value="0" />
	<input type="hidden" name="produit_livret_id" value="" />
  {{if isset($function_guid|smarty:nodefaults)}}
    <input type="hidden" name="_function_guid" value="{{$function_guid}}"/>
  {{/if}}
  <input type="hidden" name="code_cip" value=""/>
</form>

<div style="font-size: 1.1em; text-align: center" class="pagination">
{{foreach from=$tabLettre item=_lettre}}
  <a href="#1" onclick="Livret.reloadAlpha('{{$_lettre}}')" class="page {{if $lettre == $_lettre}}active{{/if}}">
    {{$_lettre}}
  </a>
{{/foreach}}
  - 
  <a href="#1" onclick="Livret.reloadAlpha('hors_T2A')" class="page {{if $lettre == "hors_T2A"}}active{{/if}}">
    Hors T2A
  </a>
</div>

{{if $lettre}}	
<table class="tbl">
  <tr>
    <th colspan="11" class="title">{{$produits_livret|@count}} produits dans le livret</th>
  </tr>  
  <tr>
    <th>Actions</th>
    <th>Libelle</th>
    <th>Code CIP</th>
    <th>Code UCD</th>
		<th>Code CIS</th>
    <th>Alias</th>
		<th>Unité de prise</th>
  </tr>
  {{foreach from=$produits_livret item=produit_livret}}
	  {{assign var=produit_livret_mb value=$produit_livret->_ref_produit_livret_therapeutique}}
  <tr>
    <td>
      <button type="button" class="trash notext" onclick="Livret.delProduit('{{$produit_livret->code_cip}}','{{$lettre}}','','{{$produit_livret_mb->_id}}')">
        {{tr}}Delete{{/tr}}
      </button>
      <button type="button" class="edit notext" onclick="Livret.editProduit('{{$produit_livret->code_cip}}','{{$lettre}}','')">
        {{tr}}Modify{{/tr}} 
      </button>
    </td>  
    <td class="text">
			<div style="float: right">
      {{if $produit_livret->_ref_produit->hospitalier}}
      <img src="./images/icons/hopital.gif" title="Produit Hospitalier" />
      {{/if}}
      {{if !$produit_livret->_ref_produit->inT2A}}
        <img src="images/icons/T2A_barre.gif" title="Produit hors T2A" />
      {{/if}}
      {{if $produit_livret->_ref_produit->_generique}}
      <img src="./images/icons/generiques.gif" title="Produit Générique" />
      {{/if}}
      {{if $produit_livret->_ref_produit->_referent}}
      <img src="./images/icons/referents.gif" title="Produit Référent" />
      {{/if}}
      </div>
      <a href="#produit{{$produit_livret->code_cip}}" 
      {{if $produit_livret->_ref_produit->_supprime}}style="color:red"{{/if}} onclick="Prescription.viewProduit('{{$produit_livret->code_cip}}')">
        {{$produit_livret->_ref_produit->libelle_long}}
      </a>
    </td>
    <td>{{$produit_livret_mb->code_cip}}</td>
    <td>{{$produit_livret_mb->code_ucd}}</td>
		<td>{{$produit_livret_mb->code_cis}}</td>
    <td class="text">{{$produit_livret_mb->commentaire}}</td> 
		<td>{{$produit_livret_mb->unite_prise}}</td>
  </tr>
  {{/foreach}}
</table>
{{else}}
	<div class="small-info">
	Veuillez sélectionner la première lettre du produit recherché
	</div>
{{/if}}
