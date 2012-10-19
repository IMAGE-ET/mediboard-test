{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{foreach from=$correspondants item=_correspondant}}
  <tr {{if $_correspondant->_id == $correspondant_id}}class="selected"{{/if}}>
    <td>
      <a href="#1" onclick="CorrespondantModele.updateSelected(this.up('tr')); CorrespondantModele.editCorrespondant('{{$_correspondant->_id}}')">
        {{mb_value object=$_correspondant field=nom}}
      </a>
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
    <td>{{mb_value object=$_correspondant field=email}}</td>
    <td>
      {{mb_value object=$_correspondant field=remarques}}
      {{mb_value object=$_correspondant field=ean}}
    </td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="12">{{tr}}CCorrespondantPatient.none{{/tr}}</td>
  </tr>
{{/foreach}}