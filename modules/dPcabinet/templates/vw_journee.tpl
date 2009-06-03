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
          <th class="title" colspan="100">
          	Journ�e de consultation du
            {{$date|date_format:$dPconfig.longdate}}
            <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
          </th>
        </tr>
        
        <tr>
          <th>
            <label for="cabinet_id" title="S�lectionner un cabinet">Cabinet</label>
          </th>
          <td>
            <select name="cabinet_id" onchange="submit()">
              <option value="">&mdash; Choisir un cabinet</option>
              {{foreach from=$cabinets item=curr_cabinet}}
                <option value="{{$curr_cabinet->_id}}" class="mediuser" style="border-color: #{{$curr_cabinet->color}}" {{if $curr_cabinet->_id == $cabinet_id}} selected="selected" {{/if}}>
                  {{$curr_cabinet->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
          
		      <th>
		      	<label for="closed" title="Type de vue du planning">Type de vue</label></th>
		      <td colspan="5">
		        <select name="closed" onchange="this.form.submit()">
		          <option value="1"{{if $closed == "1"}}selected="selected"{{/if}}>Tout afficher</option>
		          <option value="0"{{if $closed == "0"}}selected="selected"{{/if}}>Masquer les Termin�es</option>
		        </select>
		      </td>
          
          <td>
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
        {{foreach from=$praticiens item=curr_prat}}
          <th class="title">
            {{$curr_prat->_view}}
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
       {{assign var="userSel" value=$curr_day.prat}}
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