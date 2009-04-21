<!-- $Id$ -->

<h2>Bilan des cabinets</h2>

<table class="tbl">
  <tr>
    <th>Cabinet</th>
    <th>Total praticiens</th>
    <th>Praticiens en activité<br />(derniers 60 jours)</th>
  </tr>
  
  {{foreach from=$functions item=function}}
  <tr>
    <td>
    	<div class="mediuser" style="border-color: #{{$function->color}};">
      	{{mb_value object=$function field=_view}}
      </div>
    </td>
    <td style="text-align: center">{{$function->_count_total}}</td>
    <td style="text-align: center">{{$function->_count_active}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10"><em>Aucune FSE facturée</em></td>
  </tr>
	{{/foreach}}
</table>


<h2>Bilan des praticiens</h2>

<table class="tbl">
  <tr>
    <th class="title" colspan="7">Information LogicMax</th>
    <th class="title" colspan="6">Liaison Mediboard</th>
  </tr>

  <tr>
    <th>CPS</th>
    <th>Nom du PS</th>
    <th>Adeli du PS</th>
    <th>Début</th>
    <th>Fin</th>
    <th>Durée</th>
    <th>Nb. FSE</th>

    <th>{{mb_title class=CMediusers field=_user_last_name}}</th>
    <th>{{mb_title class=CMediusers field=adeli}}</th>
    <th>{{mb_title class=CMediusers field=function_id}}</th>
  </tr>
  
  {{foreach from=$cpss item=cps}}
  <tr>
    <td>{{$cps.numero}}</td>
    <td>{{$cps.nom}} {{$cps.prenom}}</td>
    <td>{{$cps.adeli}}</td>
    <td><label title="{{$cps.date_min|date_format:$dPconfig.date}}">{{$cps.date_min|date_format:'%B %Y'}}</label></td>
    <td><label title="{{$cps.date_max|date_format:$dPconfig.date}}">{{$cps.date_max|date_format:'%B %Y'}}</label></td>
    <td style="text-align: center">{{$cps.jours}} {{tr}}days{{/tr}}</td>
    <td style="text-align: center">{{$cps.nb_fse}}</td>
		
		{{if $cps.prat}}
	    <td>{{mb_value object=$cps.prat field=_view}}</td>
	    <td>{{mb_value object=$cps.prat field=adeli}}</td>

	    {{assign var=function value=$cps.prat->_ref_function}}
	    <td>
	    	<div class="mediuser" style="border-color: #{{$function->color}};">
	      	{{mb_value object=$function field=_view}}
	      </div>
	    </td>
		{{/if}}		
    
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10"><em>Aucune FSE facturée</em></td>
  </tr>
	{{/foreach}}
</table>
      