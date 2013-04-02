<!-- $Id$ -->
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPplanningOp" script="operation"}}

<script type="text/javascript">

function updateListOperations(date) {
  var url = new Url("dPplanningOp", "httpreq_vw_list_operations");
  url.addParam("pratSel" , "{{$selPrat}}");
  url.addParam("canceled", "{{$canceled}}");
  url.addParam("date"    , date);
  url.requestUpdate('operations');
  
  var row = $("date-"+date);
  if (row) {
    row.addUniqueClassName("selected");
  }
  
  return false;
}

Main.add(function () {
  updateListOperations("{{$date}}");
});

</script>
<table class="main">
  
  <tr>
    <th style="height: 16px;">
      <form action="?" name="selectPraticien" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <select name="selPrat" onchange="this.form.submit()" style="max-width: 150px;">
        <option value="-1">&mdash; Choisir un praticien</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$listPrat selected=$selPrat}}
      </select>
      </form>
    </th>
    <td rowspan="3" class="greedyPane" style="vertical-align:top;">
      <div id="operations">
        <div class="small-info">
          Cette vue affiche la liste des interventions pour le jour sélectionné.
        </div>
      </div>
    </td>
  </tr>
  
  <tr>
    <th style="height: 16px;">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$lastmonth}}">&lt;&lt;&lt;</a>
      {{$date|date_format:"%B %Y"}}
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$nextmonth}}">&gt;&gt;&gt;</a>
    </th>
  </tr>
  
  <tr>
    <td>
      
      <table class="tbl">
        <tr>
          <th>Date</th>
          <th>Plage</th>
          <th colspan="2">Nb. Opér.</th>
        </tr>
        {{foreach from=$listDays key=_date item=_day}}
        <tbody class="hoverable" id="date-{{$_date|iso_date}}">
        <tr>
          <td style="text-align: right;" rowspan="{{$_day|@count}}">
            <a href="#nothing" onclick="return updateListOperations('{{$_date|iso_date}}')">
              {{$_date|date_format:"%a %d"}}
            </a>
          </td>
          {{foreach from=$_day item=_plage key=curr_key}}
          {{if $_plage.plageop_id && $curr_key != "hors_plage"}}
          <td {{if $_plage.unique_chir}}class="user"{{else}}class="function"{{/if}}>
            {{$_plage.debut|date_format:$conf.time}} à {{$_plage.fin|date_format:$conf.time}}
          </td>
          {{else}}
          <td>
            Hors plage
          </td>
          {{/if}}
          <td align="center">{{$_plage.total|nozero}}</td>
          <td align="center" {{if $_plage.plageop_id && $curr_key != "hors_plage" && $_plage.spec_id}} style="background-color: #{{$_plage.color_function}}"{{/if}}>
            {{if $_plage.plageop_id && $curr_key != "hors_plage" && $_plage.spec_id}}
              <label title="{{$_plage.nom_function}}">{{$_plage.duree|date_format:$conf.time}}</label>
            {{else}}
              {{$_plage.duree|date_format:$conf.time}}
            {{/if}}
          </td>
        </tr>
        <tr>
          {{/foreach}}
        </tr>
        </tbody>
        {{foreachelse}}
        <tr><td class="empty" colspan="3">Aucune plage ni intervention hors plage</td></tr>
        {{/foreach}}
      </table>
      
    </td>
  </tr>
</table>