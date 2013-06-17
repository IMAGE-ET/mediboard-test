{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}




<tr>
  <th class="narrow"></th>
  {{if $conf.ref_pays == 2}}
    <th>{{mb_title class=CCorrespondantPatient field=surnom}}</th>
  {{/if}}
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
  {{if $conf.ref_pays == 2}}
    <th>{{mb_title class=CCorrespondantPatient field=ean}}</th>
    <th>{{mb_title class=CCorrespondantPatient field=type_pec}}</th>
  {{/if}}
  <th>{{mb_title class=CCorrespondantPatient field=email}}</th>
  <th>{{mb_title class=CCorrespondantPatient field=remarques}}</th>
</tr>

{{foreach from=$correspondants item=_correspondant}}

  <tr {{if $_correspondant->_id == $correspondant_id}}class="selected"{{/if}}>
    <td class="narrow"><button class="button notext edit" onclick="CorrespondantModele.editCorrespondant('{{$_correspondant->_id}}')">{{tr}}Edit{{/tr}}</button></td>
    {{if $conf.ref_pays == 2}}
      <td>
        {{mb_value object=$_correspondant field=surnom }}
      </td>
    {{/if}}
    <td>
      {{mb_value object=$_correspondant field=nom}}
    </td>
    <td>
      {{mb_value object=$_correspondant field=prenom}}
    </td>
    <td>
      {{mb_value object=$_correspondant field=naissance}}
    </td>
    <td>
      {{mb_value object=$_correspondant field=adresse}}
    </td>
    <td>
      {{mb_value object=$_correspondant field=cp}}
      {{mb_value object=$_correspondant field=ville}}
    </td>
    <td>
      {{mb_value object=$_correspondant field=tel}}
    </td>
    <td>
      {{mb_value object=$_correspondant field=mob}}
    </td>
    <td>
      {{mb_value object=$_correspondant field=fax}}
    </td>
    <td>
      {{if $_correspondant->relation != "employeur"}}
        {{if $_correspondant->parente == "autre"}}
          {{mb_value object=$_correspondant field=parente_autre}}
        {{else}}
          {{mb_value object=$_correspondant field=parente}}
        {{/if}}
      {{/if}}
    </td>
    {{if $conf.ref_pays == 1}}
      <td>
        {{if $_correspondant->relation == "employeur"}}
          {{mb_value object=$_correspondant field=urssaf}}
        {{/if}}
      </td>
    {{/if}}
    {{if $conf.ref_pays == 2}}
      <td>{{mb_value object=$_correspondant field=ean}}</td>
      <td>{{mb_value object=$_correspondant field=type_pec}}</td>
    {{/if}}
    <td>{{mb_value object=$_correspondant field=email}}</td>
    <td>
      {{mb_value object=$_correspondant field=remarques}}
    </td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="12">{{tr}}CCorrespondantPatient.none{{/tr}}</td>
  </tr>
{{/foreach}}