      <table class="tbl">
        <tr>
          <th rowspan="2">Praticien</th>
          <th rowspan="2">CCAM</th>
          <th rowspan="2">Nombre d'interventions</th>
          <th rowspan="2">Estimation de durée</th>
          <th colspan="2">Occupation de salle</th>
          <th colspan="2">Durée d'intervention</th>
        </tr>
        <tr>
          <th>Moyenne</th>
          <th>Ecart-type</th>
          <th>Moyenne</th>
          <th>Ecart-type</th>
        </tr>
        {{foreach from=$listTemps item=curr_temps}}
        <tr>
          <td>Dr {{$curr_temps->_ref_praticien->_view}}</td>
          <td>{{$curr_temps->ccam}}</td>
          <td>{{$curr_temps->nb_intervention}}</td>
          {{if $curr_temps->estimation > $curr_temps->occup_moy}}
          <td style="background-color: #aaf;">
          {{elseif $curr_temps->estimation < $curr_temps->duree_moy}}
          <td style="background-color: #faa;">
          {{else}}
          <td style="background-color: #afa;">
          {{/if}}
            {{$curr_temps->estimation|date_format:$dPconfig.time}}
          </td>
          <td>{{$curr_temps->occup_moy|date_format:$dPconfig.time}}</td>
          <td><i>{{if $curr_temps->occup_ecart != "-"}}{{$curr_temps->occup_ecart|date_format:$dPconfig.time}}{{else}}-{{/if}}</i></td>
          <td>{{$curr_temps->duree_moy|date_format:$dPconfig.time}}</td>
          <td><i>{{if $curr_temps->duree_ecart != "-"}}{{$curr_temps->duree_ecart|date_format:$dPconfig.time}}{{else}}-{{/if}}</i></td>
        </tr>
        {{/foreach}}
        
        <tr>
          <th colspan="2">Total</th>
          <td>{{$total.nbInterventions}}</td>
          {{if $total.estim_moy > $total.occup_moy}}
          <td style="background-color: #aaf;">
          {{elseif $total.estim_moy < $total.duree_moy}}
          <td style="background-color: #faa;">
          {{else}}
          <td style="background-color: #afa;">
          {{/if}}
            {{$total.estim_moy|date_format:$dPconfig.time}}
          </td>
          <td>{{$total.occup_moy|date_format:$dPconfig.time}}</td>
          <td>-</td>
          <td>{{$total.duree_moy|date_format:$dPconfig.time}}</td>
          <td>-</td>
        </tr>
      </table>