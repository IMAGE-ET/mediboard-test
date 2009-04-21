{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $_acte->_class_name == "CActeCCAM"}}
<tr>
  <td>{{$_acte->_ref_executant->_view}}</td>
  <td>{{mb_value object=$_acte field=execution}}</td>
  <td>{{mb_value object=$_acte field=code_acte}}</td>
  <td>{{mb_value object=$_acte field=code_activite}}</td>
  <td>{{mb_value object=$_acte field=code_phase}}</td>
  <td>{{mb_value object=$_acte field=modificateurs}}</td>
  <td>{{mb_value object=$_acte field=rembourse}}</td>
  <td>{{mb_value object=$_acte field=code_association}}</td>
  <td>{{mb_value object=$_acte field=montant_base}}</td>
  <td>{{mb_value object=$_acte field=montant_depassement}}</td>
  <td>{{mb_value object=$_acte field=signe}}</td>
  <td>
    {{assign var=acte_id value=$_acte->_id}}
    {{if $detCCAM.$acte_id}}
    <div class="error">{{$detCCAM.$acte_id}}</div>
    {{else}}
    <div class="message">
      Détail CCAM correctement exporté
    </div>
    {{/if}}
  </td>
</tr>
{{/if}}

{{if $_acte->_class_name == "CActeNGAP"}}
<tr>
  <td>{{$_acte->_ref_praticien->_view}}</td>
  <td>{{mb_value object=$_acte field=_execution}}</td>
  <td>{{mb_value object=$_acte field=_shortview}}</td>
  <td>-</td>
  <td>-</td>
  <td>{{mb_value object=$_acte field=complement}}</td>
  <td>-</td>
  <td>-</td>
  <td>-</td>
  <td>{{mb_value object=$_acte field=montant_depassement}}</td>
  <td>-</td>
  <td>
    {{assign var=acte_id value=$_acte->_id}}
    {{if $actNGAP.$acte_id}}
    <div class="error">{{$actNGAP.$acte_id}}</div>
    {{else}}
    <div class="message">
      Acte NGAP correctement exporté
    </div>
    {{/if}}
  </td>
</tr>
{{/if}}

