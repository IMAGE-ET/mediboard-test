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
  var oForm = document.selView;
  var url = new Url;
  url.setModuleAction("dPplanningOp", "code_selector");
  url.addElement(oForm.type);
  url.addElement(oForm.chir);
  url.addElement(oForm.anesth);
  url.addElement(oForm.object_class);  
  url.addParam("mode" , $V(oForm.mode));
  url.addParam("order", $V(oForm.order));
  url.addParam("dialog", 1);
  url.redirect();
}

Main.add(function () {
  new Control.Tabs('tabs-code');
});

</script>

<!-- Filtre principal -->
<form name="selView" action="?">
<input type="hidden" name="type" value="{{$type}}" />
<input type="hidden" name="chir" value="{{$chir}}" />
<input type="hidden" name="anesth" value="{{$anesth}}" />
<input type="hidden" name="object_class" value="{{$object_class}}" />

<table class="form">
  <tr>
    <th>Mode</th>
    <td>
      <input name="mode" value="favoris" type="radio" {{if $mode == "favoris"}}checked="checked"{{/if}} onchange="viewCode();" />
			<label for="mode_favoris">Favoris</label> 
      <input name="mode" value="stats"   type="radio" {{if $mode == "stats"  }}checked="checked"{{/if}} onchange="viewCode();" />
			<label for="mode_stats">Statistiques</label> 
    </td>
      
  	<th>Tri</th>
    <td>
      {{if $mode == "favoris"}}
      Par ordre alphabétique
      {{else}}
      <input name="order" value="alpha" type="radio" {{if $order == "alpha"}}checked="checked"{{/if}} onchange="viewCode();" />
			<label for="order_alpha">Par ordre alphabetique</label> 
      <input name="order" value="taux"  type="radio" {{if $order == "taux"  }}checked="checked"{{/if}} onchange="viewCode();" />
			<label for="order_taux">Par utilisation</label>
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
<ul id="tabs-code" class="control_tabs">
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
<tbody id="{{$profile}}" style="display: none;">
{{if $type=="ccam"}} 
  {{include file=inc_ccam_selector.tpl fusion=$list}}
{{/if}}

{{if $type=="cim10"}}
  {{include file=inc_cim_selector.tpl fusion=$list}}
{{/if}}

</tbody>
{{/foreach}}

</table>

