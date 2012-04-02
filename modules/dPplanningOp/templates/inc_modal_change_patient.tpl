{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=patient_id_config value=$conf.dPplanningOp.CSejour.patient_id}}

<script type="text/javascript">
  confirmChangePatient = function() {
    var modalConfirm = modal($("confirmChangePatientModal"));
    modalConfirm.position();
  } 
</script>

<div id="confirmChangePatientModal" style="display: none; width: 400px;"
     class="{{if $patient_id_config == 0 || $patient_id_config == 2}}
              big-info
            {{else}}
              big-warning
            {{/if}}
            text modal">
  <table class="form">
    <tr>
      <th class="title" colspan="2">
        {{tr}}CPatient.change{{/tr}}
      </th>
    </tr>
    <tr>
      <td colspan="2" class="text">
      {{if $app->user_type == 1 || !$sejour->_id || $patient_id_config == 1 || ($patient_id_config == 2 && !$sejour->entree_reelle)}}
        {{tr}}CPatient.change_patient_id_1{{/tr}}
      {{elseif $patient_id_config == 0 && $sejour->_id}}
        {{tr}}CPatient.change_patient_id_0{{/tr}}
      {{elseif $patient_id_config == 2 && $sejour->entree_reelle}}
        {{tr}}CPatient.change_patient_id_2{{/tr}}
      {{/if}}
      </td>
    </tr>
    <tr>
      {{if $app->user_type == 1 || !$sejour->_id || $patient_id_config == 1 || ($patient_id_config == 2 && !$sejour->entree_reelle)}}
        <td style="text-align: center;">
          <a class="tick button" onclick="Control.Modal.close(); PatSelector.init()">{{tr}}Yes{{/tr}}</a>
        </td>
        <td style="text-align: center;">
          <a class="cancel button" onclick="Control.Modal.close()">{{tr}}No{{/tr}}</a>
        </td>
      {{elseif ($patient_id_config == 0 && $sejour->_id) || ($patient_id_config == 2 && $sejour->entree_reelle)}}
        <td colspan="2" style="text-align: center;">
          <a class="cancel button" onclick="Control.Modal.close()">{{tr}}Close{{/tr}}</a>
        </td>
      {{else}}
        
      {{/if}}
    </tr>
  </table>
</div>