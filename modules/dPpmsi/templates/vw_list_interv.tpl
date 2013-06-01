<script type="text/javascript">

Main.add(function () {
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
});

</script>
<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-category', true);
});
</script>

<ul id="tabs-category" class="control_tabs">
  {{foreach from=$counts key=category item=count}}
  <li>
    <a href="#{{$category}}"
      {{if !$count.total}}class="empty"{{/if}} 
      {{if $count.facturees != $count.total}}class="wrong"{{/if}}>
      {{tr}}COperation-{{$category}}{{/tr}}
      <small>
       {{if $count.facturees == $count.total}}
      ({{$count.total}})
       {{else}}
      ({{$count.facturees}}/{{$count.total}})
       {{/if}}
     </small>
    </a>
  </li>
  {{/foreach}}
</ul>

<hr class="control_tabs" />

<table class="tbl">
  <tr>
    <th class="title" colspan="9">
      {{$date|date_format:$conf.longdate}}
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
  <tr>
    <th>{{mb_title class=$operation field=facture}}</th>
    <th class="narrow">{{mb_title class=CSejour field=_NDA}}</th>
    <th>{{mb_label object=$operation field=chir_id}}</th>
    <th>{{mb_label class=CSejour field=patient_id}}</th>
    <th>{{mb_label class=$operation field=time_operation}}</th>
    <th>
      {{mb_label object=$operation field=libelle}} +
      {{mb_label object=$operation field=codes_ccam}}
    </th>
    <th>Docs</th>
    <th>{{mb_title object=$operation field=labo}}</th>
    <th>{{mb_title object=$operation field=anapath}}</th>
  </tr>
  
  <tbody id="operations" style="display: none;">
  {{foreach from=$plages item=_plage}}
  {{foreach from=$_plage->_ref_operations item=_operation}}
  {{mb_include template=inc_list_interv}}
  {{/foreach}}
  {{/foreach}}
  </tbody>
  
  <tbody id="horsplages" style="display: none;">
  {{foreach from=$horsplages item=_operation}}
  {{mb_include template=inc_list_interv}}
  {{/foreach}}
  </tbody>
</table>