<script type="text/javascript">

function setClose(code, line_id) {
  var oSelector = window.opener.EquivSelector;
  oSelector.set(code, line_id);
  if(oSelector.selfClose) {
    window.close();
  }
}

function viewProduit(cip){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_produit");
  url.addParam("CIP", cip);
  url.popup(815, 620, "Descriptif produit");
}

Main.add(function () {
  // Initialisation des onglets du menu
  Control.Tabs.create('tabs-equivalent', false);
});

</script>

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