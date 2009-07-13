<table class="tbl">
  <tr>
    <th colspan="10" class="title">{{tr}}CMediusers-back-tarifs{{/tr}}</th>
  </tr>
  
  {{if !$user->_is_praticien && !$user->_is_secretaire}}
  <tr>
    <td class="text">
      <div class="big-info">
        N'étant pas praticien, vous n'avez pas accès à la liste de tarifs personnels.
      </div>
    </td>
  </tr>
  {{/if}}
  
  {{if $user->_is_secretaire}}
  <tr>
    <td colspan="10">
      <form action="?" name="selection" method="get">
        <input type="hidden" name="tarif_id" value="" />
        <input type="hidden" name="m" value="{{$m}}" />
        <select name="prat_id" onchange="this.form.submit()">
          <option value="">&mdash; Aucun praticien</option>
          {{foreach from=$listPrat item=_prat}}
          <option class="mediuser" style="border-color: #{{$_prat->_ref_function->color}};" value="{{$_prat->_id}}"
          {{if $_prat->_id == $prat->_id}}selected="selected"{{/if}}>
            {{$_prat}}
          </option>
          {{/foreach}}
        </select>
      </form>
    </td>
  </tr>
  {{/if}}
  

  {{if $user->_is_praticien || $user->_is_secretaire}}
  <tr>
    <th>{{mb_title class=CTarif field=description}}</th>
    <th>{{mb_title class=CTarif field=_has_mto}}</th>
    <th>{{mb_title class=CTarif field=secteur1}}</th>
    <th>{{mb_title class=CTarif field=secteur2}}</th>
    <th>{{mb_title class=CTarif field=_somme}}</th>
  </tr>

  {{foreach from=$listeTarifsChir item=_tarif}}
  <tr {{if $_tarif->_id == $tarif->_id}}class="selected"{{/if}}>
    <td {{if $_tarif->_precode_ready}}class="checked"{{/if}}>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;tarif_id={{$_tarif->_id}}">
      	{{mb_value object=$_tarif field=description}}
      </a>
    </td>
    <td>{{mb_value object=$_tarif field=_has_mto}}</td>
    <td style="text-align: right">{{mb_value object=$_tarif field=secteur1}}</td>
    <td style="text-align: right">{{mb_value object=$_tarif field=secteur2}}</td>
    <td style="text-align: right"><strong>{{mb_value object=$_tarif field=_somme}}</strong></td>
  </tr>
  {{/foreach}}
  {{/if}}
</table>

<table class="tbl">
  <tr><th colspan="10" class="title">{{tr}}CFunctions-back-tarifs{{/tr}}</th></tr>

  <tr>
    <th>{{mb_label class=CTarif field=description}}</th>
    <th>{{mb_label class=CTarif field=secteur1}}</th>
    <th>{{mb_label class=CTarif field=secteur2}}</th>
  </tr>

  {{foreach from=$listeTarifsSpe item=_tarif}}
  <tr {{if $_tarif->_id == $tarif->_id}}class="selected"{{/if}}>
    <td>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;tarif_id={{$_tarif->_id}}">
      	{{mb_value object=$_tarif field=description}}
      </a>
    </td>
    <td>{{mb_value object=$_tarif field=secteur1}}</td>
    <td>{{mb_value object=$_tarif field=secteur2}}</td>
  </tr>
  {{/foreach}}
</table>
