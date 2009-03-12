<table class="tbl">
  <tr>
    <th colspan="3">
      Dossiers Medicaux qui ont des traitements ou des antecedents(<strong>{{$dossiers|@count}}</strong>) 
      (nombre total: {{$nb_zombies}})</th>
  </tr>
  <tr>
    <th>Id</th>
    <th>Nb antecedents</th>
    <th>Nb traitements</th>
  </tr>
  {{foreach from=$dossiers item=_dossier}}
  <tr>
    <td>{{$_dossier->_id}}</td>
    <td>{{$_dossier->_count.antecedents}}</td>
    <td>{{$_dossier->_count.traitements}}</td>
  </tr>
  {{/foreach}}
</table>