<table class="form">
  <tr>
	<td style="text-align:center;">
	  <b>Durée totale RSPO : </b>
	  {{$totaltime|date_format:$dPconfig.time}}
	</td>
	</tr>
</table>
{{if $totaltime > "05:00:00" && $totaltime < "06:00:00" }} 
  <div class="small-warning" style="text-align:center;">
    Limite légale de 6 heures bientôt atteinte ! <br />
    <b>Temps restant : {{$timeleft|date_format:$dPconfig.longtime}}</b>
  </div>
{{/if}}
{{if $totaltime > "06:00:00"}} 
  <div class="small-error" style="text-align:center;">
    Limite légale de 6 heures dépassée !
  </div>
{{/if}}
