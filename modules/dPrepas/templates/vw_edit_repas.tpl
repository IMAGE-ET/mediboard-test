<script type="text/javascript">
function viewplat(menu_id){
  var url = new Url;
  url.setModuleAction("dPrepas", "httpreq_vw_menu");
  {{if $repas->repas_id}}
  url.addParam("repas_id", {{$repas->repas_id}});  
  {{/if}}
  url.addParam("menu_id", menu_id);
  url.requestUpdate('listPlat');
}

{{if $repas->repas_id}}
function pageMain() {
  viewplat({{$repas->menu_id}});
}
{{/if}}
</script>
<form name="editMenu" action="./index.php?m={{$m}}&amp;tab=vw_edit_repas" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="dPrepas" />
<input type="hidden" name="dosql" value="do_repas_aed" />
<input type="hidden" name="repas_id" value="{{if $repas}}{{$repas->repas_id}}{{/if}}" />
<input type="hidden" name="affectation_id" value="{{$affectation->affectation_id}}" />
<input type="hidden" name="date" value="{{$date}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if $repas->repas_id}}
    <th class="title modify" colspan="3">
      {{tr}}msg-CRepas-title-modify{{/tr}}
    {{else}}
    <th class="title" colspan="3">
      {{tr}}msg-CRepas-title-create{{/tr}}
    {{/if}}
    </th>
  </tr>
  
  <tr>
    <th>
      <strong>Chambre</strong>
    </th>
    <td>
      {{$affectation->_ref_lit->_view}}
    </td>
    <td rowspan="4" class="halfPane" id="listPlat"></td>
  </tr>

  <tr>
    <th>
      <strong>{{tr}}Date{{/tr}}</strong>
    </th>
    <td>
      {{$date|date_format:"%A %d %b %Y"}}
    </td>
  </tr>  
  
  <tr>
    <th>
      <strong>Type de Repas</strong>
    </th>
    <td>
      {{$typeRepas->nom}}
    </td>
  </tr>  
  
  <tr>
    <td colspan="2" class="button">
      {{if $listRepas|@count}}
        <table class="tbl">
          <tr>
            <th class="category">Menu</th>
            <th class="category">Diabétique</th>
            <th class="category">Sans sel</th>
            <th class="category">Sans résidu</th>
          </tr>
          {{foreach from=$listRepas item=curr_repas}}
          <tr>
            <td class="text">
              <a href="#" onclick="viewplat({{$curr_repas->menu_id}})">
                {{$curr_repas->nom}}
              </a>
            </td>
            <td class="button">{{if $curr_repas->diabete}}<strong>{{tr}}Yes{{/tr}}</strong>{{/if}}</td>
            <td class="button">{{if $curr_repas->sans_sel}}<strong>{{tr}}Yes{{/tr}}</strong>{{/if}}</td>
            <td class="button">{{if $curr_repas->sans_residu}}<strong>{{tr}}Yes{{/tr}}</strong>{{/if}}</td>
          </tr>
          {{/foreach}}
        </table>
      {{else}}
        Pas de Repas disponible
      {{/if}}
    </td>
  </tr>  
</table>
</form>