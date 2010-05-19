{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="form">
  <tr>
    <th colspan="5" class="category">
      Ajouter une transmission
    </th>
  </tr>
  <tr>
    <td colspan="5">
			<form name="editTrans" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
				<input type="hidden" name="dosql" value="do_transmission_aed" />
				<input type="hidden" name="del" value="0" />
				<input type="hidden" name="m" value="dPhospi" />
				<input type="hidden" name="object_class" value="CPrescriptionLineMix" />
				<input type="hidden" name="object_id" value="{{$prescription_line_mix->_id}}" />
				<input type="hidden" name="sejour_id" value="{{$sejour_id}}" />
				<input type="hidden" name="user_id" value="{{$app->user_id}}" />
				<input type="hidden" name="date" value="now" />
				{{mb_label object=$transmission field="text"}}
				{{mb_field object=$transmission field="degre"}}
				<br />
				{{mb_field object=$transmission field="text"}}
			</form>
	  </td>
	</tr>
	<tr>
	  <td colspan="5">
	    <button type="button" class="add" onclick="submitTransmissions();">{{tr}}Add{{/tr}}</button>
	  </td>
	</tr>
</table>
<table class="tbl">
	<tr>
	  <th colspan="5">
	    Liste des transmissions
	  </th>
	</tr>
	<tr>
    <th>Type</th>
    <th>Utilisateur</th>
    <th>Date</th>
    <th>Heure</th>
    <th>Texte</th>
  </tr>
  {{assign var=date value=""}}
  {{foreach from=$transmissions item=_transmission}}
  <tr>
    <td>Transmission</td>
    <td>{{$_transmission->_ref_user->_view}}</td>
   <td  style="text-align: center">
      {{if $date != $_transmission->date|date_format:"%d/%m/%Y"}}
        {{$_transmission->date|date_format:"%d/%m/%Y"}}
      {{else}}
        &mdash;
      {{/if}}    
    </td>
    <td>
      {{$_transmission->date|date_format:$dPconfig.time}}
    </td>
    <td class="text" colspan="2">
      <div {{if $_transmission->degre == "high"}}style="background-color: #faa"{{/if}}>
	      {{if $_transmission->object_id}}
	      <em>Cible : {{$_transmission->_ref_object->_view}}</em><br />
	      {{/if}}
        {{$_transmission->text|nl2br}}
      </div>
    </td>
  </tr>
  {{assign var=date value=$_transmission->date|date_format:"%d/%m/%Y"}}
  {{/foreach}}
</table>