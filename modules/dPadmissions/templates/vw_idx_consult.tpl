{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  Calendar.regField(getForm("selCabinet").date, null, {noView: true});
});
</script>

<table class="main">
  <tr>
    <td>
      <form name="selCabinet" action="?" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <table class="form">
        <tr>
          <th class="title">
            Consultations d'anesthésie - 
            {{$date|date_format:$dPconfig.longdate}}
            <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
          </th>
        </tr>
       </table> 
       </form>
     </td>
   </tr>
   <tr>
     <td>
       <table class="tbl">
         <tr>
         {{foreach from=$anesthesistes item=curr_anesthesiste}}
         <th class="title">
           Dr {{$curr_anesthesiste->_view}}
         </th>
         {{/foreach}}
       </tr>
   
       <!-- Affichage de la liste des consultations -->    
       <tr>
       {{foreach from=$listPlages item=curr_day}}
         <td style="width: 200px; vertical-align: top;">
         {{assign var="listPlage" value=$curr_day.plages}}
         {{assign var="date" value=$date}}
         {{assign var="hour" value=$hour}}
         {{assign var="boardItem" value=$boardItem}}
         {{assign var="board" value=$board}}
         {{assign var="tab" value=""}}
         {{assign var="vue" value="0"}}
         {{assign var="userSel" value=$curr_day.anesthesiste}}
         {{assign var="consult" value=$consult}}
         {{assign var="current_m" value="dPcabinet"}}
         {{assign var=mode_urgence value=false}}
         {{include file="../../dPcabinet/templates/inc_list_consult.tpl"}}
       </td>
       {{/foreach}}
     </tr>
   </table>
   </td>
  </tr>
 </table>