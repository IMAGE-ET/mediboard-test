<script>
  Main.add(function () {
    PairEffect.initGroup("serviceEffect");
  });
</script>

<table id="list_services" class="main tbl">
  <tr>
    <th colspan="8" class="title">
      <span style="float:left">
        <button type="button" onclick="Infrastructure.addeditService('0')" class="button new compact">
          {{tr}}CService-title-create{{/tr}}
        </button>
      </span>
      {{tr}}CService.all{{/tr}}
    </th>
  </tr>
  {{foreach from=$services item=_service}}
    <tr id="{{$_service->_guid}}-trigger">
      <td colspan="8">
        <button class="button edit notext compact" onclick="Event.stop(event); Infrastructure.addeditService('{{$_service->_id}}')"></button>
        {{mb_value object=$_service field=nom}}
        <span>
          ({{$_service->_ref_chambres|@count}} chambre(s))
        </span>
        <span class="compact">
          {{if $_service->description}}
            - {{$_service->description|spancate:150}}
          {{/if}}
        </span>
      </td>
    </tr>
    <tbody class="serviceEffect" id="{{$_service->_guid}}" style="display:none;">
    <tr>
      <th class="category" colspan="8">
        <button class="button add compact" onclick="Infrastructure.addeditChambre('0', {{$_service->_id}})" style="float:left;"> {{tr}}CChambre-title-create{{/tr}}</button>
        {{tr}}CChambre-all{{/tr}} du service {{$_service->_view}}</th>
    </tr>
    <tr>
      <th class="section">{{mb_title class=CChambre field=rank}}</th>
      <th class="section">{{mb_title class=CChambre field=nom}}</th>
      <th class="section">{{tr}}CChambre-back-lits{{/tr}}</th>
      <th class="section">{{mb_title class=CChambre field=caracteristiques}}</th>
      <th class="section">{{mb_title class=CChambre field=lits_alpha}}</th>
      <th class="section">{{mb_title class=CChambre field=is_waiting_room}}</th>
      <th class="section">{{mb_title class=CChambre field=is_examination_room}}</th>
      <th class="section">{{mb_title class=CChambre field=is_sas_dechoc}}</th>
    </tr>
      {{foreach from=$_service->_ref_chambres item=_chambre}}
       {{mb_include module=dPhospi template=inc_vw_chambre_line}}
      {{foreachelse}}
        <tr>
          <td colspan="8" class="empty">{{tr}}CChambre.none{{/tr}}</td>
        </tr>
      {{/foreach}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="8" class="empty">{{tr}}CChambre.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
