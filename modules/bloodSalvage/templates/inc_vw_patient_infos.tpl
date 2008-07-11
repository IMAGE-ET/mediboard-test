<table class="form">
	<tr>
		<th class="category" colspan="6">Infos patient</th>
	</tr>
	{{assign var=consult_anesth value=$selOp->_ref_consult_anesth}}
	{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
 	<tr>
  	<th style="width: 16%"><strong> Groupe sanguin : </strong></th><td style="width: 16%"><!-- {{$dossier_medical->groupe_sanguin}} {{$dossier_medical->rhesus}}  --></td>
    <th style="width: 16%"><strong> RAI : </strong></th><td style="width: 16%">{{$consult_anesth->rai}} </td>
    <th style="width: 16%"><strong> ASA : </strong></th><td style="width: 16%">{{$consult_anesth->ASA}}</td>
 	</tr>
 	<tr>
	  <th><strong> Poids : </strong></th><td>{{$patient->_ref_constantes_medicales->poids}}</td>
	  <th><strong> Taille : </strong></th><td>{{$patient->_ref_constantes_medicales->taille}}</td>
	  <th><strong> IMC : </strong></th><td>{{$patient->_ref_constantes_medicales->_imc}}</td>
	</tr>
</table>