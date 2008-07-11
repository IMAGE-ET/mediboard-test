<table class="form">
  <tr>
    <th style="width:10%">
      <b>Incident</b>
    </th>
    <td>
	    <!-- 
	      <form name="fsei" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
	      <input type="hidden" name="m" value="dPqualite" />
	      <input type="hidden" name="dosql" value="do_ficheEi_aed" />
	      <input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
	      <input type="hidden" name="user_id" value="{{$app->user_id}}" />
	      <input type="hidden" name="type_incident" value="inc" />
	      <input type="hidden" name="elem_concerne" value="pat" />
	      <input type="hidden" name="date_fiche" value="{{$date}}" />
	      <input type="hidden" name="date_incident" value="{{$blood_salvage->recuperation_start}}" />
	      -->
	      <select name="elem_concerne_detail" style="width:150px">
	        <option value="null">&mdash; Aucun incident</option>
	        {{foreach from=$liste_incident item=incident}}
	        <option value="{{$incident}}">{{$incident}}</option>
	        {{/foreach}}
	      </select>
	    <!-- </form> -->
    </td>
    <th style="width:10%">
      <b>Protocole qualité</b>
    </th>
    <td>
      <select name="protocole-qualite">
        <option>&mdash; Protocole</option>
        <option>Non prélevé</option>
        <option>Prélevé et transmis</option>
      </select>
    </td>
  </tr>
</table>