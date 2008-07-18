<form name="addAdministration" method="post" action="?">
  <input type="hidden" name="dosql" value="do_administration_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="administration_id" value="" />
  <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
  <input type="hidden" name="object_id" value="{{$line->_id}}" />
  <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
  <input type="hidden" name="unite_prise" value="{{$unite_prise}}" />
  <input type="hidden" name="dateTime" value="{{$dateTime}}" />
  <input type="hidden" name="prise_id" value="{{$prise_id}}" />
	<table class="form">
	  <tr>
	    <th class="title" colspan="2">Administration de {{$line->_view}}</th>
	  </tr>
	  <tr>
	    <td>
	      {{mb_label object=$prise field=quantite}}
	      {{mb_field object=$prise field=quantite min=1 increment=1 form=addAdministration}}
	      
	      {{if $line->_class_name == "CPrescriptionLineMedicament"}}
	        {{$unite_prise}}
	      {{else}}
	        {{$line->_unite_prise}}
	      {{/if}} 
	    </td>
	    <td>
	      <button type="button" class="submit" onclick="submitFormAjax(this.form, 'systemMsg', { 
	      		onComplete: function() { window.opener.loadTraitement('{{$sejour_id}}','{{$date}}'); window.close(); } });"
	      		>Administrer</button>
	    </td>
	  </tr>
	</table>
</form>