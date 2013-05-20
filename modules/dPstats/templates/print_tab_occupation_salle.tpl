<h1>Occupation de salle par praticien</h1>
<ul>
  <li>Debut : <strong>{{$debut|date_format:$conf.date}}</strong></li>
  <li>Fin : <strong>{{$fin|date_format:$conf.date}}</strong></li>
  <li>Actes prévus : <strong>{{$codeCCAM}}</strong></li>
  <li>Type d'admission : <strong>{{$type_hospi}}</strong></li>
  <li>Spécialité : <strong>{{$discipline}}</strong></li>
  <li>Bloc opératoire : <strong>{{$bloc}}</strong></li>
  <li>Salle : <strong>{{$salle}}</strong></li>
  <li>Hors plage : <strong>{{$hors_plage|ternary:"Oui":"non"}}</strong></li>
</ul>

<table class="tbl">
  <tr>
    <th rowspan="2">Spécialité</th>
    <th rowspan="2">fonction</th>
    <th rowspan="2">Praticien</th>
    <th colspan="4">Durée totale (en heure)</th>
    <th colspan="4">Durée moyenne / interv. (en minutes)</th>
  </tr>
  <tr>
    <th>Intervention</th>
    <th>Référence</th>
    <th>Occupation</th>
    <th>Référence</th>
    <th>Intervention</th>
    <th>Référence</th>
    <th>Occupation</th>
    <th>Référence</th>
  </tr>
  {{foreach from=$tableau item=_praticien}}
  <tr>
    <td>{{$_praticien.user->_ref_discipline}}</td>
    <td>{{$_praticien.user->_ref_function}}</td>
    <td>{{$_praticien.user}}</td>
    <td>{{$_praticien.duree_totale_intervs|string_format:"%.2f"}}</td>
    <td>{{$_praticien.nb_interv_intervs}}/{{$_praticien.total_interventions}} intervs.</td>
    <td>{{$_praticien.duree_totale_occupation|string_format:"%.2f"}}</td>
    <td>{{$_praticien.nb_interv_occupation}}/{{$_praticien.total_interventions}} intervs.</td>
    <td>{{$_praticien.duree_moyenne_intervs|string_format:"%.2f"}}</td>
    <td>{{$_praticien.nb_interv_intervs}}/{{$_praticien.total_interventions}} intervs.</td>
    <td>{{$_praticien.duree_moyenne_occupation|string_format:"%.2f"}}</td>
    <td>{{$_praticien.nb_interv_occupation}}/{{$_praticien.total_interventions}} intervs.</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td class="none" colspan="10">Aucun praticien</td>
  </tr>
  {{/foreach}}
  <tr>
    <th colspan="3">Total</th>
    <td>{{$duree_totale_intervs|string_format:"%.2f"}}</td>
    <td>{{$nb_interv_intervs}}/{{$total_interventions}} intervs.</td>
    <td>{{$duree_totale_occupation|string_format:"%.2f"}}</td>
    <td>{{$nb_interv_occupation}}/{{$total_interventions}} intervs.</td>
    <td>{{$duree_moyenne_intervs|string_format:"%.2f"}}</td>
    <td>{{$nb_interv_intervs}}/{{$total_interventions}} intervs.</td>
    <td>{{$duree_moyenne_occupation|string_format:"%.2f"}}</td>
    <td>{{$nb_interv_occupation}}/{{$total_interventions}} intervs.</td>
  </tr>
</table>
