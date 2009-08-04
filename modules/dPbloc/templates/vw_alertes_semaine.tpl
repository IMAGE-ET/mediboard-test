<table class="tbl">
  <tr>
    <th>Date</th>
    <th>Chirurgien</th>
    <th>Intervention</th>
  </tr>
  {{foreach from=$listOperations item=_op}}
  <tr>
    <td>{{$_op->_ref_plageop->date|date_format:$dPconfig.date}}</td>
    <td>Dr {{$_op->_ref_chir->_view}}</td>
    <td class="text">{{$_op->libelle}}</td>
  </tr>
  {{/foreach}}
</table>