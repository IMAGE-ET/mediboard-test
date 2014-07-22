{{if $see}}
  {{$factures|@count}} facture(s) en erreur.
  {{$items|@count}} item(s) concernés.
  {{if $debut}}Du {{$debut}}{{/if}}
  {{if $fin}}Du {{$fin}}{{/if}}
  {{if $factures|@count}}
    <table class="tbl">
      <th>Facture</th>
      <th>Date</th>
      <th>Type d'acte</th>
      <th>Code</th>
      <th>Libelle</th>
      <th>Base</th>
      <th>DH</th>
      {{foreach from=$items item=_item}}
        <tr>
          <td>{{$_item->_ref_object}}</td>
          <td>{{mb_value object=$_item field=date}}</td>
          <td>{{mb_value object=$_item field=type}}</td>
          <td>{{mb_value object=$_item field=code}}</td>
          <td>{{mb_value object=$_item field=libelle}}</td>
          <td>{{mb_value object=$_item field=montant_base}}</td>
          <td>{{mb_value object=$_item field=montant_depassement}}</td>
        </tr>
      {{/foreach}}
    </table>
  {{/if}}
{{else}}
  {{$items_delete}} lignes de factures supprimées.
{{/if}}