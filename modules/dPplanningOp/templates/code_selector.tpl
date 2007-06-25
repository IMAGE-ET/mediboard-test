<script type="text/javascript">

function setClose(code, type) {
  if (type == "ccam") {
    window.opener.setCodeCCAM(code, type);
  }
  if (type == "cim10") {
    window.opener.setCode(code, type);
  }
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


<table class="form">
  <tr>
    <td class="button" colspan="3">
      <button class="cancel" type="button" onclick="window.close();">{{tr}}Cancel{{/tr}}</button>
      <button class="search" type="button" onclick="createFavori();">{{tr}}button-CCodeCCAM-searchAnother{{/tr}}</button>
    </td>
  </tr>
</table>