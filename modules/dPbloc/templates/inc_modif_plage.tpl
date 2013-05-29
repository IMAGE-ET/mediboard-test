{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  Main.add(function(){
    var oForm = getForm("editPlageTiming");
    var options = {
      exactMinutes: false, 
      minInterval : {{"CPlageOp"|static:minutes_interval}},
      minHours    : {{"CPlageOp"|static:hours_start}},
      maxHours    : {{"CPlageOp"|static:hours_stop}}
    };
    Calendar.regField(oForm.debut, null, options);
    Calendar.regField(oForm.fin  , null, options);
    options = {
      exactMinutes: false
    };
    Calendar.regField(oForm.temps_inter_op, null, options);
  });
</script>

<form name="editPlageTiming" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)" class="{{$plage->_spec}}">
  <input type="hidden" name="m" value="dPbloc" />
  <input type="hidden" name="dosql" value="do_plagesop_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
  <input type="hidden" name="_repeat" value="1" />
  <input type="hidden" name="_type_repeat" value="simple" />
  <table class="form">
    <tr>
      <th>{{mb_label object=$plage field="debut"}}</th>
      <td>{{mb_field object=$plage field="debut" hidden=true onchange="submitFormAjax(this.form, 'systemMsg', {onComplete: reloadModifPlage});"}}</td>
      <td><button type="button" class="edit" onclick="EditPlanning.edit('{{$plage->_id}}', '{{$plage->date}}');">Modification avancée</button></td>
    </tr>
    <tr>
      <th>{{mb_label object=$plage field="fin"}}</th>
      <td>{{mb_field object=$plage field="fin" hidden=true onchange="submitFormAjax(this.form, 'systemMsg', {onComplete: reloadModifPlage});"}}</td>
      <td><button type="button" class="search" onclick="EditPlanning.monitorDaySalle('{{$plage->_ref_salle->_id}}', '{{$plage->date}}');">Voir sur plusieurs semaines</button></td>
    </tr>
    <tr>
      <th>{{mb_label object=$plage field="temps_inter_op"}}</th>
      <td>{{mb_field object=$plage field="temps_inter_op" hidden=true onchange="submitFormAjax(this.form, 'systemMsg', {onComplete: reloadModifPlage});"}}</td>
    </tr>
  </table>
</form>
