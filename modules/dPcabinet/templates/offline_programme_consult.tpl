<script type="text/javascript">

var PlageConsult = {
  currPlage: 0,
  changePlage: function(plage_id) {
    if(this.currPlage) {
      $('places-' + this.currPlage).hide();
      $('plage-'+this.currPlage).removeClassName("selected");
    }
    this.currPlage = plage_id;
    $('places-' + this.currPlage).show();
    ViewPort.SetAvlHeight($('places-' + this.currPlage), 1);
    $('plage-' + this.currPlage).addClassName("selected");
  }
}

Main.add(function () {
  Control.Tabs.create('month_tabs').activeLink.onmouseup();
});

</script>


<h3>
  Dernière mise à jour : {{$smarty.now|date_format:$conf.datetime}}
  &mdash;
  Période de {{$period_count-1}} {{tr}}{{$period_type}}{{/tr}}
  du {{$date_min|date_format:$conf.date}} au {{$date_max|date_format:$conf.date}}
</h3>

<div>
  Rendez-vous de :
{{foreach from=$praticiens item=_praticien}}
  <span class="mediuser" style="border-color: #{{$_praticien->_ref_function->color}};">
    {{$_praticien}}
  </span>
  {{foreachelse}}
<span class="empty">{{tr}}None{{/tr}}</span>
{{/foreach}}
</div>

<br />

<ul id="month_tabs" class="control_tabs">
  {{foreach from=$listPlages key=month_name item=listPlage}}
  <li>
    <a href="#{{$month_name}}_tab" onmouseup="ViewPort.SetAvlHeight.defer('{{$month_name}}_div', 1);">
      {{$month_name}}
      <small>({{$totals.$month_name.affected}} / {{$totals.$month_name.total}})</small>
      <button onclick="$('{{$month_name}}_tab').print()" class="print notext">{{tr}}Print{{/tr}}</button>
    </a>
  </li>
  {{/foreach}}
  <li>
    <button onclick="window.tabs.print()" class="print">{{tr}}Print{{/tr}}</button>
  </li>
</ul>

<hr class="control_tabs" />

<script type="text/javascript">
Main.add(function() {
  $$('.plages').each(function(element) {
    ViewPort.SetAvlHeight(element, 1);
  });
})
</script>

{{foreach from=$listPlages key=month_name item=listPlage}}
  <div id="{{$month_name}}_tab" style="display: none;">
    <table class="main">
      <tr>
        <td class="halfPane">
          <div class="plages" id="{{$month_name}}_div" style="overflow-y: auto;">
            {{mb_include template=inc_list_plages}}
          </div>
        </td>
        <td class="halfPane">
          {{foreach from=$listPlage item=plage}}
          {{assign var="listPlace" value=$plage->_listPlace}}
          <div class="places" id="places-{{$plage->_id}}" style="overflow-y: auto; display: none;">
            {{mb_include template=inc_list_places listBefore=null listAfter=null}}
          </div>
          {{/foreach}}
        </td>
      </tr>
    </table>
  </div>
{{/foreach}}