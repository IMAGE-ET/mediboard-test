<script type="text/javascript">
Main.add(function () {
  if(document.selCabinet){
    Calendar.regField(getForm("selCabinet").date, null, {noView: true});
  }
  
  // Mise à jour du compteur de patients arrivés
	if($('tab_main_courante')){
		link = $('tab_main_courante').select("a[href=#consultations]")[0];
	  link.update('Reconvocations ('+{{$nb_attente}}+')');
	  {{if $nb_attente == '0'}}
	    link.addClassName('empty');
	  {{else}}
	    link.removeClassName('empty');
	  {{/if}}
  }
});
</script>

<table class="main">
  {{if !$mode_urgence}}
  <tr>
    <td>
      <form name="selCabinet" action="?" method="get">
	    <input type="hidden" name="m" value="{{$m}}" />
	    <input type="hidden" name="tab" value="{{$tab}}" />
      <table class="form">
        <tr>
          <th class="title" colspan="100">
          	Journée de consultation du
            {{$date|date_format:$dPconfig.longdate}}
            <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
          </th>
        </tr>
        <tr>
          <th>
            <label for="cabinet_id" title="Sélectionner un cabinet">Cabinet</label>
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
		          <option value="0"{{if $closed == "0"}}selected="selected"{{/if}}>Masquer les Terminées</option>
		        </select>
		      </td>
          <td>
          </td>
        </tr>
      </table> 
      </form>
    </td>
  </tr>
 {{/if}}
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
       
       {{mb_ternary var=current_m test=$mode_urgence value=dPurgences other=dPcabinet}}
       
       {{mb_include module=dPcabinet template=inc_list_consult}}
     </td>
     {{foreachelse}}
     <td>
       <em>{{tr}}CPlageconsult.none{{/tr}}
     </td>
     {{/foreach}}
   </tr>
   </table>
   </td>
  </tr>
 </table>