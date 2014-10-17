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

{{mb_include module=hl7 template=inc_banner_patient_hl7}}

<form name="filter-pat-encounter-supplier" method="get" onsubmit="return TestHL7.refreshListEncounterSupplier(this)">
  <input type="hidden" name="m" value="hl7" />
  <input type="hidden" name="a" value="ajax_list_encounter" />
  <input type="hidden" name="page" value="0" />
  <fieldset>
    <legend>Critère de recherche</legend>
    <table class="form">
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
        <td class="button" colspan="2">
          <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
        </td>
      </tr>
    </table>
  </fieldset>
</form>
<br/>
<div id="list_encounter"></div>