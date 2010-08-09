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

setClose = function(type, protocole_id) {
  window.opener.ProtocoleSelector.set(aProtocoles[type][protocole_id]);
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
  url.addParam("protocole_id", '{{$protocole->_id}}');
  url.addParam("page[interv]", $V(form["page[interv]"]));
  url.addParam("page[sejour]", $V(form["page[sejour]"]));
  url.addParam("chir_id", $V(form.chir_id));
  url.addParam("dialog", $V(form.dialog));
  url.addParam("code_ccam", $V(form.code_ccam));
  
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
  refreshList(getForm("selectFrm"));
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
            <th><label for="code_ccam" title="Filtrer avec un code CCAM">Code CCAM</label></th>
            <td>
              <select name="code_ccam" onchange="refreshList(this.form)">
                <option value="" >&mdash; Tous les codes</option>
                {{foreach from=$listCodes|smarty:nodefaults key=curr_code item=code_nomber}}
                <option value="{{$curr_code}}" {{if $code_ccam == $curr_code}} selected="selected" {{/if}}>
                  {{$curr_code}} ({{$code_nomber}})
                </option>
                {{/foreach}}
              </select>
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
    
    {{if $protocole->_id && !$dialog}}
      <td class="halfPane">
        {{include file=inc_details_protocole.tpl}}
      </td>
    {{/if}} 
  </tr>
</table>