
<form action="?" name="selection" method="get">
<input type="hidden" name="m" value="{{$m}}" />
<table class="form">
  <tr>
    <th class="category" colspan="2">
      <div style="float: right;">{{$hour|date_format:"%Hh%M"}}</div>
      {{$date|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
</table>
</form>

<script type="text/javascript">
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab=vw_bloodSalvage_sspi&date=");
</script>

<table class="tbl">
	<tr>
	  <th>Salle</th>
	  <th>Praticien</th>
	  <th>Patient</th>
	  <th>Entrée réveil</th>
	  <th>Début RSPO</th>
	</tr>
	
	{{foreach from=$listRSPO item=rspo}}
		<tbody class="hoverable">
		<tr>
		  <td class="text">
		  <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage_sspi&amp;rspo={{$rspo->_id}}" title="Gérer le Cell Saver">
		  {{$rspo->_ref_operation->_ref_salle->nom}}
		  </td>
		  <td class="text">
		  <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage_sspi&amp;rspo={{$rspo->_id}}" title="Gérer le Cell Saver">
		  Dr {{$rspo->_ref_operation->_ref_chir->_view}}
		  </td>
		  <td class="text">
		  <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage_sspi&amp;rspo={{$rspo->_id}}" title="Gérer le Cell Saver">  
		  {{$rspo->_ref_operation->_ref_sejour->_ref_patient->_view}}
		  </td>
		  <td class="text">{{$rspo->_ref_operation->entree_reveil|date_format:"%H:%M"}}</td>
		  <td class="text">{{$rspo->recuperation_start|date_format:"%H:%M"}}</td>
		</tr>
		</tbody>
	{{/foreach}}
</table>
