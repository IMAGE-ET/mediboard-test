<table class="tbl">
  <tr>
    <th colspan="2">
      Allergies
    </th>
  </tr>
  <tr>
    <th>
    Date
    </th>
    <th>
    Remarque
    </th>
  </tr>
  {{foreach from=$allergies item=allergie}}
  <tr>
    <td>
			{{if $allergie->date}}
			  {{$allergie->date|date_format:"%d/%m/%Y"}}:
			{{/if}}
	  </td>
	  <td>
			{{$allergie->rques}}
	  </td>
  </tr>
 {{/foreach}}
</table>