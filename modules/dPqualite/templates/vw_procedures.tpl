<script type="text/javascript">
function refreshProceduresList() {
  var form = getForm("filter-procedures");
  var url = new Url;
  url.setModuleAction("dPqualite", "httpreq_vw_list_procedures");
  $(form).getElements().each(function(e) {
    url.addParam(e.name, $V(e));
  });
  url.requestUpdate('list_procedures', { waitingText : null });
  return false;
}

function resetFirst() {
  $V(getForm("filter-procedures").first, '', false);
}

function popFile(objectClass, objectId, elementClass, elementId, sfn){
  var url = new Url;
  url.addParam("nonavig", 1);
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, sfn);
}

function highlightRow(element) {
  $$('#list_procedures .selected').each(function(e) {
    e.removeClassName("selected");
  });
  $(element).up(1).addClassName("selected");
} 

function ZoomAjax(objectClass, objectId, elementClass, elementId, sfn){
  file_preview = elementId;
  var url = new Url;
  url.setModuleAction("dPfiles", "preview_files");
  url.addParam("objectClass", objectClass);
  url.addParam("objectId", objectId);
  url.addParam("elementClass", elementClass);
  url.addParam("elementId", elementId);
  if(sfn && sfn!=0){
    url.addParam("sfn", sfn);
  }
  url.requestUpdate('bigView', { waitingText : "{{tr}}CFile-msg-loadimgmini{{/tr}}" });
}

Main.add(refreshProceduresList);
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      
      <form name="filter-procedures" action="?m={{$m}}" method="get" onsubmit="return refreshProceduresList(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="first" value="" onchange="this.form.onsubmit();" />
        
        <table class="main form">
          <tr>
            <th><label for="theme_id" title="{{tr}}CDocGed-doc_theme_id-desc{{/tr}}">{{tr}}CDocGed-doc_theme_id{{/tr}}</label></th>
            <td>
              <select name="theme_id" onchange="resetFirst(); this.form.onsubmit();">
                <option value="0">&mdash; {{tr}}CThemeDoc.all{{/tr}}</option>
                {{foreach from=$listThemes item=curr_theme}}
                <option value="{{$curr_theme->_id}}" {{if $theme_id == $curr_theme->_id}}selected="selected"{{/if}}>
                  {{$curr_theme->nom}}
                </option>
                {{/foreach}}
              </select>
            </td>
            
            <th><label for="chapitre_id" title="{{tr}}CDocGed-doc_chapitre_id-desc{{/tr}}">{{tr}}CDocGed-doc_chapitre_id{{/tr}}</label></th>
            <td>
              <select name="chapitre_id" onchange="resetFirst(); this.form.onsubmit();">
        				<option value="0">&mdash; {{tr}}CChapitreDoc.all{{/tr}}</option>
                {{include file=inc_options_chapitres.tpl chapitres=$listChapitres chapitre_id=$chapitre_id}}
              </select>
            </td>
          <tr>
            <th><label for="sort_by">Trier par</label>
            <td>
              <select name="sort_by" onchange="resetFirst(); this.form.onsubmit();">
                <option value="date" {{if $sort_by == "date"}}selected="selected"{{/if}}>{{tr}}CDocGedSuivi-date{{/tr}}</option>
                <option value="ref" {{if $sort_by == "ref"}}selected="selected"{{/if}}>{{tr}}CDocGed-_reference_doc{{/tr}}</option>
              </select>
            </td>
            
            <th><label for="keywords">Mots-clés</label></th>
            <td>
              <input type="text" name="keywords" value="" onchange="resetFirst(); this.form.onsubmit();" />
              <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
            </td>
          </tr>
        </table>
        
      </form>
      
      <div id="list_procedures"></div>
    </td>
    <td class="halfPane" id="bigView" style="text-align: center;">
      {{include file="../../dPfiles/templates/inc_preview_file.tpl"}}
    </td>
  </tr>
</table>