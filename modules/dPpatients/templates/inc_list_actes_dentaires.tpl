{{foreach from=$actes_dentaires item=_acte_dentaire}}
  <fieldset>
    <legend>
      {{$_acte_dentaire->code}}
      <form name="delCode" method="post" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_acte_dentaire_aed" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="callback" value="afterActeDentaire" />
        {{mb_key object=$_acte_dentaire}}
        {{mb_field object=$_acte_dentaire field=code hidden=true}}
        <button type="button" class="notext trash" onclick="this.form.onsubmit();" title="Supprimer"></button>
      </form>
    </legend>
    {{mb_value object=$_acte_dentaire field=commentaire}}
  </fieldset>
{{/foreach}}
