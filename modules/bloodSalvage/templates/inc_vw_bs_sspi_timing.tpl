{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="timing{{$blood_salvage->_id}}" action="?m={{$m}}" method="post">
<input type="hidden" name="m" value="bloodSalvage" />
<input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
<input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
<input type="hidden" name="operation_id" value="{{$blood_salvage->operation_id}}" />
<input type="hidden" name="del" value="0" />
<table class="form">
  <tr>
    <th class="category" colspan="6">Timing</th>
  </tr>
	{{assign var=submit value=submitBloodSalvageTiming}}
	{{assign var=blood_salvage_id value=$blood_salvage->_id}}
	{{assign var=form value=timing$blood_salvage_id}}
  <tr>
    {{include file=../../dPsalleOp/templates/inc_field_timing.tpl object=$blood_salvage field=_recuperation_end}}
    {{include file=../../dPsalleOp/templates/inc_field_timing.tpl object=$blood_salvage field=_transfusion_start}}
    {{include file=../../dPsalleOp/templates/inc_field_timing.tpl object=$blood_salvage field=_transfusion_end}}
  </tr>
</table>
</form>
