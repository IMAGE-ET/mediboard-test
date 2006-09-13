<script language="JavaScript" type="text/javascript">

function pageMain() {
  regFieldCalendar("addPlage", "date");
  regRedirectPopupCal("{{$debut}}", "index.php?m={{$m}}&tab={{$tab}}&debut=");
}

</script>

<table class="main">
  <tr>
    <th class="title" colspan="2">
      <a href="index.php?m={{$m}}&amp;debut={{$prec}}">&lt;&lt;&lt;</a>
      semaine du {{$debut|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
      <a href="index.php?m={{$m}}&amp;debut={{$suiv}}">&gt;&gt;&gt;</a>
    </th>
  </tr>
  <tr>
    <td>
      <table width="100%">
        <tr>
          <th></th>
          {{foreach from=$plages key=curr_day item=plagesPerDay}}
          <th>{{$curr_day|date_format:"%A %d"}}</th>
          {{/foreach}}
        </tr>
        {{foreach from=$listHours item=curr_hour}}
        <tr>
          <th>{{$curr_hour}}h</th>
          {{foreach from=$plages key=curr_day item=plagesPerDay}}
            {{assign var="isNotIn" value=1}}
            {{foreach from=$plagesPerDay item=curr_plage}}
              {{if $curr_plage->_hour_deb == $curr_hour}}
                <td align="center" bgcolor="{{$curr_plage->_state}}" rowspan="{{$curr_plage->_hour_fin-$curr_plage->_hour_deb}}">
                  <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;plage_id={{$curr_plage->plageressource_id}}">
                    {{if $curr_plage->libelle}}
                    {{$curr_plage->libelle}}
                    <br />
                    {{/if}}
                    {{$curr_plage->tarif}} €
                    <br />
                    {{$curr_plage->debut|date_format:"%H"}}h - {{$curr_plage->fin|date_format:"%H"}}h
                    {{if $curr_plage->prat_id}}
                    <br />
                    {{$curr_plage->_ref_prat->_view}}
                    {{/if}}
                  </a>
                </td>
              {{/if}}
              {{if ($curr_plage->_hour_deb <= $curr_hour) && ($curr_plage->_hour_fin > $curr_hour)}}
                {{assign var="isNotIn" value=0}}
              {{/if}}
            {{/foreach}}
            {{if $isNotIn}}
              <td bgcolor="#ffffff"></td>
            {{/if}}
          {{/foreach}}
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td>
      <form name="addPlage" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <table class="form">
        {{if $plage->plageressource_id}}
        <tr>
          <td colspan="4">
            <a class="buttonnew" href="index.php?m={{$m}}&amp;plage_id=0">Créer de nouvelles plages</a>
          </td>
        </tr>
        <tr>
          <th colspan="4" class="category">
	        <a style="float:right;" href="javascript:view_log('CPlageressource',{{$plage->plageressource_id}})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Modifier la plage du {{$plage->date|date_format:"%d/%m/%Y"}}
            ({{$plage->debut|date_format:"%H"}}h-{{$plage->fin|date_format:"%H"}}h)
          </th>
        </tr>
        {{else}}
        <tr><th colspan="4" class="category">Ajouter des plages</th></tr>
        {{/if}}
        <tr>
          <th><label for="date" title="Date de la plage. Obligatoire">Date</label></th>
          <td class="date">
            {{if $plage->plageressource_id}}
            <div id="addPlage_date_da">{{$plage->date|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="date" value="{{$plage->date}}" title="{{$plage->_props.date}}" />
            {{else}}
            <div id="addPlage_date_da">{{$debut|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="date" value="{{$debut}}" title="{{$plage->_props.date}}" />
            {{/if}}
            <img id="addPlage_date_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date"/>
          </td>
          <th><label for="_hour_deb" title="Heure de début">Début</label></th>
          <td>
            <select name="_hour_deb">
              {{foreach from=$listHours item=curr_hour}}
              <option value="{{$curr_hour|string_format:"%02d"}}" {{if $curr_hour == $plage->_hour_deb}} selected="selected" {{/if}}>
              {{$curr_hour|string_format:"%02d"}}
              </option>
            {{/foreach}}
            </select> heures
          </td>
        </tr>
        <tr>
          <th><label for="libelle" title="Libellé de la plage">Libellé</label></th>
          <td><input type="text" name="libelle" value="{{$plage->libelle}}" title="{{$plage->_props.libelle}}" /></td>
          <th><label for="_hour_fin" title="Heure de fin">Fin:</label</th>
          <td>
            <select name="_hour_fin">
              {{foreach from=$listHours item=curr_hour}}
              <option value="{{$curr_hour|string_format:"%02d"}}" {{if $curr_hour == $plage->_hour_fin}} selected="selected" {{/if}}>
              {{$curr_hour|string_format:"%02d"}}
              </option>
            {{/foreach}}
            </select> heures
          </td>
        </tr>
        <tr>
          <th><label for="tarif" title="Tarif de la plage. Obligatoire">Tarif</label></th>
          <td><input type="text" name="tarif" size="3" value="{{$plage->tarif}}" title="{{$plage->_props.tarif}}" />€</td>
          <th><label for="_repeat" title="Nombre de semaine concernées">Répétition:</label></th>
          <td><input type="text" name="_repeat" size="3" value="1" /></td>
        </tr>
        <tr>
          <th><label for="prat_id" title="Praticien. Optionnel">Praticien</label></th>
          <td>
            <select name="prat_id">
              <option value="">&mdash; Choix du praticien</option>
              {{foreach from=$listPrat item=curr_prat}}
                <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $plage->prat_id}}selected="selected"{{/if}}>
                  {{$curr_prat->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
          <th><label for="_double" title="Cochez pour n'affecter qu'une semaine sur deux">1 sem / 2</label></th>
          <td><input type="checkbox" name="_double" /></td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            <input type='hidden' name='dosql' value='do_plageressource_aed' />
            <input type='hidden' name='del' value='0' />
            <input type='hidden' name='plageressource_id' value='{{$plage->plageressource_id}}' />
            {{if $plage->plageressource_id}}
            <button class="modify" type="submit">Modifier</button>
            {{else}}
            <button class="submit" type="submit">Créer</button>
            {{/if}}
        </tr>
      </table>
      </form>
      {{if $plage->plageressource_id}}
      <form name="delPlage" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <table class="form">
        <tr><th colspan="2" class="category">Supprimer cette plage</th></tr>
        <tr>
          <th><label for="_repeat" title="Nombre de semaine concernées">Répétition</label></th>
          <td><input type="text" name="_repeat" size="3" value="1" /></td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <input type='hidden' name='dosql' value='do_plageressource_aed' />
            <input type='hidden' name='del' value='1' />
            <input type='hidden' name='plageressource_id' value='{{$plage->plageressource_id}}' />
            <button class="trash" type="submit">Supprimer</button>
        </tr>
      </table>
      </form>
      {{/if}}
    </td>
  </tr>
</table>