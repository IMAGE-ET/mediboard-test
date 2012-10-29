{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script type="text/javascript">
  CorrespondantModele = {
    editCorrespondant: function(correspondant_id) {
      var url = new Url("patients", "ajax_edit_correspondant_modele");
      url.addParam("correspondant_id", correspondant_id);
      url.requestModal(600, 400);
    },

    refreshList: function(correspondant_id) {
      var url = new Url("patients", "ajax_list_correspondants_modele");
      if (correspondant_id) {
        url.addParam("correspondant_id", correspondant_id);
      }
      url.requestUpdate("list_correspondants");
    },

    afterSave: function(correspondant_id) {
      Control.Modal.close();
      CorrespondantModele.refreshList(correspondant_id ? correspondant_id : null);
    },

    updateSelected: function(elt) {
      $("list_correspondants").select("tr").invoke("removeClassName", "selected");
      if (elt) {
        elt.addClassName("selected");
      }
    }
  };

  Main.add(function() {
    CorrespondantModele.refreshList();
  });

  function popupImport() {
  var url = new Url('patients', 'assurance_import_csv');
  url.popup(800, 600, 'Import des assurances');
  }
</script>

<button type="button" class="new" onclick="CorrespondantModele.editCorrespondant(0)">
  {{tr}}CCorrespondant-title-create{{/tr}}
</button>

<button type="button" class="upload" onclick="popupImport();" class="hslip">{{tr}}Import-CSV{{/tr}}</button>

<table class="tbl">
  <tr>
    <th>{{mb_title class=CCorrespondantPatient field=nom}}</th>
    <th>{{mb_title class=CCorrespondantPatient field=prenom}}</th>
    <th>{{mb_title class=CCorrespondantPatient field=naissance}}</th>
    <th>{{mb_title class=CCorrespondantPatient field=adresse}}</th>
    <th>{{mb_title class=CCorrespondantPatient field=cp}}/{{mb_title class=CCorrespondantPatient field=ville}}</th>
    <th>{{mb_title class=CCorrespondantPatient field=tel}}</th>
    <th>{{mb_title class=CCorrespondantPatient field=mob}}</th>
    <th>{{mb_title class=CCorrespondantPatient field=fax}}</th>
    <th>{{mb_title class=CCorrespondantPatient field=relation}}</th>
    {{if $conf.ref_pays == 1}}
      <th>{{mb_title class=CCorrespondantPatient field=urssaf}}</th>
    {{/if}}
    <th>{{mb_title class=CCorrespondantPatient field=email}}</th>
    <th>{{mb_title class=CCorrespondantPatient field=remarques}}</th>
  </tr>
  <tbody id="list_correspondants"></tbody>
</table>
