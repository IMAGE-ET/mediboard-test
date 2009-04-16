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
	{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
 	<tr>
 	  {{if $isInDM}}
  	<th style="width: 16%"><strong> {{mb_label object=$dossier_medical field=groupe_sanguin}} : </strong></th><td style="width: 16%">{{$dossier_medical->groupe_sanguin}} {{$dossier_medical->rhesus}}</td>
  	{{else}}
  	<th style="width: 16%"><strong> {{mb_label object=$consult_anesth field=groupe}} : </strong></th><td style="width: 16%">{{$consult_anesth->groupe}} {{$consult_anesth->rhesus}}</td>
    {{/if}}
    <th style="width: 16%"><strong> {{mb_label object=$consult_anesth field=rai}} : </strong></th><td style="width: 16%">{{$consult_anesth->rai}} </td>
    <th style="width: 16%"><strong> {{mb_label object=$consult_anesth field=ASA}} : </strong></th><td style="width: 16%">{{$consult_anesth->ASA}}</td>
 	</tr>
 	<tr>
	  <th><strong> {{mb_label object=$patient->_ref_constantes_medicales field=poids}} : </strong></th><td>{{$patient->_ref_constantes_medicales->poids}} kg</td>
	  <th><strong> {{mb_label object=$patient->_ref_constantes_medicales field=taille}}: </strong></th><td>{{$patient->_ref_constantes_medicales->taille}} cm</td>
	  <th><strong> {{mb_label object=$patient->_ref_constantes_medicales field=_imc}}: </strong></th><td>{{$patient->_ref_constantes_medicales->_imc}}</td>
	</tr>
  <tr>
    <th><strong>{{mb_label object=$consult_anesth field="_psa"}} : </strong></th><td> {{mb_value object=$consult_anesth field="_psa"}} ml/GR</td>
    <th><strong>{{mb_label object=$patient->_ref_constantes_medicales field="_vst"}} : </strong></th><td colspan="3"> {{mb_value object=$patient->_ref_constantes_medicales field="_vst"}} ml</td>
    
  </tr>
</table>