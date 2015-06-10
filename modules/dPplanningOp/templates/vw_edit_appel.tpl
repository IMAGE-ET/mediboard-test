<form name="Edit-CAppelSejour" method="post" onsubmit="return Appel.submit(this);">
  <input type="hidden" name="m" value="{{$m}}" />
  {{mb_key    object=$appel}}
  {{mb_class  object=$appel}}
  <input type="hidden" name="del" value="0"/>
  {{mb_field object=$appel field=sejour_id hidden=true}}
  {{mb_field object=$appel field=type hidden=true}}
  {{mb_field object=$appel field=etat hidden=true}}

  <table class="form">
    {{if $sejour->_ref_appels_by_type.$type|@count}}
      <tr>
        <th colspan="2" class="title">Liste des appels</th>
      </tr>
      {{foreach from=$sejour->_ref_appels_by_type.$type item=_appel}}
        <tr>
          <th>{{mb_value object=$_appel field=etat}}</th>
          <td>
            <strong>[{{mb_value object=$_appel field=datetime}}]</strong>
            <span class="compact">{{$_appel->commentaire}}</span>
          </td>
        </tr>
      {{/foreach}}
    {{/if}}
    <tr>
      <th class="title" colspan="2">
        {{assign var=patient value=$sejour->_ref_patient}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
          {{$patient->_view}} - {{mb_value object=$patient field=_age}} ({{mb_value object=$patient field=naissance}})
        </span>
        <br/>

      </th>
    </tr>
    <tr>
      <th></th>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');">
          {{$sejour->_view}}
        </span>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$patient field=tel}}</th>
      <td>{{mb_value object=$patient field=tel}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$patient field=tel2}}</th>
      <td>{{mb_value object=$patient field=tel2}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$appel field=datetime}}</th>
      <td>{{mb_field object=$appel field=datetime form="Edit-CAppelSejour" canNull="true" register=true}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$appel field=commentaire}}</th>
      <td>{{mb_field object=$appel field=commentaire textearea=true}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="tick" type="button" onclick="Appel.changeEtat(this.form, 'realise');">{{tr}}CAppelSejour.etat.realise{{/tr}}</button>
        <button class="cancel" type="button" onclick="Appel.changeEtat(this.form, 'echec');">{{tr}}CAppelSejour.etat.echec{{/tr}}</button>
        <button class="cancel" type="button" onclick=" Appel.modal.close();">{{tr}}Cancel{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>