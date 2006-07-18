<script type="text/javascript">

function pageMain() {
  regFieldCalendar("hospitalisation", "debutact");
  regFieldCalendar("hospitalisation", "finact");
}

</script>

<table class="main">
  <tr>
    <td>
      <form name="hospitalisation" action="index.php" method="get" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="4" class="category">Occupation des lits</th>
        </tr>
        <tr>
          <th><label for="debutact" title="Date de début">Début:</label></th>
          <td class="date">
            <div id="hospitalisation_debutact_da">{{$debutact|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="debutact" title="date|notNull" value="{{$debutact}}" />
            <img id="hospitalisation_debutact_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de début"/>
         </td>
          <th>Service:</th>
          <td>
            <select name="service_id">
              <option value="0">&mdash; Tous les services</option>
              {{foreach from=$listServices item=curr_service}}
              <option value="{{$curr_service->service_id}}" {{if $curr_service->service_id == $service_id}}selected="selected"{{/if}}>
                {{$curr_service->nom}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="finact" title="Date de fin">Fin:</label></th>
          <td class="date">
            <div id="hospitalisation_finact_da">{{$finact|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="finact" title="date|moreEquals|debutact|notNull" value="{{$finact}}" />
            <img id="hospitalisation_finact_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de début"/>
          </td>
          <th>Praticien:</th>
          <td>
            <select name="prat_id">
              <option value="0">&mdash; Tous les praticiens</option>
              {{foreach from=$listPrats item=curr_prat}}
              <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $prat_id}}selected="selected"{{/if}}>
                {{$curr_prat->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>Type d'hospitalisation:</th>
          <td>
            <select name="type_adm">
              <option value="0">&mdash; Tous les types d'hospi</option>
              <option value="1" {{if $type_adm == "1"}}selected="selected"{{/if}}>Hospi complètes + ambu</option>
              {{foreach from=$listHospis item=curr_hospi}}
              <option value="{{$curr_hospi.code}}" {{if $curr_hospi.code == $type_adm}}selected="selected"{{/if}}>
                {{$curr_hospi.view}}
              </option>
              {{/foreach}}
            </select>
          </td>
          <th>Spécialité:</th>
          <td>
            <select name="discipline_id">
              <option value="0">&mdash; Toutes les spécialités</option>
              {{foreach from=$listDisciplines item=curr_disc}}
              <option value="{{$curr_disc->discipline_id}}" {{if $curr_disc->discipline_id == $discipline_id}}selected="selected"{{/if}}>
                {{$curr_disc->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="4" class="button"><button type="submit">Go</button></td>
        </tr>
        <tr>
          <td colspan="4"><i>Note : le nombre d'admissions par type d'hospitalisation avant le 16 novembre 2005 est en dessous de la réalité dû à un mauvais remplissage des dates d'admission par certains cabinets</i></td>
        </tr>
        <tr>
          <td colspan="4" class="button">
            <img alt="Patients par service" src='?m=dPstats&amp;a=graph_patparservice&amp;suppressHeaders=1&amp;debut={{$debutact}}&amp;fin={{$finact}}&amp;service_id={{$service_id}}&amp;prat_id={{$prat_id}}&amp;type_adm={{$type_adm}}&amp;discipline_id={{$discipline_id}}' />
            <img alt="Admissions par type d'hospitalisation" src='?m=dPstats&amp;a=graph_patpartypehospi&amp;suppressHeaders=1&amp;debut={{$debutact}}&amp;fin={{$finact}}&amp;service_id={{$service_id}}&amp;prat_id={{$prat_id}}&amp;type_adm={{$type_adm}}&amp;discipline_id={{$discipline_id}}' />
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>