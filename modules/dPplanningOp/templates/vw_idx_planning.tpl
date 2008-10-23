<!-- $Id$ -->
{{mb_include_script module="dPcompteRendu" script="modele_selector"}}
{{mb_include_script module="dPcompteRendu" script="document"}}

<script type="text/javascript">

function updateListOperations(date) {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "httpreq_vw_list_operations");

  url.addParam("chirSel" , "{{$selChir}}");
  url.addParam("date"    , date);

  url.requestUpdate('operations', { waitingText:null } );
}

Main.add(function () {
  updateListOperations("{{$date}}");
});

</script>

<table class="main">
  <tr>
    <td style="height: 16px;">
      <form action="?" name="selection" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <label for="selChir">Chirurgien</label>
      <select name="selChir" onchange="this.form.submit()">
        <option value="-1">&mdash; Choisir un chirurgien</option>
        {{foreach from=$listChir item=curr_chir}}
        <option class="mediuser" style="border-color: #{{$curr_chir->_ref_function->color}};" value="{{$curr_chir->user_id}}" {{if $curr_chir->user_id == $selChir}} selected="selected" {{/if}}>
          {{$curr_chir->_view}}
        </option>
        {{/foreach}}
      </select>
      </form>
    </td>
    <td rowspan="3" class="greedyPane" style="vertical-align:top;">
      <div id="operations">
      
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
        <tbody class="hoverable">
        <tr>
          <td align="right" rowspan="{{$curr_day|@count}}">
            <a href="#nothing" onclick="updateListOperations('{{$curr_date|date_format:"%Y-%m-%d"}}')">
              {{$curr_date|date_format:"%a %d"}}
            </a>
          </td>
          {{foreach from=$curr_day item=curr_plage}}
          <td align="center">
            {{if $curr_plage.plageop_id}}
              {{$curr_plage.debut|date_format:"%Hh%M"}} à {{$curr_plage.fin|date_format:"%Hh%M"}}
            {{else}}
              Hors plage
            {{/if}}
          </td>
          <td align="center">{{$curr_plage.total}}</td>
          <td align="center text">
            {{if $curr_plage.plageop_id && $curr_plage.spec_id}}
              Plage de Spécialité
            {{else}}
              {{$curr_plage.duree|date_format:"%Hh%M"}}
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