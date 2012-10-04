<!-- $Id$ -->

<script type="text/javascript">

function popupImport() {
  var url = new Url('planningOp', 'protocole_dhe_import_csv');
  url.popup(800, 600, 'Import des Protocoles de DHE');
}

function popupExport() {
  var formFrom = getForm('selectFrm');
  var formTo = getForm('exportProtocoles');
  $V(formTo.chir_id, $V(formFrom.chir_id));
  $V(formTo.function_id, $V(formFrom.function_id));
  formTo.submit();
}

window.aProtocoles = {
  sejour: {},
  interv: {}
};
  
{{if $dialog}}
  Main.add(function(){
    var urlComponents = Url.parse();
    $('{{$singleType}}' || 'interv').show();
  });
{{/if}}

chooseProtocole = function(protocole_id) {
  {{if $dialog}}
  if(protocole_id == 0) {
    var url =  new Url('planningOp', 'vw_edit_protocole');
    url.addParam('protocole_id', protocole_id);
    var protocoleModal = url.requestModal(800);
  } else {
    setClose(protocole_id);
  }
  {{else}}
  var url =  new Url('planningOp', 'vw_edit_protocole');
  url.addParam('protocole_id', protocole_id);
  url.requestModal(800);
  {{/if}}
}

setClose = function(protocole_id) {
  if (aProtocoles[protocole_id]) {
    ProtocoleSelector.set(aProtocoles[protocole_id]);
    Control.Modal.close();
  }
  else {
    var url = new Url('planningOp', 'ajax_get_protocole');
    url.addParam('protocole_id', protocole_id);
    url.addParam('chir_id'     , $V(getForm('selectFrm').chir_id));
    url.requestUpdate("get_protocole");
  }
}

refreshList = function(form, types, reset) {
  types = types || ['interv', 'sejour'];
  if (reset) {
    types.each(function(type) {
      $V(form.elements['page['+type+']'], 0, false);
    });
  }
  
  var url = new Url('planningOp','httpreq_vw_list_protocoles');
  url.addParam('chir_id'    , $V(form.chir_id));
  url.addParam('dialog'     , $V(form.dialog));
  url.addParam('function_id', $V(form.function_id));
  url.addParam('sejour_type', '{{$sejour_type}}');
  
  types.each(function(type){
  url.addParam('page['+type+']', $V(form['page['+type+']']));
    url.addParam('type', type);
    url.requestUpdate(type);
  });
}

changePagesejour = function (page) {
    $V(getForm('selectFrm').elements['page[sejour]'], page);
}

changePageinterv = function (page) {
  $V(getForm('selectFrm').elements['page[interv]'], page);
}

reloadPage = function(form) {
  $V(form['page[interv]'], 0, false);
  $V(form['page[sejour]'], 0, false);
  form.submit();
}

addBesoins = function(types_ressources_ids) {
  var form = getForm("addBesoinProtocole");
  types_ressources_ids = types_ressources_ids.split(",");
  
  types_ressources_ids.each(function(type_ressource_id) {
    $V(form.type_ressource_id, type_ressource_id);
    onSubmitFormAjax(form);
  });
}

Main.add(function(){
  var oForm = getForm('selectFrm');
  var urlComponents = Url.parse();
  
  refreshList(oForm);
  
  var url = new Url('planningOp', 'ajax_protocoles_autocomplete');
  url.addParam('field'            , 'protocole_id');
  url.addParam('input_field'      , 'search_protocole');
  url.addParam('chir_id'          , $V(oForm.chir_id));
  if ('{{$singleType}}' == 'interv') {
    url.addParam('for_sejour', '0');
  } 
  if ('{{$singleType}}' == 'sejour') {
    url.addParam('for_sejour', '1');
  }
  url.autoComplete(oForm.elements.search_protocole, null, {
    minChars: 3,
    method: 'get',
    select: 'view',
    dropdown: true,
    afterUpdateElement: function(field, selected){
      chooseProtocole(selected.id.split('-')[2]);
      $V(field, "");
    }
  });
});
</script>

