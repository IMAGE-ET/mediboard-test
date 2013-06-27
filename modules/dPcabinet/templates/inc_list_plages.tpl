<script>
  Calendar.regField(getForm("FilterPlage").date, null, {noView: true});
</script>

<form name="FilterPlage" action="?" method="get">
  <table class="form">
    <tr>
      <td class="button narrow">
        <a href="#1" onclick="updatePlage('{{$pdate}}')">&lt;&lt;&lt;</a>
        <strong>
          {{if $period == "day"  }}{{$refDate|date_format:" %A %d %B %Y"}}{{/if}}
          {{if $period == "week" || $period == "4weeks"}}{{$refDate|date_format:" semaine du %d %B %Y (%U)"}}{{/if}}
          {{if $period == "month"}}{{$refDate|date_format:" %B %Y"}}{{/if}}
        </strong>
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="updatePlage( $V(this) )" />
        <a href="#1" onclick="updatePlage('{{$ndate}}')">&gt;&gt;&gt;</a>
      </td>
    </tr>
</form>

<table class="tbl">
      <tr>
        <th style="width: 7em;">{{mb_title class=CPlageconsult field=date}}</th>
        <th>{{mb_title class=CPlageconsult field=chir_id}}</th>
        <th>{{mb_title class=CPlageconsult field=libelle}}</th>
        <th colspan="2">{{tr}}Status{{/tr}}</th>
      </tr>
      {{foreach from=$listPlage item=_plage}}
        <tr class="plage{{if $_plage->_id == $plageconsult_id && !$multiple}} selected{{/if}}" id="plage-{{$_plage->_id}}" >
          <td {{if in_array($_plage->date, $bank_holidays)}}style="background: #fc0"{{/if}}>
            {{mb_include template=inc_plage_etat multiple=$multiple}}
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
          <td colspan="{{if $multiple}}6{{else}}5{{/if}}" class="empty">{{tr}}CPlageconsult.none{{/tr}}</td>
        </tr>
      {{/foreach}}
    </table>