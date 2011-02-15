{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function startAssociation(){
  var url = new Url("dPprescription", "httpreq_do_add_table_association");
  url.requestUpdate("do_add_association");
}

function exportElementsPrescription(){
  var url = new Url("dPprescription", "httpreq_export_elements_prescription");
  url.addParam("group_id", $V(document.exportElements.group_id));
  url.requestUpdate("export_elements_prescription");
}

function importElementsPrescriptionXML(){
  var url = new Url("dPprescription", "import_elements_prescription");
  url.popup(700, 500, "export_elements_prescription_xml");
}

function importElementsPrescriptionCSV(){
  var url = new Url("dPprescription", "import_elements_prescription_csv");
  url.popup(700, 500, "export_elements_prescription_csv");
}

function updateVoie(){
  var url = new Url("dPprescription", "httpreq_update_voie");
  url.requestUpdate("update_voie");
}

function updateUCD(){
  var url = new Url("dPprescription", "httpreq_update_ucd");
  url.requestUpdate("update_ucd");
}

function updateCIS(){
  var url = new Url("dPprescription", "httpreq_update_cis");
  url.requestUpdate("update_cis");
}

/*
function onchangeMed(radioButton, other_field){
  var oForm = getForm("editConfig");
	if(radioButton.value){
	  $V(oForm.elements["dPprescription[CPrescription]["+other_field+"]"], "0", false);
  }
}
*/
</script>

<!-- Imports/Exports -->
<table class="tbl">
  <tr>
    <th>{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>
  <tr>
    <td><button class="tick" onclick="startAssociation()" >Importer la table d'association</button></td>
    <td id="do_add_association"></td>
  </tr>
  <tr>
    <td>
	    <button class="tick" onclick="exportElementsPrescription()" >Exporter les elements de prescriptions</button>
	    <form name="exportElements" action="#">
	      <select name="group_id">
	        <option value="no_group">Non associées</option>
	        {{foreach from=$groups item=_group}}
	          <option value="{{$_group->_id}}">de {{$_group->_view}}</option>
	        {{/foreach}}
	      </select>
      </form>
    </td>
    <td id="export_elements_prescription"></td>
  </tr>
  <tr>
    <td colspan="2">
    	<button class="tick" onclick="importElementsPrescriptionXML()" >Importer les elements de prescriptions XML</button>
			<button class="tick" onclick="importElementsPrescriptionCSV()" >Importer les elements de prescriptions CSV</button>
	  </td>
  </tr>
  <tr>
    <td><button class="tick" onclick="updateVoie()">Mettre à jour la voie pour les lignes de medicaments</button></td>
    <td id="update_voie"></td>
  </tr>
  <tr>
    <td><button class="tick" onclick="updateUCD()">Mettre à jour les code UCD et CIS (mise à jour initiale)</button></td>
    <td id="update_ucd"></td>
  </tr>
  <tr>
    <td><button class="tick" onclick="updateCIS()">Mettre à jour les codes CIS</button></td>
    <td id="update_cis"></td>
  </tr>
</table>