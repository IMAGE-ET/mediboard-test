<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("cancelled-operations");
});
</script>

<h2>{{$title}}</h2>

<ul class="control_tabs" id="cancelled-operations">
{{foreach from=$list item=month key=month_label}}
  <li><a href="#month-{{$month_label|replace:'/':'-'}}">
    {{$month_label}}
  </a></li>
{{/foreach}}
</ul>
<hr class="control_tabs" />
  
{{foreach from=$list item=month key=month_label}}
  <table id="month-{{$month_label|replace:'/':'-'}}" class="main tbl" style="display: none;">
    <tr>
      <th>{{mb_title class=COperation field=date}}</th>
      <th>{{mb_title class=COperation field=chir_id}}</th>
      <th>{{mb_title class=CSejour field=patient_id}}</th>
      <th>{{mb_title class=CSejour field=type}}</th>
      <th>{{mb_title class=COperation field=libelle}}</th>
      <th>{{mb_title class=COperation field=codes_ccam}}</th>
    </tr>
    
  {{foreach from=$month item=salle key=salle_label}}
    <tr>
      <th colspan="10">{{$salle_label}}</th>
    </tr>
    {{foreach from=$salle item=op}}
      <tr>
        <td>
          <a href="#1" class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$op->_guid}}')">
            {{mb_value object=$op->_ref_plageop field=date}}
          </a>
        </td>
        <td>{{$op->_ref_chir}}</td>
        <td>{{$op->_ref_patient}}</td>
        <td>{{mb_value object=$op->_ref_sejour field=type}}</td>
        <td>{{$op->libelle}}</td>
        <td>{{$op->codes_ccam}}</td>
      </tr>
    {{/foreach}}
  {{/foreach}}
  
  </table>
{{/foreach}}