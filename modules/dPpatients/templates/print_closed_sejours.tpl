<table class="tbl" style="width: 100%">
  <tr>
  	<th>{{mb_label class=CSejour field=entree_reelle}}</th>
	  <th>{{mb_label class=CSejour field=sortie_reelle}}</th>
	  <th>{{mb_label class=CSejour field=praticien_id}}</th>
	</tr>
  {{foreach from=$sejours item=_sejour}}
    <tr>
      <td>{{mb_value object=$_sejour field=entree_reelle}}</td>
      <td>{{mb_value object=$_sejour field=sortie_reelle}}</td>
      <td>{{mb_value object=$_sejour field=praticien_id}}</td>
    </tr>
  {{/foreach}}
</table>
