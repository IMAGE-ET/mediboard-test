
      <form action="index.php" name="selection" method="get">
      
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="op" value="0" />
      
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            {{$date|date_format:"%A %d %B %Y"}}
            <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
          </th>
        </tr>
        
        <tr>
          <th><label for="salle" title="Salle d'opération">Salle</label></th>
          <td>
            <select name="salle" onchange="this.form.submit()">
              <option value="">&mdash; Aucune salle</option>
              {{foreach from=$listSalles item=curr_salle}}
              <option value="{{$curr_salle->salle_id}}" {{if $curr_salle->salle_id == $salle}} selected="selected" {{/if}}>
                {{$curr_salle->nom}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
      </table>
      
      </form>
      <script type="text/javascript">
      regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&op=0&date=");
	  </script>      
      {{include file="inc_details_plages.tpl"}}