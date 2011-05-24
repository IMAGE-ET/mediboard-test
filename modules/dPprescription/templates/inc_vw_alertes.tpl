{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
	<tr>
		<th class="title" colspan="2">
			{{$alertes|@count}} alertes
			<form name="closeAlertes-{{$level}}-{{$prescription_id}}" action="?" method="post" 
			      onSubmit="return onSubmitFormAjax(this, { onComplete: function(){ refreshLineSejour('{{$sejour_id}}'); $('tooltip-content-alertes-{{$level}}-{{$sejour_id}}').hide(); } })">
				<input type="hidden" name="m" value="dPprescription" />
				<input type="hidden" name="dosql" value="do_close_all_alertes_aed" />
				<input type="hidden" name="prescription_id" value="{{$prescription_id}}" />
				<input type="hidden" name="level" value="{{$level}}" />
			  <button type="submit" class="tick">
          Traiter toutes les alertes
        </button>
			</form>
		</th>
  </tr>
{{foreach from=$alertes item=_alerte}}
  <tr>
    <td class="narrow">
      <form name="editAlert-{{$level}}-{{$_alerte->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ refreshLineSejour('{{$sejour_id}}'); $('tooltip-content-alertes-{{$level}}-{{$sejour_id}}').up('.tooltip').remove(); } })">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_alert_aed" />
        <input type="hidden" name="alert_id" value="{{$_alerte->_id}}" />
        <input type="hidden" name="handled" value="1" />
        <button type="submit" class="tick notext">Traiter</button>
      </form>
    </td>
    <td class="text compact">
      {{mb_value object=$_alerte field=comments}}
		</td>
	</tr>
{{/foreach}}
</table>