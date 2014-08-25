{{*
 * $Id$
 *
 * @category test
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_include module=system template=inc_pagination total=$nb_pat current=$page change_page="TestHL7.changePageListDemographicSupplier" step=30}}

<table class="tbl">
  <tr>
    <th colspan="6" class="title">Données patients</th>
    <th colspan="4" class="title">{{tr}}Actions{{/tr}}</th>
  </tr>

  <tr>
    <th style="width: 20%;">{{mb_title object=$patient field=nom}}</th>
    <th style="width: 20%;">{{mb_title object=$patient field=prenom}}</th>
    <th style="width: 20%;">{{mb_title object=$patient field=nom_jeune_fille}}</th>
    <th style="width: 10%;">{{mb_title object=$patient field=sexe}}</th>
    <th>{{mb_title object=$patient field=_IPP}}</th>
    <th>Créateur</th>
    <th class="narrow">A28</th>
    <th class="narrow">A31</th>
    <th class="narrow">A47</th>
    <th class="narrow">A40</th>
  </tr>

  {{foreach from=$patients item=_patient}}
    <tr>
      <td>
        <span  onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}')">
          {{mb_value object=$_patient field=nom}}
        </span>
      </td>
      <td>{{mb_value object=$_patient field=prenom}}</td>
      <td>{{mb_value object=$_patient field=nom_jeune_fille}}</td>
      <td>{{mb_value object=$_patient field=sexe}}</td>
      <td>{{mb_value object=$_patient field=_IPP}}</td>
      <td>
        <span  onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_ref_first_log->_guid}}')">
          {{$_patient->_ref_first_log->_ref_user->_view}}
        </span>
      </td>
      <td>
        <button type="button" class="compact send notext"
                onclick="TestHL7.sendA28('{{$_patient->_id}}')">
          A28
        </button>
      </td>
      <td>
        <button type="button" class="compact edit notext" onclick="TestHL7.sendA31('{{$_patient->_id}}')">
          A31
        </button>
      </td>
      <td>
        <button type="button" class="compact idex notext" onclick="guid_ids('{{$_patient->_guid}}')">
          A47
        </button>
      </td>
      <td>
        <input type="checkbox" name="merge_patient_id" value="{{$_patient->_id}}" onclick="TestHL7.handleMergeClick(this.form)"/>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}CPatient.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>