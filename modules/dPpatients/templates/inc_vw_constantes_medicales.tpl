{{mb_default var=simple_view value=0}}
{{mb_script module=patients script=constants_graph ajax=true}}

<script type="text/javascript">
  const_ids = {{$const_ids|@json}};
  keys_selection = {{$custom_selection|@array_keys|@json}};
  paginate = {{$paginate|@json}};

  newConstants = function(context_guid) {
    window.oGraphs.getHiddenGraphs();
    var url = new Url('patients', 'httpreq_vw_form_constantes_medicales');
    url.addParam("context_guid", context_guid);
    url.addParam("selection[]", keys_selection);
    url.addParam("patient_id", '{{$patient->_id}}');
    url.requestUpdate('constantes-medicales-form', {onComplete: function() { window.oGraphs.initCheckboxes();}});
  };

  editConstants = function(const_id, context_guid, start) {
    window.oGraphs.getHiddenGraphs();
    var url = new Url('patients', 'httpreq_vw_form_constantes_medicales');
    url.addParam("const_id", const_id);
    url.addParam("context_guid", context_guid);
    url.addParam("start", start || 0);
    url.addParam("selection[]", keys_selection);
    url.addParam("patient_id", '{{$patient->_id}}');
    url.requestUpdate('constantes-medicales-form', {onComplete: function() { window.oGraphs.initCheckboxes();}});
  };

  Main.add(function () {
    var graphs_data = {{$graphs_data|@json}};
    window.oGraphs = new ConstantsGraph(graphs_data, {{$min_x_index}}, {{$min_x_value}}, false, '{{$context_guid}}', '{{$display.mode}}', {{$display.time}}, {{$hidden_graphs|@json}}, {{$graphs_structure|@json}});
    window.oGraphs.draw();

    if (window.tabsConsult || window.tabsConsultAnesth) {
      Control.Tabs.setTabCount("constantes-medicales", '{{$total_constantes}}');
    }

    {{if !$print}}
      {{assign var=_context value=$context}}
      {{assign var=_readonly value=false}}
      {{if !$_context || $context->_guid != $context_guid}}
      {{assign var=_readonly value=true}}
      {{/if}}
      {{if !$_context}}
      {{assign var=_context value=$patient}}
      {{/if}}

      Control.Tabs.create("surveillance-tab", true, {
        afterChange: function (container) {
          switch (container.id) {
            case "tab-constantes-medicales":
              break;

            case "tab-ex_class-list":
              ExObject.loadExObjects(
                '{{$_context->_class}}',
                '{{$_context->_id}}',
                'tab-ex_class-list',
                0,
                null,
                {
                  readonly: {{$_readonly|ternary:1:0}},
                  creation_context_class: "{{$current_context->_class}}",
                  creation_context_id:    "{{$current_context->_id}}"

                  {{if $_context instanceof CPatient}}
                  ,
                  cross_context_class: "{{$_context->_class}}",
                  cross_context_id:    "{{$_context->_id}}"
                  {{/if}}
                }
              );
              break;

            case "tab-fiches":
            {{if $context instanceof CSejour}}
              refreshFiches('{{$context->_id}}');
            {{/if}}
              break;
          }
        }
      });

      Control.Tabs.create("tabs-constantes-graph-table", true);
    {{/if}}

    var header_constants = $('header-constants');
    var content_dossier = $('content-dossier-soins');
    if (!content_dossier) {
      ViewPort.SetAvlHeight('constantes-medicales', 1.0);
      content_dossier = $('constantes-medicales');
    }
    var tab_constants = $('tab-constantes-medicales')
    tab_constants.setStyle({height: (content_dossier.getHeight() - header_constants.getHeight()) + 'px'});
  });

  loadConstantesMedicales  = function(context_guid) {
    var url = new Url("patients", "httpreq_vw_constantes_medicales"),
        container = $("constantes-medicales") || $("constantes") || $("Constantes")|| $("constantes-tri"); // case sensitive ?

    url.addParam("context_guid", '{{$context_guid}}');
    url.addParam("patient_id", '{{$patient->_id}}');
    url.addParam("selection[]", keys_selection);
    url.addParam("selected_context_guid", context_guid);
    url.addParam("paginate", window.paginate || 0);
    url.addParam("count", $V($('count_constantes')));
    if (window.oGraphs) {
          url.addParam('hidden_graphs', JSON.stringify(window.oGraphs.getHiddenGraphs()));
    }
    url.requestUpdate(container);
  };

  refreshFiches = function(sejour_id) {
    var url = new Url("soins", "ajax_vw_fiches");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate("tab-fiches");
  }
</script>

