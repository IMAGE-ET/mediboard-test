<script type="text/javascript">

function setClose(code, type, tarif) {
  if (type == "ccam" ) {
  	var oSelector = window.opener.CCAMSelector;
  }
  
  if (type == "cim10") {
  	var oSelector = window.opener.CIM10Selector;
  }

  oSelector.set(code, tarif);
  window.close();
}

function createFavori() {
  var oForm = document.selView;
  var sType = "{{$type}}";
  var sModule = sType == "ccam" ? "dPccam" : "dPcim10";

  var url = new Url;
  url.setModuleAction(sModule, "vw_find_code");
  if (sModule == "dPccam") {
    url.addParam("object_class", oForm.object_class.value);
  }
  url.addParam("dialog", 1);
  url.redirect();
}


function viewCode() {
  {{if $type == "ccam" }}viewCCAM();{{/if}}
  {{if $type == "cim10"}}viewCim ();{{/if}}
}

function viewCCAM() {
  var oForm = document.selView;
  var url = new Url;
  url.setModuleAction("dPplanningOp", "code_selector");
  url.addParam("type", "ccam"); 
  url.addElement(oForm.order);
  url.addElement(oForm.mode);
  url.addElement(oForm.chir);
  url.addElement(oForm.anesth);
  url.addElement(oForm.object_class);  
  url.addParam("dialog", 1);
  url.redirect();
}

function viewCim(){
  var oForm = document.selViewCim;
  var url = new Url;
  url.setModuleAction("dPplanningOp", "code_selector");
  url.addParam("type", "cim10"); 
  url.addElement(oForm.order);
  url.addElement(oForm.mode);
  url.addElement(oForm.chir);  
  url.addParam("dialog", 1);
  url.redirect();
}

function pageMain() {
  new Control.Tabs('main_tab_group');
}

</script>

<!-- Filtre principal -->
<form name="selView" action="?">
<input type="hidden" name="chir" value="{{$chir}}" />
<input type="hidden" name="anesth" value="{{$anesth}}" />
<input type="hidden" name="object_class" value="{{$object_class}}" />

<table class="form">
  <tr>
    <th>Mode</th>
    <td>
      <select name="mode" onchange="viewCode();">
  	    <option>&mdash; Choisir un mode</option>
  	    <option value="favoris" {{if $mode == "favoris"}} selected="selected" {{/if}}>Favoris</option>
  	    <option value="stats"   {{if $mode == "stats"  }} selected="selected" {{/if}}>Statistiques</option>
  	  </select>
    </td>
      
  	<th>Tri</th>
    <td>
      {{if $mode == "favoris"}}
      Par ordre alphabétique
      {{else}}
      <select name="order" onchange="viewCode();">
  	    <option>&mdash; Choisir un tri</option>
  	    <option value="alpha" {{if $order == "alpha"}} selected="selected" {{/if}}>Par ordre alphabetique</option>
  	    <option value="taux"  {{if $order == "taux" }} selected="selected" {{/if}}>Par utilisation</option>
  	  </select>
			{{/if}}
  	</td>
  </tr>

  <tr>
    <td class="button" colspan="4">
      <button class="cancel" type="button" onclick="window.close();">{{tr}}Cancel{{/tr}}</button>
      <button class="search" type="button" onclick="createFavori();">{{tr}}button-CCodeCCAM-searchAnother{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

<!-- Favoris par utilisateur -->
<ul id="main_tab_group" class="control_tabs">
{{foreach from=$listByProfile key=profile item=list}}
  {{assign var=user value=$users.$profile}}
  <li>
    <a href="#{{$profile}}">
    	{{tr}}Profile.{{$profile}}{{/tr}} 
    	{{$user->_view}}
    </a>
  </li>
{{/foreach}}
</ul>
<hr class="control_tabs" />

<table class="selectCode">

{{foreach from=$listByProfile key=profile item=list}}
<tbody id="{{$profile}}">
{{if $type=="ccam"}} 
  {{include file=inc_ccam_selector.tpl fusion=$list}}
{{/if}}

{{if $type=="cim10"}}
  {{include file=inc_cim_selector.tpl fusion=$list}}
{{/if}}

</tbody>
{{/foreach}}


</table>

