<table class="tbl" style="text-align:center;">
  <tr>
    <th>Montant {{$caisses_maladie[$caisse]}}:</th>
    <th>{{$tarifs[0]*$nb_factures[$caisse]}} CHF</th>
  </tr>
  <tr>
    <td>Scinder ce montant</td>
    <td><input type="checkbox" name="active_{{$caisse}}" {{if $nb_factures[$caisse] >=2}}checked{{/if}} /></td>
  </tr>
  <tr>
    <td>Nombre de coupes:</td>
    <td><input type="text" name="nb_factures_{{$caisse}}" value="{{$nb_factures[$caisse]}}" onchange="refresh(this, '{{$caisse}}');"/></td>
  </tr>
  {{foreach from=$tarifs item=montant key=key}}
    <tr>
      <td>Facture n° {{$key+1}}</td>
      <td><input type="text" name="tarif{{$caisse}}_{{$key}}" value="{{$montant|string_format:"%.2f"}}""/></td>
    </tr>
  {{/foreach}}
</table>