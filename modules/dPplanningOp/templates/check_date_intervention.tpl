<table class="tbl" style="text-align: center;">
  <tr>
    <th style="width: 25%;">Nombre d'interventions...</th>
    <th style="width: 25%;">dans une plage...</th>
    <th style="width: 25%;">sans date!</th>
    <th style="width: 25%;">avec une date eronn�e!</th>
  </tr>

  <tr>
    <td>{{$counts.total|integer}}</td>
    <td>{{$counts.plaged|integer}}</td>
    <td class="{{$counts.missing|ternary:warning:ok}}"><strong>{{$counts.missing|integer}}</strong></td>
    <td class="{{$counts.wrong|ternary:warning:ok}}"><strong>{{$counts.wrong|integer}}</strong></td>
  </tr>

  <tr>
    <td colspan="4" class="button">
      <button type="button" class="change">{{tr}}Sanitize{{/tr}}</button>
    </td>
  </tr>
</table>