<script type="text/javascript">
//Main.add(window.print);

var lists = {
  sejour: {
    labels: ["Dernier séjour", "Séjours"],
    all: true
  },
  consultation: {
    labels: ["Dernière consultation", "Consultations"],
    all: true
  },
};

function toggleList(list, button) {
  var lines = $$('.'+list),
      data = lists[list];
      
  lines.invoke('toggle');
  lines.first().show();
  data.all = !data.all;
  button.up().select('span')[0].update(data.labels[data.all ? 1 : 0]);
}
</script>

<table class="print">
  <tr>
  	<th class="title" colspan="2"><a href="#" onclick="window.print()">Fiche Patient</a></th>
	</tr>
  <tr>
  	<th>Date: </th>
		<td>{{$today}}</td>
	</tr>
  <tr>
  	<th class="category" colspan="2">Informations sur le patient</th>
	</tr>
  <tr>
  	<th>{{mb_label object=$patient field=nom}} / {{mb_label object=$patient field=prenom}}</th>
	  <td><strong>{{$patient->_view}}</strong> {{mb_include module=dPpatients template=inc_vw_ipp ipp=$patient->_IPP hide_empty=true}}</td>
  </tr>
  <tr>
  	<th>{{mb_label object=$patient field=naissance}} / {{mb_label object=$patient field=sexe}}</th>
		<td>né(e) le {{mb_value object=$patient field=naissance}} de sexe {{if $patient->sexe == "m"}} masculin {{else}} féminin {{/if}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field=incapable_majeur}}</th>
    <td>{{mb_value object=$patient field=incapable_majeur}}</td>
  </tr>
  <tr>
  	<th>{{mb_label object=$patient field=tel}}</th>
		<td>{{mb_value object=$patient field=tel}}</td>
  </tr>
  <tr>
  	<th>{{mb_label object=$patient field=tel2}}</th>
		<td>{{mb_value object=$patient field=tel2}}</td>
	</tr>
  <tr>
  	<th>{{mb_label object=$patient field=adresse}}</th>
		<td>{{$patient->adresse|nl2br}} - {{$patient->cp}} {{$patient->ville}}</td>
	</tr>
  <tr>
  	<th>{{mb_label object=$patient field=rques}}</th>
		<td>{{$patient->rques|nl2br}}</td>
	</tr>
  {{if $patient->_ref_medecin_traitant->medecin_id || $patient->_ref_medecins_correspondants|@count}}
  <tr><th class="category" colspan="2">Correspondants médicaux</th></tr>
  {{if $patient->_ref_medecin_traitant->medecin_id}}
    <tr>
      <th>Médecin traitant: </th>
      <td>
        {{$patient->_ref_medecin_traitant->_view}}<br />
        {{$patient->_ref_medecin_traitant->adresse|nl2br}}<br />
        {{$patient->_ref_medecin_traitant->cp}} {{$patient->_ref_medecin_traitant->ville}}
      </td>
    </tr>
  {{/if}}
  {{if $patient->_ref_medecins_correspondants|@count}}
    <tr>
      <th>Correspondants médicaux: </th>
      <td>
        {{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp}}
        <div style="float: left; margin-right: 1em; margin-bottom: 0.5em; margin-top: 0.4em; width: 15em;">
          {{$curr_corresp->_ref_medecin->_view}}<br />
          {{$curr_corresp->_ref_medecin->adresse|nl2br}}<br />
          {{$curr_corresp->_ref_medecin->cp}} {{$curr_corresp->_ref_medecin->ville}}
        </div>
        {{/foreach}}
      </td>
    </tr>
  {{/if}}
  {{/if}}
  {{if $patient->_ref_sejours|@count}}
  <tr>
    <th class="category" colspan="10">
      <button class="change not-printable" style="float:right;" onclick="toggleList('sejour', this)">Seulement le dernier</button>
      <span>Séjours</span>
    </th>
  </tr>
  {{foreach from=$patient->_ref_sejours item=curr_sejour}}
  <tr class="sejour">
    <th>Dr {{$curr_sejour->_ref_praticien->_view}}</th>
    <td>
      {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$curr_sejour->_num_dossier}}
      Du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}}
      au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
			- ({{mb_value object=$curr_sejour field=type}})
      <ul>
      {{foreach from=$curr_sejour->_ref_operations item="curr_op"}}
        <li>
          Intervention le {{$curr_op->_datetime|date_format:"%d/%m/%Y"}}
          (Dr {{$curr_op->_ref_chir->_view}})
        </li>
      {{foreachelse}}
        <li><em>Pas d'interventions</em></li>
      {{/foreach}}
      </ul>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
  {{if $patient->_ref_consultations|@count}}
  <tr>
    <th class="category" colspan="10">
      <button class="change not-printable" style="float:right;" onclick="toggleList('consultation', this)">Seulement la dernière</button>
      <span>Consultations</span>
    </th>
  </tr>
  {{foreach from=$patient->_ref_consultations item=curr_consult}}
  <tr class="consultation">
    <th>Dr {{$curr_consult->_ref_plageconsult->_ref_chir->_view}}</th>
    <td>le {{$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}}</td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>