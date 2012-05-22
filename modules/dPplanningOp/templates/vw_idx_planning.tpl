<!-- $Id$ -->
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcompteRendu" script="document"}}

<script type="text/javascript">

function updateListOperations(date) {
  var url = new Url("dPplanningOp", "httpreq_vw_list_operations");
  url.addParam("pratSel", "{{$selPrat}}");
  url.addParam("date"   , date);
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
        {{foreach from=$listPrat item=curr_prat}}
        <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $selPrat}} selected="selected" {{/if}}>
          {{$curr_prat->_view}}
        </option>
        {{/foreach}}
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
        {{foreach from=$listDays key=curr_date item=curr_day}}
        <tbody class="hoverable" id="date-{{$curr_date|iso_date}}">
        <tr>
          <td style="text-align: right;" rowspan="{{$curr_day|@count}}">
            <a href="#nothing" onclick="return updateListOperations('{{$curr_date|iso_date}}')">
              {{$curr_date|date_format:"%a %d"}}
            </a>
          </td>
          {{foreach from=$curr_day item=curr_plage}}
          {{if $curr_plage.plageop_id}}
          <td {{if $curr_plage.unique_chir}}class="user"{{else}}class="function"{{/if}}>
            {{$curr_plage.debut|date_format:$conf.time}} à {{$curr_plage.fin|date_format:$conf.time}}
          </td>
          {{else}}
          <td>
            Hors plage
          </td>
          {{/if}}
          <td align="center">{{$curr_plage.total}}</td>
          <td align="center" {{if $curr_plage.plageop_id && $curr_plage.spec_id}} style="background-color: #{{$curr_plage.color_function}}"{{/if}}>
            {{if $curr_plage.plageop_id && $curr_plage.spec_id}}
              <label title="{{$curr_plage.nom_function}}">{{$curr_plage.duree|date_format:$conf.time}}</label>
            {{else}}
              {{$curr_plage.duree|date_format:$conf.time}}
            {{/if}}
          </td>
        </tr>
        <tr>
          {{/foreach}}
        </tr>
        </tbody>
        {{/foreach}}
      </table>
      
      
    </td>
  </tr>
</table>