<table class="main">
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>
            Liste des fonctions disponibles
            <a href="{{$soap_url}}" title="accéder directement au serveur">(accéder directement au serveur)</a>
          </th>
        </tr>
        {{foreach from=$functions item=curr_function}}
        <tr>
          <td>
            {{$curr_function}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>