<div id="header-constants">
  <table class="tbl" {{if $print || $simple_view}}style="display: none;"{{/if}}>
    <tr>
      <th colspan="10" class="title">
        Surveillance dans le cadre de:
        <br />
        {{if !$can_select_context}}
          {{$context}}
        {{else}}
          <select id="select_context" name="context" onchange="loadConstantesMedicales($V(this));">
            <option value="all" {{if $all_contexts}} selected {{/if}}>Tous les contextes du patient</option>
            {{foreach from=$list_contexts item=curr_context}}
              <option value="{{$curr_context->_guid}}"
              {{if !$all_contexts && $curr_context->_guid == $context->_guid}}selected="selected"{{/if}}
              {{if !$all_contexts && $curr_context->_guid == $context_guid}}style="font-weight:bold;"{{/if}}
              >
                {{if !$all_contexts && $curr_context->_guid == $context_guid}}&rArr;{{/if}}
                {{$curr_context}}
              </option>
            {{/foreach}}
          </select>
        {{/if}}
      </th>
    </tr>
    {{if $infos_patient}}
      <tr>
        {{mb_include module=soins template=inc_infos_patients_soins add_class=1}}
      </tr>
    {{/if}}
  </table>

  {{if $print}}
    <div id="constantes-medicales-graph" style="text-align: center;"></div>
    </div>
  {{else}}


    {{* Contexte de la page  : $current_context *}}
    {{* Contexte sélectionné : $_context *}}

    <ul class="control_tabs" id="surveillance-tab">
      <li>
        <a href="#tab-constantes-medicales">Constantes</a>
      </li>

      {{if !$simple_view}}
        {{if "forms"|module_active}}
          <li>
            <a href="#tab-ex_class-list">Formulaires</a>
          </li>
        {{/if}}

        {{if $context instanceof CSejour}}
        <li>
          <a href="#tab-fiches">Fiches</a>
        </li>
        {{/if}}
      {{/if}}
    </ul>
  </div>

  <div id="tab-constantes-medicales" style="position: relative;">
    <div id="constantes-medicales-form" style="position: absolute; top: 0px; left: 0px; width: 30%; height: 100%;">
      {{include file="inc_form_edit_constantes_medicales.tpl" context_guid=$context_guid}}
    </div>
    <div id="constantes-medicales-graphs" style="position: absolute; top: 0px; left: 30%; width: 70%; height: 100%; overflow-y: auto;">
      {{unique_id var=uniq_id_constantes}}

      {{* {{if $paginate}}
        {{mb_include module=system template=inc_pagination total=$total_constantes current=$start change_page="changePage$uniq_id_constantes" step=$count}}
      {{/if}} *}}

    <ul class="control_tabs small" id="tabs-constantes-graph-table">
      <li><a href="#constantes-graph">Graphiques</a></li>
      <li><a href="#constantes-table">Tableau</a></li>
      {{if $paginate}}
        <li>
          <label>
            Afficher les
            <select id="count_constantes" name="count_constantes" onchange="loadConstantesMedicales($V($('select_context')));" style="margin: 0;">
              {{assign var=_counts value=","|@explode:"50,100,200,500"}}
              {{foreach from=$_counts item=_count}}
                <option value="{{$_count}}" {{if $count == $_count}} selected {{/if}}>{{$_count}}</option>
              {{/foreach}}
            </select>
            derniers ({{$total_constantes}} au total)
          </label>
        </li>
      {{/if}}
    </ul>

    {{if $const_ids|@count == $count}}
      <div class="small-warning">
        Le nombre de constantes affichées est limité à {{$count}}.
      </div>
    {{/if}}

    <div id="constantes-graph" style="min-height: 290px;">
      <button class="hslip notext" style="float: left;" onclick="$('constantes-medicales-form').toggle();" type="button">
        Afficher/Cacher le formulaire
      </button>

      <button id="constantes-medicales-graph-before" class="left" style="float: left;"       onclick="window.oGraphs.shift('before');">Avant</button>
      <button id="constantes-medicales-graph-after"  class="right rtl" style="float: right;" onclick="window.oGraphs.shift('after');">Après</button>
      <br/>

      <div id="graphs" style="clear: both;">
        {{foreach from=$graphs_data key=_rank item=_graphs_for_rank}}
          {{foreach from=$_graphs_for_rank key=_graph_id item=_graph}}
            <div id="graph_row_{{$_rank}}_{{$_graph_id}}" style="display: inline-block;">
              <table class="layout">
                <tr>
                  <td>
                    <p style="text-align: center"><strong>{{$_graph.title|utf8_decode}}</strong></p>
                    <div id="placeholder_{{$_rank}}_{{$_graph_id}}" style="width: {{$_graph.width}}px; height: 175px; margin-bottom: 5px; margin-left: {{$_graph.margin_left}}px;"></div>
                  </td>
                  <td style="padding-top: 1.2em; width: 10em">
                    <div id="legend_{{$_rank}}_{{$_graph_id}}"></div>
                  </td>
                </tr>
              </table>
            </div>
          {{/foreach}}
        {{/foreach}}
      </div>
    </div>

    <div id="constantes-table" style="display: none; text-align: left;">
      <button class="change" onclick="$('constantes-table').down('.contantes-horizontal').toggle(); $('constantes-table').down('.contantes-vertical').toggle()">
        Changer l'orientation
      </button>

      <div class="contantes-horizontal" style="display: none;">
      {{mb_include module=patients template=print_constantes}}
      </div>
      <div class="contantes-vertical">
      {{mb_include module=patients template=print_constantes_vert}}
      </div>
    </div>
    </div>
  </div>

  <div id="tab-ex_class-list" style="display: none;"></div>
  <div id="tab-fiches" style="display: none;"></div>
{{/if}}