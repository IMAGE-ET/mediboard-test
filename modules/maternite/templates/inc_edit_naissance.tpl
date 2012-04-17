<form name="newNaissance" method="post" action="?"
  onsubmit="return onSubmitFormAjax(this, {onComplete: function() { Control.Modal.close(); Naissance.reloadNaissances('{{$operation_id}}'); }})">
  <input type="hidden" name="m" value="maternite" />
  <input type="hidden" name="del" value="0" />
  {{mb_field object=$naissance field=sejour_maman_id hidden=true}}
  {{mb_field object=$naissance field=operation_id hidden=true}}
  
  {{if $provisoire}}
    <input type="hidden" name="dosql" value="do_dossier_provisoire_aed" />
  {{else}}
    <input type="hidden" name="dosql" value="do_create_naissance_aed" />
  {{/if}}
  {{if $callback}}
    <input type="hidden" name="callback" value="{{$callback}}" />
  {{/if}}
  
  {{if $naissance}}
    {{mb_key object=$naissance}}
  {{/if}}
 
  {{if $parturiente}} 
    {{mb_key object=$parturiente}}
  {{/if}}

  {{if $constantes}} 
    {{mb_key object=$constantes}}
  {{/if}}
  
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
        {{mb_field object=$patient field="sexe" emptyLabel="Choose"}}
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
    
    {{if !$provisoire}}
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
    {{/if}}
    <tr>
      <th>
       {{mb_label object=$naissance field="rang"}}
      </th>
      <td>
        {{mb_field object=$naissance field="rang" size="2" increment="true" form="newNaissance" step="1"}}
      </td>
    </tr>
    {{if !$provisoire}}
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
    {{/if}}
    
    <tr>
      <th>
        {{mb_label object=$sejour field=praticien_id}}
      </th>
      <td>
        <select name="praticien_id">
          <option value="">&mdash; Choisissez un praticien</option>
          {{mb_include module=mediusers template=inc_options_mediuser list=$praticiens selected=$sejour->praticien_id}}
        </select>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        {{if $naissance->_id}}
          <button type="submit" class="submit">{{tr}}Modify{{/tr}}</button>
          <button type="button" class="trash" onclick="Naissance.confirmDeletion(this.form)">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button type="submit" class="submit singleclick">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>

  </table>
</form>