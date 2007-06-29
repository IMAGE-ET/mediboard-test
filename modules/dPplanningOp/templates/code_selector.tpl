<script type="text/javascript">

function setClose(code, type) {
  if(type=="ccam"){
  var oSelector = window.opener.CCAMSelector;
  }
  if(type=="cim10"){
  var oSelector = window.opener.CIM10Selector;
  }
  
  oSelector.set(code, type);
  window.close();
}

function createFavori() {
  var sType = "{{$type}}";
  var sModule = sType == "ccam" ? "dPccam" : "dPcim10";

  var url = new Url;
  url.setModuleAction(sModule, "vw_find_code");
  url.addParam("dialog", 1);
  url.redirect();
}


function view_() {
  var oForm = document.selView;
  var url = new Url;
  url.setModuleAction("dPplanningOp", "code_selector");
  url.addParam("type", "ccam"); 
  url.addElement(oForm.view);
  url.addElement(oForm.chir);
  url.addElement(oForm.object_class);  
  url.addParam("dialog", 1);
  url.redirect();
}




</script>


<table class="selectCode">
{{if $type=="ccam"}}  
  <tr>
  	<th>Favoris disponibles</th>
    <td>
      <form name="selView">
      <input type="hidden" name="chir" value="{{$chir}}">
  	  <input type="hidden" name="object_class" value="{{$object_class}}">
      <select name="view" onchange="view_();">
  	    <option>&mdash; Choisir un mode d'affichage</option>
  	    <option value="alpha" {{if $view == "alpha"}} selected="selected" {{/if}}>Par ordre alphabetique</option>
  	    <option value="taux" {{if $view == "taux"}} selected="selected" {{/if}}>Par utilisation</option>
  	  </select>
  	  </form>
  	</td>
  </tr>
  <tr>
  {{foreach from=$fusion item=curr_code key=curr_key name=fusion}}
    <td>
      <strong><span style="float:left">{{$curr_code.codeccam->code}}</span>
      {{if $curr_code.codeccam->occ==0}}
      <span style="float:right">Favoris</span>
      {{else}}
      <span style="float:right">{{$curr_code.codeccam->occ}} acte(s)</span>
      {{/if}}
      </strong>
      <br />
      {{$curr_code.codeccam->libelleLong}}
      <br />
      <button class="tick" type="button" onclick="setClose('{{$curr_code.codeccam->code}}', '{{$type}}')">
        {{tr}}Select{{/tr}}
      </button>
    </td>  
  {{if $smarty.foreach.fusion.index % 3 == 2}}
  </tr><tr>
  {{/if}}
  {{/foreach}}
  </tr>
</table>
{{/if}}

{{if $type=="cim10"}}
  <tr>
  	<th>Favoris disponibles</th>
  </tr>
  
  {{if !$list}}
  <tr>
  	<td>{{tr}}CFavoriCCAM.none{{/tr}}</td>
  </tr>
  {{/if}}
  <tr>
  {{foreach from=$list item=curr_code key=curr_key}}
    <td>
      <strong>{{$curr_code->code}}</strong>
      <br />
      {{$curr_code->libelleLong}}
      <br />
      <button class="tick" type="button" onclick="setClose('{{$curr_code->code}}', '{{$type}}')">
        {{tr}}Select{{/tr}}
      </button>
    </td>
  {{if ($curr_key+1) is div by 3}}
  </tr><tr>
  {{/if}}
  {{/foreach}}
  </tr>
</table>

{{/if}}

<table class="form">
  <tr>
    <td class="button" colspan="3">
      <button class="cancel" type="button" onclick="window.close();">{{tr}}Cancel{{/tr}}</button>
      <button class="search" type="button" onclick="createFavori();">{{tr}}button-CCodeCCAM-searchAnother{{/tr}}</button>
    </td>
  </tr>
</table>