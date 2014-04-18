{{mb_default var=simple_view value=0}}
{{mb_script module=patients script=constants_graph ajax=true}}

<script type="text/javascript">
const_ids = {{$const_ids|@json}};
keys_selection = {{$custom_selection|@array_keys|@json}};
paginate = {{$paginate|@json}};

newConstants = function(context_guid) {
  var url = new Url('patients', 'httpreq_vw_form_constantes_medicales');
  url.addParam("context_guid", context_guid);
  url.addParam("selection[]", keys_selection);
  url.addParam("patient_id", '{{$patient->_id}}');
  url.requestUpdate('constantes-medicales-form', {onComplete: initCheckboxes.curry()});
};

editConstants = function(const_id, context_guid, start) {
  var url = new Url('patients', 'httpreq_vw_form_constantes_medicales');
  url.addParam("const_id", const_id);
  url.addParam("context_guid", context_guid);
  url.addParam("start", start || 0);
  url.addParam("selection[]", keys_selection);
  url.addParam("patient_id", '{{$patient->_id}}');
  url.requestUpdate('constantes-medicales-form', {onComplete: window.oGraphs.initCheckboxes});
};

Main.add(function () {
  var graphs_data = {{$graphs_data|@json}};
  window.oGraphs = new ConstantsGraph(graphs_data, {{$min_x_index}}, {{$min_x_value}}, false, '{{$context_guid}}', '{{$display.mode}}', {{$display.time}}, {{$drawn_constants|@json}}, {{$graphs_structure|@json}});
  window.oGraphs.draw();
  ViewPort.SetAvlHeight('tab-constantes-medicales', 1.0);
});

loadConstantesMedicales  = function(context_guid) {
  var url = new Url("patients", "httpreq_vw_constantes_medicales"),
      container = $("constantes-medicales") || $("constantes") || $("Constantes")|| $("constantes-tri"); // case sensitive ?

  url.addParam("context_guid", '{{$context_guid}}');
  url.addParam("patient_id", '{{$patient->_id}}');
  url.addParam("selection[]", keys_selection);
  url.addParam("selected_context_guid", context_guid);
  url.addParam("paginate", window.paginate || 0);
  url.requestUpdate(container);
};

refreshFiches = function(sejour_id) {
  var url = new Url("soins", "ajax_vw_fiches");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate("tab-fiches");
}
</script>

<table class="tbl" {{if $print || $simple_view}}style="display: none;"{{/if}}>
  <tr>
    <th colspan="10" class="title">
      Constantes médicales dans le cadre de: 
      <br />
      {{if !$can_select_context}}
        {{$context}}
      {{else}}
        <select name="context" onchange="loadConstantesMedicales($V(this));">
          <option value="all" {{if $all_contexts}}selected="selected"{{/if}}>Tous les contextes</option> 
          {{foreach from=$list_contexts item=curr_context}}
            <option value="{{$curr_context->_guid}}" 
            {{if !$all_contexts && $curr_context->_guid == $context->_guid}}selected="selected"{{/if}}
            {{if !$all_contexts && $curr_context->_guid == $context_guid}}style="font-weight:bold;"{{/if}}
            >{{$curr_context}}</option>
          {{/foreach}}
        </select>
      {{/if}}
    </th>
  </tr>
</table>

{{if $print}}
  <div id="constantes-medicales-graph" style="text-align: center;"></div>
{{else}}
   <script type="text/javascript">
     Main.add(function(){
      Control.Tabs.create("surveillance-tab");
     });
   </script>

    <ul class="control_tabs" id="surveillance-tab">
      <li>
        <a href="#tab-constantes-medicales">Constantes</a>
      </li>
      
      {{if $context instanceof CSejour && !$simple_view}}
        {{if "forms"|module_active}}
          <li>
            <a href="#tab-ex_class-list" onmousedown="this.onmousedown=null; ExObject.loadExObjects('{{$context->_class}}', '{{$context->_id}}', 'tab-ex_class-list', 0)">
              Formulaires
            </a>
          </li>
        {{/if}}
        
        <li>
          <a href="#tab-fiches" onmousedown="refreshFiches('{{$context->_id}}');">Fiches</a>
        </li>
      {{/if}}
    </ul>
    <hr class="control_tabs" />
      
  <table class="main" id="tab-constantes-medicales">
    <tr>
      <td class="narrow" id="constantes-medicales-form" style="width: 30%;
      ">
        {{include file="inc_form_edit_constantes_medicales.tpl" context_guid=$context_guid}}
      </td>
      <td id="constantes-medicales-graphs" style="width: 69%;">
        {{unique_id var=uniq_id_constantes}}
         
        <script type="text/javascript">
          Main.add(function(){
            Control.Tabs.create("tabs-constantes-graph-table", true);
          });
        </script>
        
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
                <select name="count_constantes" onchange="refreshConstantesMedicales('{{$context_guid}}', 1, $V(this))" style="margin: 0;">
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
        <hr class="control_tabs" />
        
        {{if $const_ids|@count == $count}}
          <div class="small-warning">
            Le nombre de constantes affichées est limité à {{$count}}.
          </div>
        {{/if}}
          
        <div id="constantes-graph" style="min-height: 290px;">
          <button class="hslip notext" style="float: left;" title="Afficher/Cacher le formulaire" onclick="$('constantes-medicales-form').toggle();" type="button">
            Formulaire constantes
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
      </td>
    </tr>
  </table>
  
  <div id="tab-ex_class-list" style="display: none;"></div>
  <div id="tab-fiches" style="display: none;"></div>
{{/if}}