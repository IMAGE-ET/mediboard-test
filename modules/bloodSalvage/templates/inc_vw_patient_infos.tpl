{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="form">
	<tr>
		<th class="category" colspan="6">Infos patient</th>
	</tr>
	{{assign var=consult_anesth value=$selOp->_ref_consult_anesth}}
	{{assign var=constantes value=$patient->_ref_constantes_medicales}}
 	<tr>
  	<th style="width: 16%; font-weight: bold;">{{mb_label object=$consult_anesth field=groupe}}</th>
    <td style="width: 16%">{{$consult_anesth->groupe}} {{$consult_anesth->rhesus}}</td>
    <th style="width: 16%; font-weight: bold;">{{mb_label object=$consult_anesth field=rai}}</th>
    <td style="width: 16%">{{$consult_anesth->rai}} </td>
    <th style="width: 16%; font-weight: bold;">{{mb_label object=$consult_anesth field=ASA}}</th>
    <td style="width: 16%">{{$consult_anesth->ASA}}</td>
 	</tr>
 	<tr>
	  <th style="font-weight: bold">{{mb_label object=$constantes field=poids}}</th>
    <td>{{mb_value object=$constantes field=poids}} kg</td>
	  <th style="font-weight: bold">{{mb_label object=$constantes field=taille}}</th>
    <td>{{mb_value object=$constantes field=taille}} cm</td>
	  <th style="font-weight: bold">{{mb_label object=$constantes field=_imc}}</th>
    <td>{{mb_value object=$constantes field=_imc}}</td>
	</tr>
  <tr>
    <th style="font-weight: bold">{{mb_label object=$consult_anesth field="_psa"}}</th>
    <td> {{mb_value object=$consult_anesth field="_psa"}} ml/GR</td>
    <th style="font-weight: bold">{{mb_label object=$constantes field="_vst"}}</th>
    <td colspan="3">{{mb_value object=$constantes field="_vst"}} ml</td>
  </tr>
</table>