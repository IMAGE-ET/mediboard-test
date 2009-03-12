<table class="tbl">
  <tr>
    <th colspan="4">
      Dossiers Medicaux qui ont des traitements ou des antecedents(<strong>{{$dossiers|@count}}</strong>) 
      (nombre total: {{$nb_zombies}})</th>
  </tr>
  <tr>
    <th>Id</th>
    <th>Nb antecedents</th>
    <th>Nb traitements</th>
    <th>Codes CIM</th>
  </tr>
  {{foreach from=$dossiers item=_dossier}}
  <tr>
    <td>{{$_dossier->_id}}</td>
    <td>{{$_dossier->_count.antecedents}}</td>
    <td>{{$_dossier->_count.traitements}}</td>
    <td>{{$_dossier->codes_cim}}</td>
  </tr>
  {{/foreach}}
</table>