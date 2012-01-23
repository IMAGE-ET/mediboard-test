<script type="text/javascript">
afterAdministration = function() {
  Control.Modal.close();
  {{if $isAnesth}}
    bindOperation('{{$sejour_id}}');
  {{else}}
    onSubmitFormAjax(getForm('addConsultation'));
  {{/if}}
}
</script>
<table class="tbl">
  {{if $prescription_id}}
    {{foreach from=$lines item=_line}}
      <tr>
        <td>
          <strong onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}');">{{$_line->_ref_element_prescription->_view}}</strong>
        </td>
        <td class="narrow">
          {{assign var=line_guid value=$_line->_guid}}
          <form name="addAdministration-{{$_line->_guid}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: afterAdministration});">
            <input type="hidden" name="m" value="dPprescription" />
            <input type="hidden" name="dosql" value="do_administration_aed" />
            {{mb_key object=$administration}}
            <input type="hidden" name="object_class" value="{{$_line->_class}}"/>
            <input type="hidden" name="object_id" value="{{$_line->_id}}" />
						<input type="hidden" name="unite_prise" value="{{$_line->_chapitre}}" />
            {{if $_line->_prise_id}}
              <input type="hidden" name="prise_id" value="{{$_line->_prise_id}}" />
						{{/if}}
						{{mb_field object=$administration field=_date hidden=true}}
            {{mb_field object=$administration field=administrateur_id hidden=true}}
            {{mb_field object=$administration field=quantite hidden=true}}
            {{mb_field object=$administration field=_time form=addAdministration-$line_guid register=true}}
            <button type="button" class="add notext" onclick="this.form.onsubmit()"></button>
          </form>
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td class="empty">
          {{tr}}CPrescriptionLineElement.none{{/tr}}
        </td>
      </tr>
    {{/foreach}}
  {{else}}
    <tr>
      <td class="empty">
        {{tr}}CPrescription.type.sejour.none{{/tr}}
      </td>
    </tr>
  {{/if}}
  <tr>
    <td colspan="2">
      <button type="button" class="hslip" onclick="afterAdministration();">Continuer vers la consultation</button>
    </td>
  </tr>
</table>