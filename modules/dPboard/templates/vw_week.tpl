{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function viewItem(guid, id, date, oTd) {
  oTd = $(oTd);
  
  oTd.up("table").select(".event").invoke("removeClassName", "selected");
  oTd.up(".event").addClassName("selected");
   
  //Affichage de la partie droite correspondante
  var url = new Url;
  url.addParam("board"     , "1");
  url.addParam("boardItem" , "1");
  url.addParam("pratSel" , "{{$chirSel}}");
  url.addParam("date"    , date);
  
  if(guid[6] == "c"){
    url.setModuleAction("dPcabinet", "httpreq_vw_list_consult");
    url.addParam("plageconsult_id", id);
    url.addParam("vue2"           , "{{$vue}}");
    url.addParam("selConsult"     , "");
  } 
  else if(guid[6] == "O"){
    url.setModuleAction("dPplanningOp", "httpreq_vw_list_operations");
    url.addParam("urgences", "0");
  } else return;

  url.requestUpdate('viewTooltip');
}
</script>

<!-- Script won't be evaled in Ajax inclusion. Need to force it -->
{{mb_script script=intermax}}

{{mb_script module=ssr script=planning}}
<table class="main">
  <tr>
    <th>
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="plageconsult_id" value="0" />
        
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$prec}}')">&lt;&lt;&lt;</a>
        
        Semaine du {{$debut|date_format:"%A %d %b %Y"}} au {{$fin|date_format:"%A %d %b %Y"}}
        <input type="hidden" name="debut" class="date" value="{{$debut}}" onchange="this.form.submit()" />
        
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$suiv}}')">&gt;&gt;&gt;</a>
        <br />
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$date}}')">Aujourd'hui</a>
      </form>
    </th>
  </tr>

  <tr >
    <td  id="semainiertdb" style="height:400px;">{{mb_include module=ssr template=inc_vw_week}}</td>
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
<div style="width:100px;">
  Légende:
  <table id="weeklyPlanning" class="tbl">
    <tr>
      <td style="background-color:#9F9;">&nbsp;&nbsp;</td>
      <td>Plage de consultation</td>
    </tr>
    <tr>
      <td style="background-color:#ABE;">&nbsp;&nbsp;</td>
      <td>Plage opératoire</td>
    </tr>
  </table>
</div>