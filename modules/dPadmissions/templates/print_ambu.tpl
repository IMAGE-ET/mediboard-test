<table class="tbl">
  <tr>
    <th colspan="10">
      <span style="float: right">
      Service
      <form name="selService" action="" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dialog" value="1" />
        <input type="hidden" name="a" value="{{$a}}" />
	      <select name="service_id" onchange="this.form.submit();">
	        <option value="">&mdash; Tous les services</option>
	        {{foreach from=$services item=_service}}
	        <option value="{{$_service->_id}}" {{if $service_id == $_service->_id}}selected="selected"{{/if}}>{{$_service->_view}}</option>
	        {{/foreach}}
	      </select>
      </form>
      </span>
      <a href="#" onclick="window.print()">Patients en séjour de type Ambulatoire du {{$date|date_format:$dPconfig.date}}</a>
    </th>
  </tr>
  <tr>
    <th>Patient</th>
    <th>Praticien</th>
    <th>Service</th>
    <th>Chambre</th>
    <th>Entrée<br />ambu</th>
    <th>Entrée<br />au bloc</th>
    <th>Sortie<br />de bloc</th>
    <th>Entrée<br />salle de réveil</th>
    <th>Sortie<br />salle de réveil</th>
    <th>Sortie<br />établissement</th>
  </tr>
	{{foreach from=$sejours item=_sejour}}
		<tr>
		  <td class="text">{{$_sejour->_ref_patient->_view}}</td>
		  <td class="text">{{$_sejour->_ref_praticien->_view}}</td>
		  <td class="text">
			  {{foreach from=$_sejour->_ref_affectations item="affectation"}}
	          {{$affectation->_ref_lit->_ref_chambre->_ref_service->_view}}<br />
	      {{/foreach}}
	      {{if !$_sejour->_ref_affectations|@count}}
	        -
	      {{/if}}
	    </td>
		  <td class="text">
			  {{foreach from=$_sejour->_ref_affectations item="affectation"}}
	          {{$affectation->_ref_lit->_view}}<br />
	      {{/foreach}}
	      {{if !$_sejour->_ref_affectations|@count}}
	        -
	      {{/if}}
		  </td>
		  <td style="text-align: center;">{{$_sejour->entree_reelle|date_format:$dPconfig.time}}</td>
		  <td style="text-align: center;">{{$_sejour->_ref_last_operation->entree_salle|date_format:$dPconfig.time}}</td>
		  <td style="text-align: center;">{{$_sejour->_ref_last_operation->sortie_salle|date_format:$dPconfig.time}}</td>
		  <td style="text-align: center;">{{$_sejour->_ref_last_operation->entree_reveil|date_format:$dPconfig.time}}</td>
		  <td style="text-align: center;">{{$_sejour->_ref_last_operation->sortie_reveil|date_format:$dPconfig.time}}</td>
		  <td style="text-align: center;">{{$_sejour->sortie_reelle|date_format:$dPconfig.time}}</td>
		</tr>
	{{/foreach}}
</table>