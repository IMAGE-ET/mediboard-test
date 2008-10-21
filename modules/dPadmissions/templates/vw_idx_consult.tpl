<script type="text/javascript">
Main.add(function () {
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
});
</script>

<table class="main">
  <tr>
    <td>
      <form name="selCabinet" action="?" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <table class="form">
        <tr>
          <th class="title" colspan="3">Sélection d'un cabinet</th>
        </tr>
        <tr>
          <th>
            <label for="cabinet_id" title="Sélectionner un cabinet">Cabinet: </label>
          </th>
          <td>
            <select name="cabinet_id" onchange="submit()">
              <option value="">&mdash; Choisir un cabinet &mdash;</option>
              {{foreach from=$cabinets item=curr_cabinet}}
                <option value="{{$curr_cabinet->_id}}" class="mediuser" style="border-color: #{{$curr_cabinet->color}}" {{if $curr_cabinet->_id == $cabinet_id}} selected="selected" {{/if}}>
                  {{$curr_cabinet->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
          <td>
            {{$date|date_format:"%A %d %B %Y"}}
            <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
          </td>
        </tr>
       </table> 
       </form>
     </td>
   </tr>
   <tr>
     <td>
       <table class="form">
       <tr>
         <th class="title" colspan="4">Affichage du planning</th>
       </tr>
       <tr>
       {{foreach from=$anesthesistes item=curr_anesthesiste}}
       <th class="title">
       {{$curr_anesthesiste->_view}}
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
       {{include file="../../dPcabinet/templates/inc_list_consult.tpl"}}
     </td>
     {{/foreach}}
   </tr>
   </table>
   </td>
  </tr>
 </table>