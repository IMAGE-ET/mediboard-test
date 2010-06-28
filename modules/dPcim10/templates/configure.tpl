{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{include file="../../system/templates/configure_dsn.tpl" dsn=cim10}}

<script type="text/javascript">

function startCIM10() {
  var url = new Url;
  url.setModuleAction("dPcim10", "httpreq_do_add_cim10");
  url.requestUpdate("cim10");
}

function diffCIM10Atih(del) {
  var url = new Url;
  url.setModuleAction("dPcim10", "httpreq_diff_cim10_atih");
  if (del) {
    url.addParam('do_delete', true);
  }
  url.requestUpdate(del ? "cim10_remove" : "cim10_add");
}

</script>

<h2>Import de la base de données CIM10</h2>

<table class="tbl">

<tr>
  <th>{{tr}}Action{{/tr}}</th>
  <th>{{tr}}Status{{/tr}}</th>
</tr>
  
<tr>
  <td><button class="tick" onclick="startCIM10()">Importer la base de données CIM10</button></td>
  <td id="cim10" />
</tr>

<tr>
  <td><button class="tick" onclick="diffCIM10Atih()">Ajouter les modifications de l'ATIH</button></td>
  <td id="cim10_add" />
</tr>

<tr>
  <td><button class="tick" onclick="diffCIM10Atih(true)">Supprimer les modifications de l'ATIH</button></td>
  <td id="cim10_remove" />
</tr>

</table>