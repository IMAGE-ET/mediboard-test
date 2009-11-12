<script type="text/javascript">
Main.add(function () {
  Calendar.regField(getForm("addPlage").date);
  Calendar.regField(getForm("changeDate").debut, null, {noView: true});
});
</script>

<table class="main">
  <tr>
    <th class="title" colspan="2">
      <a href="?m={{$m}}&amp;debut={{$prec}}">&lt;&lt;&lt;</a>
      Semaine du {{$debut|date_format:$dPconfig.longdate}}
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="debut" class="date" value="{{$debut}}" onchange="this.form.submit()" />
      </form>
      <a href="?m={{$m}}&amp;debut={{$suiv}}">&gt;&gt;&gt;</a>
    </th>
  </tr>
  <tr>
    <td>
      <table width="100%" id="weeklyPlanning">
        <tr>
          <th></th>
          {{foreach from=$plages|smarty:nodefaults key=curr_day item=plagesPerDay}}
          <th scope="col" style="width: {{math equation="x/y" x=100 y=$plages|@count}}%">{{$curr_day|date_format:"%A %d"}}</th>
          {{/foreach}}
        </tr>
        {{foreach from=$listHours|smarty:nodefaults item=curr_hour}}
        <tr>
          <th scope="row">{{$curr_hour}}h</th>
          {{foreach from=$plages key=curr_day item=plagesPerDay}}
            {{assign var="isNotIn" value=1}}
            {{foreach from=$plagesPerDay item=curr_plage}}
              {{if $curr_plage->_hour_deb == $curr_hour}}
                <td align="center" bgcolor="{{$curr_plage->_state}}" rowspan="{{$curr_plage->_hour_fin-$curr_plage->_hour_deb}}">
                  <a href="?m={{$m}}&amp;tab={{$tab}}&amp;plage_id={{$curr_plage->plageressource_id}}">
                    {{if $curr_plage->libelle}}
                    {{$curr_plage->libelle}}
                    <br />
                    {{/if}}
                    {{$curr_plage->tarif}} {{$dPconfig.currency_symbol}}
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
              <td class="empty hour_start"></td>
            {{/if}}
          {{/foreach}}
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td>
      <form name="addPlage" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <table class="form">
        {{if $plage->_id}}
        <tr>
          <td colspan="4">
            <a class="button new" href="?m={{$m}}&amp;plage_id=0">Créer de nouvelles plages</a>
          </td>
        </tr>
        <tr>
          <th colspan="4" class="category modify">
			      {{mb_include module=system template=inc_object_idsante400 object=$plage}}
			      {{mb_include module=system template=inc_object_history object=$plage}}
            Modifier la plage du 
						{{mb_value object=$plage field=date}}
            ({{mb_value object=$plage field=debut}}-{{mb_value object=$plage field=fin}})
          </th>
        </tr>
        {{else}}
        <tr><th colspan="4" class="category">Ajouter des plages</th></tr>
        {{/if}}
        <tr>
          <th>{{mb_label object=$plage field="date"}}</th>
          <td>
            {{if $plage->plageressource_id}}
            <input type="hidden" name="date" value="{{$plage->date}}" class="{{$plage->_props.date}}" />
            {{else}}
            <input type="hidden" name="date" value="{{$debut}}" class="{{$plage->_props.date}}" />
            {{/if}}
          </td>
          <th>{{mb_label object=$plage field="_hour_deb"}}</th>
          <td>
            <select name="_hour_deb">
              {{foreach from=$listHours|smarty:nodefaults item=curr_hour}}
              <option value="{{$curr_hour|string_format:"%02d"}}" {{if $curr_hour == $plage->_hour_deb}} selected="selected" {{/if}}>
              {{$curr_hour|string_format:"%02d"}}
              </option>
            {{/foreach}}
            </select> heures
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$plage field="libelle"}}</th>
          <td>{{mb_field object=$plage field="libelle"}}</td>
          <th>{{mb_label object=$plage field="_hour_fin"}}</th>
          <td>
            <select name="_hour_fin" class="num moreThan|_hour_deb">
              {{foreach from=$listHours|smarty:nodefaults item=curr_hour}}
              <option value="{{$curr_hour|string_format:"%02d"}}" {{if $curr_hour == $plage->_hour_fin}} selected="selected" {{/if}}>
              {{$curr_hour|string_format:"%02d"}}
              </option>
            {{/foreach}}
            </select> heures
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$plage field="tarif"}}</th>
          <td>{{mb_field object=$plage field="tarif"}}</td>
          <th><label for="_repeat" title="Nombre de semaine concernées">Répétition:</label></th>
          <td><input type="text" name="_repeat" size="3" value="1" /></td>
        </tr>
        <tr>
          <th>{{mb_label object=$plage field="prat_id"}}</th>
          <td>
            <select name="prat_id">
              <option value="">&mdash; Choix du praticien</option>
              {{foreach from=$listPrat item=curr_prat}}
                <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $plage->prat_id}}selected="selected"{{/if}}>
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