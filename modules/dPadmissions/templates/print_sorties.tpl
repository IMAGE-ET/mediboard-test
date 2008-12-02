<!-- $Id: $ -->

<script type="text/javascript">

function printAdmission(id) {
  var url = new Url;
  url.setModuleAction("dPadmissions", "print_admission");
  url.addParam("id", id);
  url.popup(700, 550, 'Admissions');
}

</script>

<table class="main">
  <tr>
    <th>
      <a href="#" onclick="window.print()">
        Sorties du {{$date|date_format:"%A %d %b %Y"}} ({{$total}} sorties)
      </a>
    </th>
  </tr>
  {{foreach from=$listByPrat key=key_prat item=curr_prat}}
  {{assign var="praticien" value=$curr_prat.praticien}}
  <tr>
    <td>
      <strong>
        Dr {{$praticien->_view}} : {{$curr_prat.sejours|@count}} sortie(s)
      </strong>
    </td>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th colspan="4"><strong>Patient</strong></th>
          <th colspan="6"><strong>Sejour</strong></th>
        </tr>
        <tr>
          <th>Nom / Prenom</th>
          <th>Naissance (Age)</th>
          <th>Sexe</th>
          <th>Remarques</th>
		      <th>Sortie</th>
		      <th>Type</th>
          <th>Dur.</th>
          <th>Conv.</th>
          <th>Chambre</th>
          <th>Remarques</th>
        </tr>
        {{foreach from=$curr_prat.sejours item=curr_sejour}}
        <tr>
          <td>
            <a href="#" onclick="printAdmission({{$curr_sejour->_id}})">
              {{$curr_sejour->_ref_patient->_view}}
            </a>
          </td>
          <td>
            <a href="#" onclick="printAdmission({{$curr_sejour->_id}})">
              {{mb_value object=$curr_sejour->_ref_patient field="naissance"}} ({{$curr_sejour->_ref_patient->_age}} ans)
            </a>
          </td>
          <td>
            {{if $curr_sejour->_ref_patient->sexe == "m"}}masculin{{else}}féminin{{/if}}
          </td>
          <td class="text">
            <a href="#" onclick="printAdmission({{$curr_sejour->sejour_id}})">
              {{$curr_sejour->_ref_patient->rques}}
            </a>
          </td>
          <td>{{$curr_sejour->sortie_prevue|date_format:$dPconfig.time}}</td>
          <td>
            {{if !$curr_sejour->facturable}}
            <strong>NF</strong>
            {{/if}}
            
            {{$curr_sejour->type|truncate:1:""|capitalize}}
          </td>
          <td>{{$curr_sejour->_duree_prevue}} j</td>
          <td class="text">{{$curr_sejour->convalescence|nl2br}}</td>
          <td class="text">
            {{assign var="affectation" value=$curr_sejour->_ref_last_affectation}}
            {{if $affectation->affectation_id}}
              {{$affectation->_ref_lit->_view}}
            {{else}}
              Non placé
            {{/if}}
            ({{tr}}chambre_seule.{{$curr_sejour->chambre_seule}}{{/tr}})
          </td>
          <td class="text">{{$curr_sejour->rques}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{/foreach}}
</table>