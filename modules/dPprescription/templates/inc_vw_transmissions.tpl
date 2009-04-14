<div id="transmissions">
 
{{if @$addTrans}}
<script type="text/javascript">

refreshTransmission = function(){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_transmissions");
  url.addParam("sejour_id", '{{$sejour_id}}');
  url.addParam("addTrans", true);
  url.requestUpdate("transmissions", { waitingText: null } );
}

</script>
{{/if}}

<table class="tbl">
  {{if @$addTrans}}
  <tr>
    <th colspan="6" class="title">
    Ajout d'une transmission
    </th>
  </tr>
  <tr>
   <td colspan="6">
      <form name="editTrans" action="?" method="post">
      <input type="hidden" name="dosql" value="do_transmission_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="object_class" value="" />
      <input type="hidden" name="object_id" value="" />
      <input type="hidden" name="sejour_id" value="{{$sejour_id}}" />
      <input type="hidden" name="user_id" value="{{$app->user_id}}" />
      <input type="hidden" name="date" value="now" />
      {{mb_label object=$transmission field="text"}}
      {{mb_field object=$transmission field="degre"}}
      <br />
      {{mb_field object=$transmission field="text"}}
      <br />
      <button type="button" class="add" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: refreshTransmission } );">{{tr}}Add{{/tr}}</button>
      </form>
    </td>
    </tr>
  {{/if}}
  {{if $ajax || $dialog}}
  <tr>
    <th class="title" colspan="6">Transmissions et observations</th>
  </tr>
  {{/if}}
</table>

<table class="tbl">
  <tr>
    <th>Patient</th>
    <th>Type</th>
    <th>Utilisateur</th>
    <th>Date</th>
    <th>Heure</th>
    <th>Texte</th>
  </tr>
  
  {{assign var=date value=""}}
  {{foreach from=$trans_and_obs item=_objects_by_date}}
	  {{foreach from=$_objects_by_date item=_object}}
			{{include file=../../dPhospi/templates/inc_line_suivi.tpl 
				_suivi=$_object
				show_patient=true
				without_del_form=true
			}}
		{{/foreach}}
	{{foreachelse}}
	<tr>
	  <td colspan="6">Aucune transmission</td>
	</tr>
{{/foreach}}
</table>

</div>