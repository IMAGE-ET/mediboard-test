<script type="text/javascript">
function popSearch() {
  var f = document.FrmClass;
  var url = new Url;
  url.setModuleAction("system", "object_selector");
  url.addParam("keywords", f.keywords.value);
  url.addParam("selClass", f.selClass.value);  
  url.popup(600, 300, "-");
}

function setData(key, val){
  var f = document.FrmClass;
  if (val != '') {
    f.selKey.value = key;
    f.selView.value = val;
    f.submit();
  }
}
</script>

<table class="main">
  <tr>
    <td  class="HalfPane">
      <table class="form">
        <tr>
          <td>
            <form name="FrmClass" action="?m={{$m}}" method="get">
            <input type="hidden" name="m" value="{{$m}}" />          
            <label for="selClass" title="Veuillez Sélectionner une Class">Class</label>
            <select name="selClass" onchange="submit()">
            {{foreach from=$listClass item=curr_listClass}}
              <option value="{{$curr_listClass}}"{{if $selClass==$curr_listClass}} selected="selected"{{/if}}>{{$curr_listClass}}</option>
            {{/foreach}}
            </select>
            
            {{if $selClass}}
            <input type="text" name="keywords" value="{{$keywords}}" />
            <button type="button" onclick="popSearch()" class="search">Rechercher</button>
            {{/if}}
            <input type="hidden" name="selKey" value="{{$selKey}}" />
            <input type="hidden" name="selView" value="{{$selView}}" />  
            </form>
          </td>
        </tr>
        {{if $selClass && $selKey}}
        <tr>
          <td>
            <div class="accordionMain" id="accordionConsult">
            {{foreach from=$listCategory item=curr_listCat}}
              <div id="{{$curr_listCat->nom}}">
                <div id="{{$curr_listCat->nom}}Header" class="accordionTabTitleBar">
                  {{$curr_listCat->nom}}
                </div>
                <div id="{{$curr_listCat->nom}}Content"  class="accordionTabContentBox">
                  <div id="mainConsult">
                  
                  </div>
                </div>
              </div>
            {{/foreach}}            
            </div>
            <script language="Javascript" type="text/javascript">
            new Rico.Accordion( $('accordionConsult'), {panelHeight:100} );
            </script>
          </td>
        </tr>
        {{/if}}
      </table>
    </td>    
    <td class="greedyPane">
    
    </td>
  </tr>
</table>