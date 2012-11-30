<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("cancelled-operations");
});
</script>

<ul class="control_tabs" id="cancelled-operations">
{{foreach from=$list item=month key=month_label}}
  <li><a href="#month-{{$month_label}}">
    {{$month_label}} ({{$month.total}})
  </a></li>
{{/foreach}}
</ul>
<span style="float: right">
  <form name="intervs" action="?" method="get" onsubmit="alert('toto'); return checkForm(this)">
    <input type="hidden" name="m" value="stats" />
    <input type="hidden" name="tab" value="vw_cancelled_operations" />
    <select name="type_modif" onchange="this.form.submit()">
      <option value="annule" {{if $type_modif == "annule"}}selected="selected"{{/if}}>Interventions annulées le jour même</option>
      <option value="ajoute" {{if $type_modif == "ajoute"}}selected="selected"{{/if}}>Interventions ajoutées le jour même</option>
    </select>
    Jusqu'au {{mb_field class=COperation field="_date_max" value=$date_max form="intervs" canNull="false" register=true onchange="this.form.submit()"}}
  </form>
</span>
<hr class="control_tabs" />
  
{{foreach from=$list item=month key=month_label}}
  <table id="month-{{$month_label}}" class="main tbl" style="display: none;">
    <tr>
      <th>{{mb_title class=COperation field=date}}</th>
      <th>{{mb_title class=COperation field=salle_id}}</th>
      <th>{{mb_title class=COperation field=chir_id}}</th>
      <th>{{mb_title class=CSejour field=patient_id}}</th>
      <th>{{mb_title class=CSejour field=type}}</th>
      <th>{{mb_title class=COperation field=libelle}}</th>
      <th>{{mb_title class=COperation field=rques}}</th>
      <th>{{mb_title class=COperation field=codes_ccam}}</th>
    </tr>
    
    <tr>
      <th colspan="100" class="section">Interventions dans une vacation</th>
    </tr>
    {{foreach from=$month.inPlage item=op}}
      <tr>
        <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$op->_guid}}')">
            {{mb_value object=$op field=_datetime}}
          </span>
        </td>
        <td class="text">{{mb_value object=$op field=salle_id}}</td>
        <td>{{mb_value object=$op field=chir_id}}</td>
        <td class="text">{{mb_value object=$op->_ref_sejour field=patient_id}}</td>
        <td>{{mb_value object=$op->_ref_sejour field=type}}</td>
        <td class="text">{{mb_value object=$op field=libelle}}</td>
        <td class="text">{{mb_value object=$op field=rques}}</td>
        <td>{{mb_value object=$op field=codes_ccam}}</td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="100" class="empty">{{tr}}COperation.none{{/tr}}</td>
      </tr>
    {{/foreach}}
      
    
    <tr>
      <th colspan="100" class="section">Interventions hors plage</th>
    </tr>
    {{foreach from=$month.horsPlage item=op}}
      <tr>
        <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$op->_guid}}')">
            {{mb_value object=$op field=_datetime}}
          </span>
        </td>
        <td>{{mb_value object=$op field=salle_id}}</td>
        <td>{{mb_value object=$op field=chir_id}}</td>
        <td>{{mb_value object=$op->_ref_sejour field=patient_id}}</td>
        <td>{{mb_value object=$op->_ref_sejour field=type}}</td>
        <td class="text">{{mb_value object=$op field=libelle}}</td>
        <td class="text">{{mb_value object=$op field=rques}}</td>
        <td>{{mb_value object=$op field=codes_ccam}}</td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="100" class="empty">{{tr}}COperation.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  
  </table>
{{/foreach}}