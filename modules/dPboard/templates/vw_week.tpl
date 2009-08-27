{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function viewItem(oTd, guid, date) {
  // Mise en surbrillance de la plage survolée
  $$('td.selectedConsult').each(function(elem) { elem.className = "nonEmptyConsult";});
  $$('td.selectedOp').each(function(elem) { elem.className = "nonEmptyOp";});
  
  var parts = guid.split('-'),
      sClassName = parts[0],
      id = parts[1];
      
  if(sClassName == "CPlageconsult"){
    oTd.up().className = "selectedConsult";
  }else if(sClassName == "CPlageOp"){
    oTd.up().className = "selectedOp";
  }
  
  // Affichage de la plage selectionnée et chargement si besoin
  Dom.cleanWhitespace($('viewTooltip'));
  $('viewTooltip').childElements().invoke('hide');

  oElement = $(guid).show();
  
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
    url.addParam("chirSel"        , "{{$prat->_id}}");
    url.addParam("vue2"           , "{{$vue}}");
    url.addParam("selConsult"     , "");
  } else if(sClassName == "CPlageOp"){
    url.setModuleAction("dPplanningOp", "httpreq_vw_list_operations");
    url.addParam("chirSel" , "{{$prat->_id}}");
    url.addParam("date"    , date);
    url.addParam("urgences", "0");
  } else return;

  url.requestUpdate(oElement);
  oElement.alt = "infos - cliquez pour fermer";
}

function updateSemainier() {
  var url = new Url("dPboard", "httpreq_semainier");
  url.addParam("chirSel" , "{{$prat->_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("board"   , "1");
  url.requestUpdate("semainier");
}

Main.add(function () {
  {{if $prat->_id}}
		  updateSemainier();
  {{/if}}
  
  ViewPort.SetAvlHeight("semainier", 1);
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
});

</script>

<!-- Script won't be evaled in Ajax inclusion. Need to force it -->
{{mb_include_script path="includes/javascript/intermax.js"}}

<table class="main">
  <tr>
    <th>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$prec}}">&lt;&lt;&lt;</a>
      <form name="changeDate" action="?m={{$m}}" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        {{$date|date_format:$dPconfig.longdate}}
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$suiv}}">&gt;&gt;&gt;</a>
    </th>
  </tr>

  <tbody class="viewported">

  <tr>
    <td id="semainier" class="viewport" colspan="2"></td>
  </tr>
  
  </tbody>
  
</table>