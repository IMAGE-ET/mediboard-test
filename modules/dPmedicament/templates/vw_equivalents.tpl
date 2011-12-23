{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function setClose(code, line_id) {
  var oSelector = window.opener.EquivSelector;
  oSelector.set(code, line_id);
  if(oSelector.selfClose) {
    window.close();
  }
}

Main.add(function () {
  // Initialisation des onglets du menu
  Control.Tabs.create('tabs-equivalent', false);

  // Autocomplete des produits
  var url = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
  url.autoComplete(getForm("searchProd").produit, "produit_auto_complete", {
    minChars: 2,
    updateElement: function(selected) {
      Element.cleanWhitespace(selected);
      var dn = selected.childNodes;
      setClose(dn[0].firstChild.nodeValue, '{{$line->_id}}')
    }
  } );
});

</script>

{{if $line->_count_administrations > 0}}
  <div class="small-info">
  	Cette ligne de prescription a déjà donné lieu à des administrations dans le plan de soins, 
		si vous décidez de la substituer, cela arrêtera la ligne courante pour la faire évoluer et créer ensuite la substitution.
  </div>
{{/if}}

	<table class="tbl">
	  <tr>
	    <th class="title">
	      Recherche d'équivalents {{if $inLivret}}dans le livret Thérapeutique{{/if}} pour {{$produit->libelle}}
	    </th>
	  </tr>
	</table>
	
	<ul id="tabs-equivalent" class="control_tabs">
	  <li><a href="#equivalents_strictes_BCB">Equivalents BCB</a></li>
	  <li><a href="#equivalents_strictes_ATC">Equivalents ATC strictes</a></li>
	  {{if $inLivret}}
	  <li><a href="#equivalents_therapeutiques_ATC">Equivalents ATC Thérapeutiques</a></li>
	  {{/if}}
		<li><a href="#recherche_produit">Recherche</a></li>
	</ul>
	<hr class="control_tabs" />
	<!-- Equivalents strictes BCB -->
	<table class="tbl" id="equivalents_strictes_BCB" style="display:none;">
	  <tr>
	    <th colspan="4">
	      Equivalents strictes BCB<br />{{$libelle_stricte_BCB}} ({{$code_stricte_BCB}})
	    </th>
	  </tr>
	  <tr>
	    <th>CIP</th>
	    <th>UCD</th>
	    <th>Produit</th>
	    <th>Laboratoire</th>
	  </tr>
	  {{foreach from=$equivalents_strictes_BCB item=produit}}
	    {{include file="../../dPmedicament/templates/inc_vw_produit.tpl" nodebug=true}}
	  {{foreachelse}}
	  <tr>
	    <td colspan="4">Aucun équivalent</td>
	  </tr>
	  {{/foreach}}
	</table>
	
	<!-- Equivalents sctictes ATC -->
	<table class="tbl" id="equivalents_strictes_ATC" style="display: none;">
	  <tr>
	    <th colspan="4">Equivalents strictes ATC<br />{{$libelle_stricte_ATC}} ({{$code_stricte_ATC}})</th>
	  </tr>  
	  <tr>
	    <th>CIP</th>
	    <th>UCD</th>
	    <th>Produit</th>
	    <th>Laboratoire</th>
	  </tr>
	  {{foreach from=$equivalents_strictes_ATC item=_produit}}
	    {{include file="../../dPmedicament/templates/inc_vw_produit.tpl" produit=$_produit->_ref_produit nodebug=true}}
	  {{foreachelse}}
	  <tr>
	    <td colspan="4">Aucun équivalent</td>
	  </tr>
	  {{/foreach}}
	</table>
	
	{{if $inLivret}}
	<!-- Equivalents Therapeutiques (niveau 2) ATC -->
	<table class="tbl" id="equivalents_therapeutiques_ATC" style="display: none;">
	  <tr>
	    <th colspan="4">Equivalents thérapeutiques ATC<br />{{$libelle_thera_ATC}} ({{$code_thera_ATC}})</th>
	  </tr>  
	  <tr>
	    <th>CIP</th>
	    <th>UCD</th>
	    <th>Produit</th>
	    <th>Laboratoire</th>
	  </tr>
	  {{foreach from=$equivalents_thera_ATC item=_produit}}
	    {{include file="../../dPmedicament/templates/inc_vw_produit.tpl" produit=$_produit->_ref_produit nodebug=true}}
	  {{foreachelse}}
	  <tr>
	    <td colspan="4">Aucun équivalent</td>
	  </tr>
	  {{/foreach}}
	</table>
	{{/if}}
	
	<div id="recherche_produit">
		<form action="?" method="get" name="searchProd">
	    <input type="text" style="width: 350px;" name="produit" class="autocomplete" autofocus="autofocus"/>
	    <label title="Recherche dans le livret thérapeutique">
	      <input type="checkbox" value="1" name="_recherche_livret"
	        {{if $line->_ref_prescription->type == "sejour"}}checked="checked"{{/if}} /> Livret Thérap.
	    </label>
	    <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
	  </form>
	</div>