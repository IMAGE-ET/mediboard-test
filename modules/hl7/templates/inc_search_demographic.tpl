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

<form name="filter-pat-demographic-supplier" method="get" onsubmit="return TestHL7.refreshListDemographicSupplier(this)">
  <input type="hidden" name="m" value="hl7" />
  <input type="hidden" name="a" value="ajax_list_demographic" />
  <input type="hidden" name="page" value="0" />

  <table class="form">
    <tr>
      <th class="title" colspan="2">Critères de recherche</th>
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
      <td class="button" colspan="2">
        <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
<br/>
<div id="list_demographic"></div>