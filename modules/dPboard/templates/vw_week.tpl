{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=cabinet script=edit_consultation}}
{{mb_script module=planningOp script=operation}}

<script type="text/javascript">
Consultation.useModal();

function viewItem(guid, id, date, oTd) {
  oTd = $(oTd);
  
  oTd.up("table").select(".event").invoke("removeClassName", "selected");
  oTd.up(".event").addClassName("selected");
   
  // Affichage de la partie droite correspondante
  var sClass = guid.split("-")[0];
  
  viewList(date, id, sClass);  
}

function viewList(date, id, sClass) {
  var url = new Url();
  url.addParam("board"     , "1");
  url.addParam("boardItem" , "1");
  
  url.addParam("date"    , date);
  
  switch (sClass) {
    case "CPlageconsult":
      url.setModuleAction("dPcabinet", "httpreq_vw_list_consult");
      url.addParam("chirSel" , "{{$chirSel}}");
      url.addParam("plageconsult_id", id);
      url.addParam("selConsult"     , "");
      break;
    case "CPlageOp":
      url.setModuleAction("dPplanningOp", "httpreq_vw_list_operations");
      url.addParam("pratSel" , "{{$chirSel}}");
      url.addParam("urgences", "0");
      break;
    default:
      return;
  }
  url.requestUpdate('viewTooltip');
}

updateListOperations = function() {
  var url = new Url("dPplanningOp", "httpreq_vw_list_operations");
  url.addParam("pratSel" , "{{$chirSel}}");
  url.addParam("urgences", "0");
  url.addParam("board"   , "1");
  url.requestUpdate("viewTooltip");
}

Main.add(function () {
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
});
</script>

{{mb_script module=ssr script=planning}}
<table class="main">
  <tr>
    <th>
      <div style="width:120px; float: right;">
        <table id="weeklyPlanning" class="tbl">
          <tr>
            <td style="background-color:#BFB;">&nbsp;&nbsp;</td>
            <td>Plage de consultation</td>
          </tr>
          <tr>
            <td style="background-color:#BCE;">&nbsp;&nbsp;</td>
            <td>Plage opératoire</td>
          </tr>
        </table>
      </div>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$prec}}" >&lt;&lt;&lt;</a>
      Semaine du {{$debut|date_format:"%A %d %b %Y"}} au {{$fin|date_format:"%A %d %b %Y"}}
      <form name="changeDate" action="?m={{$m}}" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="date" class="date" value="{{$debut}}" onchange="this.form.submit()" />
      </form>
      <a  href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$suiv}}">&gt;&gt;&gt;</a>
      <br />
      <a  href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$today}}">Aujourd'hui</a>
    </th>
  </tr>

  <tr >
    <td  id="semainiertdb" style="height:400px;">{{mb_include module=system template=calendars/vw_week}}</td>
    <td id="viewTooltip" style="min-width:300px;width:33%;"></td>
  </tr>
  
   <script>
     Main.add(function() {
       window["planning-{{$planning->guid}}"].onMenuClick = function(guid, id, oTd){
       
        if(oTd.title != "operation" && oTd.title != "consultation"){
          viewItem(guid, id, oTd.title, oTd);
        }
        else{
          var url;
          
          if(oTd.title == "operation"){
            url = new Url("dPplanningOp", "vw_idx_planning", "tab");
          }
          else if(oTd.title == "consultation"){
            url = new Url("dPcabinet", "edit_consultation", "tab");
          }
          url.addParam("date" , guid);
          url.addParam("pratSel" , "{{$chirSel}}");
          url.redirectOpener();
        }
       }
     });
   </script>
   
</table>