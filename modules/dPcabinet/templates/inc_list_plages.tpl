    <table class="tbl">
      <tr>
        <th style="width: 7em;">{{mb_title class=CPlageconsult field=date}}</th>
        <th>{{mb_title class=CPlageconsult field=chir_id}}</th>
        <th>{{mb_title class=CPlageconsult field=libelle}}</th>
        <th colspan="2">{{tr}}Status{{/tr}}</th>
      </tr>
      {{foreach from=$listPlage item=_plage}}
      <tr {{if $_plage->_id == $plageconsult_id}}class="selected"{{/if}} id="plage-{{$_plage->_id}}">
        <td {{if in_array($_plage->date, $bank_holidays)}}style="background: #fc0"{{/if}}>
          {{mb_include template=inc_plage_etat}}
        </td>
        <td class="text">
          <div class="mediuser" style="border-color: #{{$_plage->_ref_chir->_ref_function->color}};">
            {{$_plage->_ref_chir}}
          </div>
        </td>
        <td class="text">
          <div style="background-color:#{{$_plage->color}};display:inline;">&nbsp;&nbsp;</div>
          {{if $online}}
            <span style="float: right;">
              {{mb_include module=system template=inc_object_notes object=$_plage}}
            </span>
          {{/if}}
          {{$_plage->libelle}}
        </td>
        <td style="text-align: center;">
          {{$_plage->_affected}}/{{$_plage->_total|string_format:"%.0f"}}
        </td>
        <td>
          {{if $_plage->_consult_by_categorie|@count}}
            {{foreach from=$_plage->_consult_by_categorie item=curr_categorie}}
              {{$curr_categorie.nb}}
              <img alt="{{$curr_categorie.nom_categorie}}" title="{{$curr_categorie.nom_categorie}}" src="modules/dPcabinet/images/categories/{{$curr_categorie.nom_icone|basename}}"  style="vertical-align: middle;" />
            {{/foreach}}
          {{/if}}
        </td>
      </tr>
      {{foreachelse}}
      <tr>
        <td colspan="5" class="empty">{{tr}}CPlageconsult.none{{/tr}}</td>
      </tr>
      {{/foreach}}
    </table>