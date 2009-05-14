{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
loadComposants = function(composant, code, libelle, dialog){
  var oForm = document.rechercheComposant;
  
  var url = new Url;
  url.setModuleAction("dPmedicament", "httpreq_vw_composants");
  url.addParam("composant", composant);
  url.addParam("code", code);
  url.addParam("libelle", libelle);
  url.addParam("dialog", dialog);
  url.addParam("rechercheLivretComposant", $V(oForm.rechercheLivretComposant));
  url.requestUpdate("composants", { waitingText: null } );
}
</script>

<table class="form">
  <tr>
    <td>
      <form name="rechercheComposant" action="?" method="get" onsubmit="return false;">
        Composant recherché
        <input type="text" name="composant" value="{{$composant}}" />
        <button type="button" class="search" onclick="loadComposants(this.form.composant.value, '', '', '{{$dialog}}')">Rechercher</button>
        <br />
        <br />
        <input type="checkbox" name="rechercheLivretComposant" value="1" {{if $rechercheLivretComposant == 1}}checked = "checked"{{/if}} />
        Rechercher uniquement dans le livret thérapeutique
      </form>
    </td>
  </tr>
</table>

{{if $composant && $composition}}
<table class="tbl">
  <tr>
    <th>{{$composition->_ref_composants|@count}} compositions trouvées</th>
  </tr>
  {{foreach from=$composition->_ref_composants item="_composant"}}
    <tr>
      <td><a href="#" onclick="loadComposants('', '{{$_composant->Code}}', '{{$_composant->Libelle}}', '{{$dialog}}')">{{$_composant->Libelle}}</a></td>
    </tr>
  {{/foreach}}
</table>
{{/if}}

{{if $code}}
<table class="tbl">
  <tr>
    <th {{if $dialog}}colspan="2"{{/if}}>{{$composition->_ref_produits|@count}} produits trouvés contenant {{$libelle}}</th>
  </tr>
  {{foreach from=$composition->_ref_produits item="_produit"}}
    <tr>
      {{if $dialog}}
      <td style="width: 1%;">
        <button type="button" class="add notext" onclick="setClose('{{$_produit->Libelle}}', '{{$_produit->CodeCIP}}')"></button>
      </td>
      {{/if}}
      <td>
        <a href="#produit{{$_produit->CodeCIP}}" onclick="Prescription.viewProduit('{{$_produit->CodeCIP}}')">
        {{$_produit->Libelle}}
        </a>
      </td>
    </tr>
  {{/foreach}}
</table>
{{/if}}