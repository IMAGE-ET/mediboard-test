<script>
  Main.add(function(){
    Control.Tabs.create("supervision-tab", true);
  });
</script>

<table class="main layout">
  <tr>
    <td class="narrow">
      <ul class="control_tabs_vertical small" id="supervision-tab" style="white-space: nowrap;">
        <li>
          <a href="#tab-graphs" {{if $graphs|@count == 0}} class="empty" {{/if}}>
            Graphiques <small>({{$graphs|@count}})</small>
          </a>
        </li>

        <li>
          <a href="#tab-timed_data" {{if $timed_data|@count == 0}} class="empty" {{/if}}>
            Données horodatées <small>({{$timed_data|@count}})</small>
          </a>
        </li>

        <li>
          <a href="#tab-timed_pictures" {{if $timed_pictures|@count == 0}} class="empty" {{/if}}>
            Images <small>({{$timed_pictures|@count}})</small>
          </a>
        </li>

        {{if "patientMonitoring"|module_active}}
          <li>
            <a href="#tab-instant_data" {{if $instant_data|@count == 0}} class="empty" {{/if}}>
              Données instant. <small>({{$instant_data|@count}})</small>
            </a>
          </li>
        {{/if}}

        <li>
          <a href="#tab-packs" {{if $packs|@count == 0}} class="empty" {{/if}}>
            Packs <small>({{$packs|@count}})</small>
          </a>
        </li>
      </ul>
    </td>

    <td class="list-container">

      <div id="tab-graphs" style="display: none;">
        <button class="new" onclick="SupervisionGraph.editGraph(0)" style="float: right;">
          {{tr}}CSupervisionGraph-title-create{{/tr}}
        </button>

        <table class="main tbl" style="clear: both;">
          {{foreach from=$graphs item=_graph}}
            <tr id="list-{{$_graph->_guid}}" class="{{if $_graph->disabled}} opacity-50 {{/if}}">
              <td>
                <a href="#{{$_graph->_guid}}" onclick="return SupervisionGraph.editGraph({{$_graph->_id}})">
                  {{mb_value object=$_graph field=title}}
                </a>
              </td>
              <td class="compact">
                {{foreach from=$_graph->_back.axes item=_axis}}
                  <div style="clear: both;">
                    {{foreach from=$_axis->_back.series|@array_reverse item=_series}}
                      <span style="float: right; width: 4px; height: 9px; background-color: #{{$_series->color}}; margin-left: 1px;"></span>
                    {{/foreach}}
                    {{mb_include module=patients template=inc_axis_symbol axis=$_axis small=true}}
                    {{$_axis}}
                  </div>
                {{/foreach}}
              </td>
            </tr>
            {{foreachelse}}
            <tr>
              <td class="empty" colspan="2">{{tr}}CSupervisionGraph.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      </div>

      <div id="tab-timed_data" style="display: none;">
        <button class="new" onclick="SupervisionGraph.editTimedData(0)" style="float: right;">
          {{tr}}CSupervisionTimedData-title-create{{/tr}}
        </button>

        <table class="main tbl" style="clear: both;">
          {{foreach from=$timed_data item=_timed_data}}
            <tr id="list-{{$_timed_data->_guid}}" class="{{if $_timed_data->disabled}} opacity-50 {{/if}}">
              <td>
                <a href="#edit-{{$_timed_data->_guid}}" onclick="return SupervisionGraph.editTimedData({{$_timed_data->_id}})">
                  {{mb_value object=$_timed_data field=title}}
                </a>
              </td>
            </tr>
            {{foreachelse}}
            <tr>
              <td class="empty">{{tr}}CSupervisionTimedData.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      </div>

      <div id="tab-timed_pictures" style="display: none;">
        <button class="new" onclick="SupervisionGraph.editTimedPicture(0)" style="float: right;">
          {{tr}}CSupervisionTimedPicture-title-create{{/tr}}
        </button>

        <table class="main tbl" style="clear: both;">
          {{foreach from=$timed_pictures item=_timed_picture}}
            <tr id="list-{{$_timed_picture->_guid}}" class="{{if $_timed_picture->disabled}} opacity-50 {{/if}}">
              <td>
                <a href="#edit-{{$_timed_picture->_guid}}" onclick="return SupervisionGraph.editTimedPicture({{$_timed_picture->_id}})">
                  {{mb_value object=$_timed_picture field=title}}
                </a>
              </td>
            </tr>
            {{foreachelse}}
            <tr>
              <td class="empty">{{tr}}CSupervisionTimedPicture.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      </div>

      <div id="tab-instant_data" style="display: none;">
        <button class="new" onclick="SupervisionGraph.editInstantData(0)" style="float: right;">
          {{tr}}CSupervisionInstantData-title-create{{/tr}}
        </button>

        <table class="main tbl" style="clear: both;">
          {{foreach from=$instant_data item=_instant_data}}
            <tr id="list-{{$_instant_data->_guid}}" class="{{if $_instant_data->disabled}} opacity-50 {{/if}}">
              <td>
                <a href="#edit-{{$_instant_data->_guid}}" onclick="return SupervisionGraph.editInstantData({{$_instant_data->_id}})">
                  {{mb_value object=$_instant_data field=title}}
                </a>
              </td>
            </tr>
            {{foreachelse}}
            <tr>
              <td class="empty">{{tr}}CSupervisionInstantData.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      </div>

      <div id="tab-packs" style="display: none;">
        <button class="new" onclick="SupervisionGraph.editPack(0)" style="float: right;">
          {{tr}}CSupervisionGraphPack-title-create{{/tr}}
        </button>

        <table class="main tbl" style="clear: both;">
          {{foreach from=$packs item=_pack}}
            <tr id="list-{{$_pack->_guid}}" class="{{if $_pack->disabled}} opacity-50 {{/if}}">
              <td colspan="2">
                <a href="#edit-{{$_pack->_guid}}" onclick="return SupervisionGraph.editPack({{$_pack->_id}})">
                  {{mb_value object=$_pack field=title}}
                </a>
              </td>
            </tr>
            {{foreachelse}}
            <tr>
              <td class="empty" colspan="2">{{tr}}CSupervisionGraphPack.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      </div>
    </td>
  </tr>
</table>
