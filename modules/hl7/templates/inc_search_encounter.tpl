{{*
 * $Id$
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<form name="filter-pat-encounter-supplier" method="get" onsubmit="return TestHL7.refreshListEncounterSupplier(this)">
  <input type="hidden" name="m" value="hl7" />
  <input type="hidden" name="a" value="ajax_list_encounter" />
  <input type="hidden" name="page" value="0" />

  <table class="form">
    <tr>
      <th class="title" colspan="2">Critères de recherche</th>
    </tr>
    <tr>
      <th><label for="event_select">Evenement</label></th>
      <td>
        <select id="event_select" name="event">
          <option value="A01">Admit - INSERT (A01)</option>
          <option value="A11">Admit - CANCEL (A11)</option>
          <option value="event_change_class_inpatient">change patient class to inpatient - INSERT</option>
          <option value="event_change_class_outpatient">change patient class to outpatient - INSERT</option>
          <option value="A03">Discharge patient - INSERT (A03)</option>
          <option value="A13">Discharge patient - CANCEL (A13)</option>
          <option value="event_merge">Merge patient identifier lists -INSERT</option>
          <option value="A05">Pre Admit Patient - INSERT (A05)</option>
          <option value="A15">Pre Admit Patient - CANCEL (A15)</option>
          <option value="A04">Register outpatient - INSERT (A04)</option>
          <option value="A11">Register outpatient - CANCEL (A11)</option>
          <option value="A02">Transfer patient - INSERT (A02)</option>
          <option value="A12">Transfer patient - CANCEL (A12)</option>
          <option value="event_update_info">Update patient information</option>
        </select>
      </td>
    </tr>
    <tr>
      <th>{{mb_title class="CPatient" field=nom}}</th>
      <td>{{mb_field class="CPatient" field=nom}}</td>
    </tr>
    <tr>
      <th>{{mb_title class="CPatient" field=prenom}}</th>
      <td>{{mb_field class="CPatient" field=prenom}}</td>
    </tr>
    <tr>
      <th>{{mb_title class="CPatient" field=nom_jeune_fille}}</th>
      <td>{{mb_field class="CPatient" field=nom_jeune_fille}}</td>
    </tr>
    <tr>
      <th>{{mb_title class="CPatient" field=sexe emptyLabel="All"}}</th>
      <td>{{mb_field class="CPatient" field=sexe emptyLabel="All"}}</td>
    </tr>
    <tr>
      <th>{{mb_label class="CPatient" field="pays"}}</th>
      <td>
        <script>
          Main.add(function () {
            initPaysField("filter-pat-encounter-supplier", "pays")
          });
        </script>
        {{mb_field class="CPatient" field="pays" size="31" prop="str autocomplete"}}
        <div style="display:none;" class="autocomplete" id="pays_auto_complete"></div>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
<br/>
<div id="list_encounter"></div>