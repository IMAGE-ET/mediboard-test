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
	  $('searchProd_produit').value = "";
	}
</script>

<form action="?m=dPmedicament" method="post" name="addProduit" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPmedicament" />
  <input type="hidden" name="dosql" value="do_produit_livret_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="group_id" value="{{$g}}"/>
  <input type="hidden" name="code_cip" value=""/>
  
</form>

<div style="font-size: 1.1em; text-align: center">
{{foreach from=$tabLettre item=_lettre}}
  <a href="#" onclick="Livret.reloadAlpha('{{$_lettre}}')">
    {{if $lettre == $_lettre}}
      <strong>[{{$_lettre}}]</strong>
    {{else}}
      {{$_lettre}}
    {{/if}}
  </a>
{{/foreach}}
  - 
  <a href="#" onclick="Livret.reloadAlpha('hors_T2A')">
    {{if $lettre == "hors_T2A"}}
      <strong>[Hors T2A]</strong>
    {{else}}
      Hors T2A
    {{/if}}
  </a>
</div>

{{if $lettre}}	
<table class="tbl">
  <tr>
    <th colspan="10">{{$produits_livret|@count}} produits dans le livret</th>
  </tr>  
  <tr>
    <th>Actions</th>
    <th>Libelle</th>
    <th>Code CIP</th>
    <th>Code UCD</th>
    <th>Prix Hôpital</th>
    <th>Prix Ville</th>
    <th>Date Prix Hôpital</th>
    <th>Date Prix Ville</th>
    <th>Code Interne</th>
    <th>Commentaire</th>
  </tr>
  {{foreach from=$produits_livret item=produit_livret}}
  <tr>
    <td>
      <button type="button" class="trash notext" onclick="Livret.delProduit('{{$produit_livret->code_cip}}','{{$lettre}}','')">
        {{tr}}Delete{{/tr}}
      </button>
      <button type="button" class="edit notext" onclick="Livret.editProduit('{{$produit_livret->code_cip}}','{{$lettre}}','')">
        {{tr}}Modify{{/tr}} 
      </button>
    </td>  
    <td class="text">
			<div style="float: right">
      {{if $produit_livret->_ref_produit->hospitalier}}
      <img src="./images/icons/hopital.gif" alt="Produit Hospitalier" title="Produit Hospitalier" />
      {{/if}}
      {{if !$produit_livret->_ref_produit->inT2A}}
        <img src="images/icons/T2A_barre.gif" alt="Produit hors T2A" title="Produit hors T2A" />
      {{/if}}
      {{if $produit_livret->_ref_produit->_generique}}
      <img src="./images/icons/generiques.gif" alt="Produit Générique" title="Produit Générique" />
      {{/if}}
      {{if $produit_livret->_ref_produit->_referent}}
      <img src="./images/icons/referents.gif" alt="Produit Référent" title="Produit Référent" />
      {{/if}}
      </div>
      <a href="#produit{{$produit_livret->code_cip}}" 
      {{if $produit_livret->_ref_produit->_supprime}}style="color:red"{{/if}}onclick="Prescription.viewProduit('{{$produit_livret->code_cip}}')">
        {{$produit_livret->_ref_produit->libelle_long}}
      </a>
    </td>
    <td>{{$produit_livret->_ref_produit->code_cip}}</td>
    <td>{{$produit_livret->_ref_produit->code_ucd}}</td>
    <td>
      {{if $produit_livret->prix_hopital}}
        {{$produit_livret->prix_hopital|currency}}
      {{/if}}
    </td>
    <td>
      {{if $produit_livret->prix_ville}}
        {{$produit_livret->prix_ville|currency}}
      {{/if}}
    </td>
    <td>{{$produit_livret->date_prix_hopital|date_format:"%d/%m/%Y"}}</td>
    <td>{{$produit_livret->date_prix_ville|date_format:"%d/%m/%Y"}}</td>
    <td>{{$produit_livret->code_interne}}</td> 
    <td class="text">{{$produit_livret->commentaire}}</td> 
  </tr>
  {{/foreach}}
</table>
{{else}}
	<div class="small-info">
	Veuillez sélectionner la première lettre du produit recherché
	</div>
{{/if}}


<script type="text/javascript">
  // Preparation du formulaire
  prepareForm(document.addProduit);
  prepareForm(document.searchProd);
  // Autocomplete
  var url = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
  url.autoComplete("searchProd_produit", "produit_auto_complete", {
      minChars: 3,
      updateElement: updateFields,
      callback: 
	      function(input, queryString){
	        return (queryString + "&search_by_cis=0"); 
	      }
  } );
</script>