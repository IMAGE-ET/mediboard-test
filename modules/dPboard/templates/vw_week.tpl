<script type="text/javascript">
var notWhitespace   = /\S/;

function viewItem(oTd, sClassName, id, date) {

  // Mise en surbrillance de la plage survolée
  
  aListConsult = $$('td.selectedConsult');
  aListConsult.each(function(elem) { elem.className = "nonEmptyConsult";});
  
  aListConsult = $$('td.selectedOp');
  aListConsult.each(function(elem) { elem.className = "nonEmptyOp";});
  
  if(sClassName == "CPlageconsult"){
    oTd.parentNode.className = "selectedConsult";
  }else if(sClassName == "CPlageOp"){
    oTd.parentNode.className = "selectedOp";
  }
  
  // Affichage de la plage selectionnée et chargement si besoin
  
  Dom.cleanWhitespace($('viewTooltip'));
  var oDiv = $('viewTooltip').childNodes;

  $H(oDiv).each(function (pair) {
    if(typeof pair.value == "object"){
      $(pair.value["id"]).hide();
    }
  });

  oElement = $(sClassName+id);
  oElement.show();
  
  if(oElement.alt == "infos - cliquez pour fermer") {
    return;
  }
  
  url = new Url;
  url.addParam("board"     , "1");
  url.addParam("boardItem" , "1");
  
  if(sClassName == "CPlageconsult"){
    url.setModuleAction("dPcabinet", "httpreq_vw_list_consult");
    url.addParam("plageconsult_id", id);
    url.addParam("date"           , date);
    url.addParam("chirSel"        , "{{$pratSel->_id}}");
    url.addParam("vue2"           , "{{$vue}}");
    url.addParam("selConsult"     , "");
  } else if(sClassName == "CPlageOp"){
    url.setModuleAction("dPplanningOp", "httpreq_vw_list_operations");
    url.addParam("chirSel" , "{{$pratSel->_id}}");
    url.addParam("date"    , date);
    url.addParam("urgences", "0");
  } else{
    return;
  }
  url.requestUpdate(oElement);
  oElement.alt = "infos - cliquez pour fermer";
}

function hideItem(sClassName, id) {
  oElement = $(sClassName+id);
  oElement.hide();
}

function updateSemainier() {
  var url = new Url;
  url.setModuleAction("dPboard", "httpreq_semainier");

  url.addParam("chirSel" , "{{$pratSel->_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("board"   , "1");

  url.requestUpdate("semainier");
}

function pageMain() {
  updateSemainier();
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
}

</script>

<!-- Script won't be evaled in Ajax inclusion. Need to force it -->
{{mb_include_script path="includes/javascript/intermax.js"}}

<table class="main">
  {{if $secretaire || $admin}}
  <tr>
    <td>
    <form name="praticien" method="post">
	    <select name="praticien_id" onchange="form.submit()">
	    <option value="">&mdash; Choix d'un praticien</option>
	    {{foreach from=$listPraticiens item="praticien"}}
	      <option value="{{$praticien->_id}}" class="mediuser" style="border-color: #{{$praticien->_ref_function->color}};" {{if $praticien_id == $praticien->_id}}selected = "selected"{{/if}}>{{$praticien->_view}}</option>
	    {{/foreach}}
	    </select>
	  </form>
	  </td>
  </tr>
  {{/if}}
  <tr>
    <th>
      <form name="editFrmPratDate" action="?m={{$m}}" method="get">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$prec}}">&lt;&lt;&lt;</a>
      <input type="hidden" name="m" value="{{$m}}" />
      {{$date|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$suiv}}">&gt;&gt;&gt;</a>
      </form>
    </th>
  </tr>
  <tr>
    <td style="border: 1px dotted #000;" colspan="2">
      <div id="semainier">
      </div>
    </td>
  </tr>
</table>