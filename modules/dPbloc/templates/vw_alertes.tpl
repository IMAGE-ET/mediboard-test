<table class="tbl">
  <tr>
    <th>Date</th>
    <th>Chirurgien</th>
    <th>Salle</th>
    <th>Intervention</th>
  </tr>
  {{if $listNonValidees|@count}}
  <tr>
    <th colspan="4">{{$listNonValidees|@count}} intervention(s) non validées</th>
  </tr>
  {{/if}}
  {{foreach from=$listNonValidees item=_operation}}
  <tr>
    <td>{{$_operation->_ref_plageop->date|date_format:$dPconfig.date}}</td>
    <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}</td>
    <td>{{$_operation->_ref_salle->_view}}</td>
    <td class="text">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}')">
        {{mb_include template=../../dPplanningOp/templates/inc_vw_operation}}
      </span>
    </td>
  </tr>
  {{/foreach}}
  {{if $listHorsPlage|@count}}
  <tr>
    <th colspan="4">{{$listHorsPlage|@count}} intervention(s) hors plage</th>
  </tr>
  {{/if}}
  {{foreach from=$listHorsPlage item=_operation}}
  <tr>
    <td>{{$_operation->date|date_format:$dPconfig.date}}</td>
    <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}</td>
    <td>{{$_operation->_ref_salle->_view}}</td>
    <td class="text">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}')">
        {{mb_include template=../../dPplanningOp/templates/inc_vw_operation}}
      </span>
    </td>
  </tr>
  {{/foreach}}
</table>