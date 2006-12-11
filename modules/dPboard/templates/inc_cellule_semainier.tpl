{{assign var="plageInfos" value=$plageJour.$colonne}}

{{if is_string($plageJour.$colonne) && $plageJour.$colonne == "empty"}}
  <td class="empty"></td>
{{elseif is_string($plageJour.$colonne) && $plageJour.$colonne == "hours"}}
  <td class="empty" rowspan="4"></td>
{{elseif is_string($plageJour.$colonne) && $plageJour.$colonne == "full"}}
{{else}}
  <td class="nonEmpty{{$style}}" rowspan="{{$plageInfos->_nbQuartHeure}}">
  
  {{assign var="pct" value=$plageInfos->_fill_rate}}
  {{if $pct gt 100}}
  {{assign var="pct" value=100}}
  {{/if}}
  {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
  {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
  {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
  {{else}}{{assign var="backgroundClass" value="full"}}
  {{/if}}
  
  {{if $colonne=="plagesConsult"}}
    <a href="?m=dPcabinet&amp;tab=vw_planning&amp;plageconsult_id={{$plageInfos->plageconsult_id}}" title="Voir le contenu de la plage">
      {{if $plageInfos->libelle}}{{$plageInfos->libelle}}<br />{{/if}}
      {{$plageInfos->debut|date_format:"%Hh%M"}} - {{$plageInfos->fin|date_format:"%Hh%M"}}
    </a>
    <a href="?m=dPcabinet&amp;tab=edit_planning&amp;consultation_id=0&amp;plageconsult_id={{$plageInfos->plageconsult_id}}" title="Planifier une consultation dans cette plage"> 
      <div class="progressBar">
        <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
        <div class="text">{{$plageInfos->_affected}} / {{$plageInfos->_total}}</div>
      </div>
    </a>
  {{else}}
    {{$plageInfos->_ref_salle->nom}}<br />
    {{$plageInfos->debut|date_format:"%Hh%M"}} - {{$plageInfos->fin|date_format:"%Hh%M"}}
      <div class="progressBar">
        <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
        <div class="text">{{$plageInfos->_nb_operations}} Intervention(s)</div>
      </div>    
  {{/if}}
  </td>            
{{/if}}