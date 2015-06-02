<table class="form">
  <tr>
    <th class="title">{{mb_label class=CChapitreMotif field=nom}}</th>
    <th class="title">{{mb_label object=$rpu field="ccmu"}}</th>
  </tr>
  <tr>
    <td style="text-align: center;">
      <strong style="font-size: 2em;">{{mb_value object=$rpu field="code_diag"}}</strong>
      <button type="button" class="search notext" onclick="searchMotif();" style="float: right;">{{tr}}Search{{/tr}}</button>
      {{if $rpu->code_diag}}
        <form name="deleteMotifRPU" action="#" method="post" onsubmit="return Motif.deleteDiag(this, 0);">
          {{mb_class  object=$rpu}}
          {{mb_key    object=$rpu}}
          <input type="hidden" name="code_diag" value="" />
          <button type="button" class="cancel notext" onclick="this.form.onsubmit();" style="float: right;">{{tr}}Delete{{/tr}}</button>
        </form>
      {{/if}}
    </td>
    <td style="text-align: center;">
      {{if $rpu->ccmu || $rpu->_estimation_ccmu}}
        <strong style="font-size: 2em;">{{tr}}CRPU.ccmu.{{if $rpu->ccmu}}{{$rpu->ccmu}}{{else}}{{$rpu->_estimation_ccmu}}{{/if}}-court{{/tr}}</strong> {{if !$rpu->ccmu}}(Estimation){{/if}}
      {{/if}}
      {{if $rpu->ccmu && $rpu->ccmu <=4 && $rpu->ccmu > 1}}
        <form name="modifCcmuRPU" action="#" method="post" onsubmit="return Motif.deleteDiag(this, 0);">
          {{mb_class  object=$rpu}}
          {{mb_key    object=$rpu}}
          <input type="hidden" name="ccmu" value="{{math equation="x-1" x=$rpu->ccmu}}" />
          <button type="button" class="up notext" onclick="this.form.onsubmit();" style="float: right;">Augmenter le dégré de l'urgence</button>
        </form>
      {{/if}}
    </td>
  </tr>
  {{if $rpu->code_diag}}
    <tr>
      <td colspan="2" style="text-align: center">
        <strong style="font-size: 1.4em;">{{$rpu->_ref_motif}}</strong>
        </td>
    </tr>
  {{/if}}
</table>

{{mb_include module=urgences template=vw_classement_cts}}