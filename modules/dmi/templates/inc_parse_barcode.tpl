<h1>{{$parsed.type}}</h1>
<table class="tbl">
{{foreach from=$parsed.comp key=key item=value}}
  <tr>
    <th style="width: 0.1%;">{{$key}}</th>
    <td>{{$value}}</td>
  </tr>
{{/foreach}}
</table>

<h1>Résultats</h1>
<table class="tbl">
{{foreach from=$matches item=_match}}
  <tr>
    <th style="width: 0.1%;">{{$_match->_class_name}}</th>
    <td>
      {{$_match}} - {{$_match->_ref_societe}}
      
      {{if $_match->_class_name == "CProduct"}}
        <h3>Lots:</h3>
        {{foreach from=$_match->_lots item=_lot}}
          [{{$_lot->code}}] - {{$_lot}}<br />
        {{/foreach}}
      {{/if}}
    </td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="2">No match</td>
  </tr>
{{/foreach}}
</table>