<form name="exportProtocoles" method="get" target="_blank">
  <input type="hidden" name="m" value="planningOp" />
  <input type="hidden" name="a" value="ajax_export_protocoles_dhe" />
  <input type="hidden" name="dialog" value="1" />
  <input type="hidden" name="suppressHeaders" value="1" />
  <input type="hidden" name="chir_id" />
  <input type="hidden" name="function_id" />
</form>

<div id="get_protocole" style="display: none;"></div>

<table class="main" style="background-color: #fff">
  <tr>
    <td colspan="2" style="text-align: left;">
      <button type="button" class="new" onclick="chooseProtocole(0);">{{tr}}CProtocole-title-create{{/tr}}</button>
      <form name="selectFrm" action="?" method="get" onsubmit="return false">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dialog" value="{{$dialog}}" />
        <input type="hidden" {{if $dialog}} name="a" {{else}} name="tab" {{/if}} value="vw_protocoles" />
        <input type="hidden" name="page[interv]" value="{{$page.interv}}" onchange="refreshList(this.form, ['interv'])" />
        <input type="hidden" name="page[sejour]" value="{{$page.sejour}}" onchange="refreshList(this.form, ['sejour'])" />
        
        <table class="form">
          <tr>
            <th><label for="chir_id" title="Filtrer les protocoles d'un praticien">Praticien</label></th>
            <td>
              <select name="chir_id" style="width: 20em;" onchange="if (this.form.function_id) {this.form.function_id.selectedIndex=0;} refreshList(this.form);">
                <option value="0">&mdash; {{tr}}Choose{{/tr}}</option>
                {{foreach from=$listPrat item=curr_prat}}
                <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}}; {{if !$curr_prat->_count_protocoles}}color: #999;{{/if}}"
                        value="{{$curr_prat->user_id}}" {{if ($chir_id == $curr_prat->user_id) && !$function_id}} selected="selected" {{/if}}>
                  {{$curr_prat->_view}} ({{$curr_prat->_count_protocoles}})
                </option>
                {{/foreach}}
              </select>
            </td>
            <th><label for="function_id" title="Filtrer les protocoles d'une fonction">Fonction</label></th>
            <td>
              {{if $can->admin}}
              <select name="function_id" style="width: 30em;" onchange="if (this.form.chir_id) { this.form.chir_id.selectedIndex=0; } refreshList(this.form);">
                <option value="0">&mdash; {{tr}}Choose{{/tr}}</option>
                {{foreach from=$listFunc item=curr_function}}
                <option class="mediuser" style="border-color: #{{$curr_function->color}}; {{if !$curr_function->_count_protocoles}}color: #999;{{/if}}"
                        value="{{$curr_function->_id}}" {{if $curr_function->_id == $function_id}}selected="selected"{{/if}}>
                  {{$curr_function->_view}} ({{$curr_function->_count_protocoles}})
                </option>
                {{/foreach}}
              </select>
              {{/if}}
            </td>
            <th>{{tr}}Search{{/tr}}</th>
            <td>
              <input name="search_protocole" />
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  
  <tr>
    <td>
      {{if !$dialog}}
      <ul id="tabs-protocoles" class="control_tabs">
        <li><a href="#interv">Chirurgicaux <small>(0)</small></a></li>
        <li><a href="#sejour">Médicaux <small>(0)</small></a></li>
        {{if !$dialog}}
          <li>
            <button type="button" onclick="popupImport();" class="hslip">{{tr}}Import-CSV{{/tr}}</button>
            <button type="button" onclick="popupExport();" class="hslip">{{tr}}Export-CSV{{/tr}}</button>
          </li>
        {{/if}}
      </ul>
      
      <script type="text/javascript">
      Main.add(function(){
        // Don't use .create() because the #fragment of the url 
        // is not taken into account, and this is important here
        new Control.Tabs('tabs-protocoles');
      });
      </script>
      
      <hr class="control_tabs" />
      {{/if}}
      
      <div style="display: none;" id="interv"></div>
      <div style="display: none;" id="sejour"></div>
    </td> 
  </tr>
</table>