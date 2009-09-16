"c:\Program Files\WinHTTrack\httrack.exe" "http://localhost/mediboard/?login=1&dialog=1&no_login_info=1&username=admin&password=abc123&m=dPcabinet&a=offline_programme_consult&nb_months=1&chir_id=25" -O c:\Users\Fabien\Desktop\offline -v -N100 -s0 -I0 -C2

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
    $('plage-' + this.currPlage).addClassName("selected");
  }
}

Main.add(function () {
  // Initialisation des onglets
  Control.Tabs.create('month_tabs');
});

</script>

<h3>Dernière mise à jour : {{$smarty.now|date_format:$dPconfig.datetime}}</h3>

<ul id="month_tabs" class="control_tabs">
  {{foreach from=$listPlages key=month_name item=listPlage}}
  <li><a href="#{{$month_name}}_tab">{{$month_name}}</a></li>
  {{/foreach}}
</ul>

<hr class="control_tabs" />
    
{{foreach from=$listPlages key=month_name item=listPlage}}
  <div id="{{$month_name}}_tab" style="display:none">
    <table class="main">
      <tr>
        <td class="halfPane">{{include file="inc_list_plages.tpl"}}</td>
        <td class="halfPane">
          {{foreach from=$listPlage item=plage}}
          {{assign var="listPlace" value=$plage->_listPlace}}
          <div id="places-{{$plage->_id}}" style="display:none">
            {{include file="inc_list_places.tpl"}}
          </div>
          {{/foreach}}
        </td>
      </tr>
    </table>
  </div>
{{/foreach}}