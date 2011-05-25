<!-- $Id$ -->

<script type="text/javascript">
{{if $dialog}}
  var aProtocoles = {
    sejour: {},
    interv: {}
  };
  
  Main.add(function(){
    var urlComponents = Url.parse();
    $(urlComponents.fragment || 'interv').show();
    getForm("selectFrm").action = "#"+urlComponents.fragment;
  });
{{/if}}

chooseProtocole = function(protocole_id) {
  {{if $dialog}}
  setClose(protocole_id);
  {{else}}
  var url =  new Url();
  url.setModuleTab("dPplanningOp", "vw_edit_protocole");
  url.addParam("protocole_id", protocole_id);
  url.redirect();
  {{/if}}
}

setClose = function(protocole_id) {
  window.parent.ProtocoleSelector.set(aProtocoles[protocole_id]);
  window.close();
}

refreshList = function(form, types, reset) {
  types = types || ["interv", "sejour"];
  
  if (reset) {
    types.each(function(type) {
      $V(form.elements["page["+type+"]"], 0, false);
    });
  }
  
  var url = new Url("dPplanningOp","httpreq_vw_list_protocoles");
  url.addParam("page[interv]", $V(form["page[interv]"]));
  url.addParam("page[sejour]", $V(form["page[sejour]"]));
  url.addParam("chir_id", $V(form.chir_id));
  url.addParam("dialog", $V(form.dialog));
  
  types.each(function(type){
    url.addParam("type", type);
    url.requestUpdate(type);
  });
}

var changePage = {
  sejour: function (page) {
    $V(getForm("selectFrm").elements['page[sejour]'], page);
  },
  interv: function (page) {
    $V(getForm("selectFrm").elements['page[interv]'], page);
  }
};

reloadPage = function(form) {
  $V(form["page[interv]"], 0, false);
  $V(form["page[sejour]"], 0, false);
  form.submit();
}

Main.add(function(){
  var oForm = getForm("selectFrm");
  var urlComponents = Url.parse();
  
  refreshList(oForm);
  
  var url = new Url("system"      , "ajax_seek_autocomplete");
  url.addParam("object_class"     , "CProtocole");
  url.addParam("field"            , "protocole_id");
  url.addParam("input_field"      , "search_protocole");
  url.addParam("where[chir_id]"   , $V(oForm.chir_id));
  if(urlComponents.fragment == 'interv') {
    url.addParam("where[for_sejour]", '0');
  } else if(urlComponents.fragment == 'sejour') {
    url.addParam("where[for_sejour]", '1');
  }
  url.autoComplete(oForm.elements.search_protocole, null, {
    minChars: 3,
    method: "get",
    select: "view",
    dropdown: true,
    afterUpdateElement: function(field,selected){
        chooseProtocole(selected.id.split("-")[2]);
    }
  });
});
</script>
<table class="main">
  <tr>
    <td colspan="2">
      <a class="button new" href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}=vw_edit_protocole&amp;protocole_id=0">Créer un nouveau protocole</a>
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
              <select name="chir_id" onchange="reloadPage(this.form)">
                <option value="" >&mdash; Tous les praticiens</option>
                {{foreach from=$listPrat item=curr_prat}}
                <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}}; {{if !$curr_prat->_ref_protocoles|@count}}color: #999;{{/if}}"
                        value="{{$curr_prat->user_id}}" {{if $chir_id == $curr_prat->user_id}} selected="selected" {{/if}}>
                  {{$curr_prat->_view}} ({{$curr_prat->_ref_protocoles|@count}})
                </option>
                {{/foreach}}
              </select>
            </td>
            <th>Recherche</th>
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