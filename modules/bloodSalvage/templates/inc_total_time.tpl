<table class="form">
  <tr>
	<td style ="text-align:center;">
	<b>Durée totale RSPO : </b>
	<input name="total" size="3" maxLength="5" type="text" value="{{$totaltime|date_format:$dPconfig.time}}" />
	</td>
	</tr>
</table>
{{if $totaltime > "05:00:00" && $totaltime < "06:00:00" }} 
  <div class="big-warning" style="text-align:center;">
    Limite légale de 6 heures bientôt atteinte ! <br /><br />
    <b>Temps restant : {{$timeleft|date_format:$dPconfig.longtime}}</b>
  </div>
{{/if}}
{{if $totaltime > "06:00:00"}} 
  <div class="big-error" style="text-align:center;">
    Limite légale de 6 heures dépassée ! <br /><br />
  </div>
{{/if}}
