{{assign var="GHM" value=$sejour->_ref_GHM}}
<table class="form">
{{if $sejour->_ref_GHM->_CM}}
  <tr>
    <td colspan="2" class="text">
      <strong>Catégorie majeure CM{{$GHM->_CM}}</strong> : {{$GHM->_CM_nom}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="text">
      <strong>GHM</strong> : 
			{{$GHM->_GHM}} ({{$GHM->_tarif_2006|currency}})
    </td>
  </tr>
  <tr>
    <td colspan="2" class="text">
      {{$GHM->_GHM_nom}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="text">
      <em>Appartenance aux groupes {{$GHM->_GHM_groupe}}</em>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="text">
      <strong>Bornes d'hospitalisation</strong> :
      de {{$GHM->_borne_basse}}
      à {{$GHM->_borne_haute}} jours
    </td>
  </tr>
  {{if $GHM->_notes|@count}}
  <tr>
    <td colspan="2" class="text">
      <strong>Notes</strong> :
      <ul>
      {{foreach from=$GHM->_notes item="curr_note"}}
        <li>{{$curr_note}}</li>
      {{/foreach}}
      </ul>
    </td>
  </tr>
  {{/if}}
  <tr>
    <td class="button">
      <button class="tick" onclick="exporterHPRIM({{$sejour->_id}}, 'sej')">Export S@nté.com</button>
    </td>
  </tr>
  <tr>
    <td>
      {{if $sejour->_nb_echange_hprim}}
      <div class="small-success">
        Export déjà effectué {{$sejour->_nb_echange_hprim}} fois
      </div>
      {{else}}
      <div class="small-info">
        Pas d'export effectué
      </div>
      {{/if}}
    </td>
  </tr>
  {{else}}
  <tr>
    <td>
      <strong>{{$GHM->_GHM}}</strong>
    </td>
  </tr>
{{/if}}
</table>