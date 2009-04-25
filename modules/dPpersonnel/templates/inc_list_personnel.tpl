<a href="?m={{$m}}&amp;tab={{$tab}}&amp;personnel_id=0" class="buttonnew">
	{{tr}}CPersonnel-title-create{{/tr}}
</a>

<!-- Filtre -->
<table class="form">
  <tr>
    <td>
      <form name="filterFrm" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="dialog" value="{{$dialog}}" />
        <table class="form">
          <tr>
            <th colspan="4" class="title">Recherche d'un membre du personnel</th>
          </tr>
          <tr>
            <th>{{mb_label object=$filter field="_user_last_name"}}</th>
            <td>{{mb_field object=$filter field="_user_last_name"}}</td>
            <th>{{mb_label object=$filter field="_user_first_name"}}</th>
            <td>{{mb_field object=$filter field="_user_first_name"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$filter field="emplacement"}}</th>
            <td>{{mb_field object=$filter defaultOption="&mdash; Tous" canNull=true field="emplacement"}}</td>
          </tr>
          <tr>
            <td class="button" colspan="6">
              <button class="search" type="submit">{{tr}}Show{{/tr}}</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr> 
</table> 

<!-- Liste de personnel -->
<table class="tbl">
  <tr>
    <th colspan="3">Liste du personnel</th>
  </tr>

	<tr>
	  <th>{{mb_title class=CPersonnel field=user_id}}</th>
	  <th>{{mb_title class=CPersonnel field=emplacement}}</th>
	  <th>{{tr}}CPersonnel-back-affectations{{/tr}}</th>
	</tr>

	{{foreach from=$personnels item=_personnel}}
	<tr {{if $_personnel->_id == $personnel->_id}}class="selected"{{/if}}>
	  <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;personnel_id={{$_personnel->_id}}">{{$_personnel->_ref_user->_view}}</a></td>
		
	  <td>{{tr}}CPersonnel.emplacement.{{$_personnel->emplacement}}{{/tr}}</td>
		{{if $_personnel->actif}}
	  <td style="text-align: center;">{{$_personnel->_count.affectations}}</td>
		{{else}}
	  <td class="cancelled">INACTIF</td>
		{{/if}}
	</tr>
	{{/foreach}}

</table>
	  