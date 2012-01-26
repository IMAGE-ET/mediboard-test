<form name="newNaissance" method="post" action="?"
  onsubmit="return onSubmitFormAjax(this, {onComplete: function() { Control.Modal.close(); Naissance.reloadNaissances('{{$naissance->operation_id}}'); }})">
  <input type="hidden" name="m" value="maternite" />
  <input type="hidden" name="dosql" value="do_create_naissance_aed" />
  
  {{mb_key object=$naissance}}
  {{mb_key object=$patient}}
  {{mb_key object=$constantes}}
  <input type="hidden" name="operation_id" value="{{$naissance->operation_id}}" />
  
  <table class="form">
    <tr>
      <th class="category" colspan="2">
        Informations sur la naissance
      </th>
    </tr>
    <tr>
      <th>
        {{mb_label object=$patient field="sexe"}}
      </th>
      <td>
        {{mb_field object=$patient field="sexe" emptyLabel="CPatient.sexe."}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$patient field="naissance"}}
      </th>
      <td>
        {{mb_field object=$patient field="naissance" form="newNaissance" register="true"}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$patient field="nom"}}
      </th>
      <td>
        {{mb_field object=$patient field="nom"}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$patient field="prenom"}}
      </th>
      <td>
        {{mb_field object=$patient field="prenom"}}
      </td>
    </tr>
    <tr>
      <th>
       {{mb_label object=$naissance field="hors_etab"}}
      </th>
      <td>
        {{mb_field object=$naissance field="hors_etab"}}
      </td>
    </tr>
    <tr>
      <th>
       {{mb_label object=$naissance field="heure"}}
      </th>
      <td>
        {{mb_field object=$naissance field="heure" form="newNaissance" register="true"}}
      </td>
    </tr>
    <tr>
      <th>
       {{mb_label object=$naissance field="rang"}}
      </th>
      <td>
        {{mb_field object=$naissance field="rang" size="2" increment="true" form="newNaissance" step="1"}}
      </td>
    </tr>
    <tr>
      <th class="category" colspan="2">{{tr}}CConstantesMedicales{{/tr}}</th>
    </tr>
    <tr>
      <th>
       {{mb_label object=$constantes field=poids}}
      </th>
      <td>
        {{mb_field object=$constantes field=poids size="3"}} {{$list_constantes.poids.unit}}
      </td>
    </tr>
    <tr>
      <th>
       {{mb_label object=$constantes field=taille}}
      </th>
      <td>
        {{mb_field object=$constantes field=taille size="3"}} {{$list_constantes.taille.unit}}
      </td>
    </tr>
    <tr>
      <th>
       {{mb_label object=$constantes field=perimetre_cranien}}
      </th>
      <td>
        {{mb_field object=$constantes field=perimetre_cranien size="3"}} {{$list_constantes.perimetre_cranien.unit}}
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="button" class="save" onclick="this.form.onsubmit();">
          {{tr}}{{if !$naissance->_id}}Create{{else}}Save{{/if}}{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>