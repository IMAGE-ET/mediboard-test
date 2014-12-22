<script>
  Main.add(function() {
    var tabs = Control.Tabs.create('tabs-configure', true);
  });
</script>

<table class="main">
  <tr>
    <th class="title" colspan="2">Statistiques d'utilisation de l'UF: {{$uf->_view}}</th>
  </tr>
  <tr>
    <th>{{mb_label class=CUniteFonctionnelle field=type}}</th>
    <td>{{mb_value object=$uf field=type}}</td>
  </tr>
</table>

<ul id="tabs-configure" class="control_tabs">
  {{foreach from=$type_affectations key=type item=tab_type}}
    <li><a href="#{{$type}}" {{if $tab_type|@count == 0}}class="empty"{{/if}}>{{tr}}{{$type}}{{/tr}} ({{$tab_type|@count}})</a></li>
  {{/foreach}}
</ul>

{{foreach from=$type_affectations key=type item=tab_type}}
  <div id="{{$type}}" style="display: none;">
    <table class="tbl">
      <tr>
        <th>{{tr}}{{$type}}{{/tr}}</th>
      </tr>
      {{foreach from=$tab_type item=_affectation}}
        <tr>
          <td>{{$_affectation->_ref_object->_view}}</td>
        </tr>
      {{/foreach}}
    </table>
  </div>
{{/foreach}}
