{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
loadDCI = function(DC_search, DCI_code, dialog, forme, dosage){
  var oForm = document.rechercheDCI;  
  var url = new Url("dPmedicament", "httpreq_vw_DCI");
  url.addParam("DC_search", DC_search);
  url.addParam("DCI_code", DCI_code);
  url.addParam("dialog", dialog);
  url.addParam("forme", forme);
  url.addParam("dosage", dosage);
  url.addParam("rechercheLivretDCI",$V(oForm.rechercheLivretDCI));
  url.requestUpdate("DCI", { waitingText: null } );
}
</script>

<table class="form">
  <tr>
    <td>
      <form name="rechercheDCI" action="?" method="get" onsubmit="return false;">
        Dénomination commune
        <input type="text" name="DCI" value="{{$DC_search}}" />
        <button type="button" class="search" onclick="loadDCI(this.form.DCI.value, '', '{{$dialog}}');">Rechercher</button>
        <br />
        <br />
        <input type="checkbox" name="rechercheLivretDCI" value="1" {{if $rechercheLivretDCI == 1}}checked = "checked"{{/if}} />
        Rechercher uniquement dans le livret thérapeutique
      </form>
    </td>
  </tr>
</table>

{{if $tabDCI}}
<table class="tbl">
  <tr>
    <th>{{$tabDCI|@count}} DCI trouvées</th>
  </tr>
  {{foreach from=$tabDCI item=_DCI}}
  <tr>
    <td><a href="#" onclick="loadDCI('', '{{$_DCI->Code}}', '{{$dialog}}', '')">{{$_DCI->Libelle}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}

{{if $DCI_code && !$tabViewProduit}}
<table class="tbl">
  <tr>
    <th {{if $dialog}}colspan="3"{{else}}colspan="2"{{/if}}>{{$DCI->_ref_produits|@count}} produits trouvés</th>
  </tr>
  {{foreach from=$tabProduit item=dosageProduit key="dosage"}}
      <tr>
        <th style="width: 50px" rowspan={{$dosageProduit|@count}} {{if $dialog}}colspan="2"{{/if}} class="title">{{$dosage}}</th>
        {{foreach from=$dosageProduit item="formeProduit" key="forme" name="forme_foreach"}}
          <td><a href="#" onclick="loadDCI('', '{{$DCI_code}}', '{{$dialog}}', '{{$forme}}', '{{$dosage}}')">{{$forme}}</a></td>
        </tr>
    {{/foreach}}
  {{/foreach}}
</table>
{{/if}}

{{if $tabViewProduit}}
<table class="tbl">
  <tr>
    <th {{if $dialog}}colspan="2"{{/if}}>{{$tabViewProduit|@count}} produits trouvés</th>
  </tr>
  {{foreach from=$tabViewProduit item=_produit}}
  <tr>
    {{if $dialog}}
    <td style="width: 1%;">
      <button type="button" class="add notext" onclick="setClose('{{$_produit->Libelle}}', '{{$_produit->CIP}}')"></button>
    </td>
    {{/if}}
    <td>
    <a href="#produit{{$_produit->CIP}}" onclick="Prescription.viewProduit('{{$_produit->CIP}}')">{{$_produit->Libelle}}</a>
    </td>
  </tr>  
  {{/foreach}}
</table>
{{/if}}