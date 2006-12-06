<div id="disabledEtape4" class="chargementMask" style="position:absolute;display:none;"></div>
<table class="main">

  <tr>
    <th colspan="2" class="category">Consultations</th>
    <th colspan="2" class="category">Interventions</th>
    {{if $_nb_services}}
    <th colspan="2" class="category">Services</th>
    {{/if}}
  </tr>
  
  <tr>
    <th>
      <label for="_nb_plages" title="Nombre de plages de consultations à créer par praticien">Nombre de plages</label>
    </th>
    <td>
      {{html_options name="_nb_plages" options=$list_14}}
    </td>  
    <th>
      <label for="_nb_plagesop" title="Nombre de plages opératoire à créer par salle et par semaine">Nombre de plage opératoire</label>
    </th>
    <td>
      {{html_options name="_nb_plagesop" options=$list_14}}
    </td>
    {{if $_nb_services}}
    <th>
      <label for="_nb_chambre" title="Nombre de chambre à créer par service">Nombre de chambre</label>
    </th>
    <td>
      {{html_options name="_nb_chambre" options=$list_20}}
    </td>
    {{/if}} 
  </tr>
  
  <tr>
    <th>
      <label for="_nb_consult" title="Nombre de consultation à créer par praticien">Nombre de consultation</label>
    </th>
    <td>
      {{html_options name="_nb_consult" options=$list_20}}
    </td>
    <th>
      <label for="_nb_interv" title="Nombre d'intervention à créer par plage opératoire">Nombre d'intervention</label>
    </th>
    <td>
      {{html_options name="_nb_interv" options=$list_5}}
    </td>
    {{if $_nb_services}}
    <th>
      <label for="_nb_lit" title="Nombre maximal de lit à créer par chambre">Nombre de lit</label>
    </th>
    <td>
      {{html_options name="_nb_lit" options=$list_5}}
    </td>
    {{/if}}
  </tr>
  
  <tr>
    <td class="button" colspan="6">
      <button type="submit" class="submit">Créer</button>
    </td>
  </tr>
</table>