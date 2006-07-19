      <table class="tbl">
        <tr>
          <th rowspan="2">Praticien</th>
          <th rowspan="2">Nombre de préparation</th>
          <th rowspan="2">Nombre de plages</th>
          <th colspan="2">Durée des pauses</th>
        </tr>
        <tr>
          <th>Moyenne</th>
          <th>Ecart-type</th>
        </tr>
        {{foreach from=$result item=curr_result}}
        <tr>
          <td>Dr. {{$curr_result.praticien}}</td>
          <td>{{$curr_result.preparation}}</td>
          <td>-</td>
          <td>{{$curr_result.moyenne|date_format:"%Mmin %Ss"}}</td>
          <td>-</td>
        </tr>
        {{/foreach}}
        <tr>
          <th>Total</th>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
        </tr>
        
      </table>