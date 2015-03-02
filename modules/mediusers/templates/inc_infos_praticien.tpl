<tr>
  <td colspan="2" class="text">
    <div class="small-info">
      Informations pertinentes pour les seuls 
      <strong>professionnels de santé</strong>.
    </div>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$object field=discipline_id}}</th>
  <td>{{mb_field object=$object field=discipline_id options=$disciplines style="width: 250px;"}}</td> 
</tr>


<tr>  
  <th>{{mb_label object=$object field=spec_cpam_id}}</th>
  <td>{{mb_field object=$object field=spec_cpam_id options=$spec_cpam style="width: 250px;"}}</td> 
</tr>

{{if @$modules.eai}}
  <tr>
    <th>{{mb_label object=$object field=other_specialty_id}}</th>
    <td>{{mb_field object=$object field=other_specialty_id autocomplete="true,1,50,true,true" form=$name_form}}</td>
  </tr>
{{/if}}

{{if $conf.ref_pays == 1}}
  <tr>
    <th>{{mb_label object=$object field="adeli"}}</th>
    <td>{{mb_field object=$object field="adeli"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$object field="rpps"}}</th>
    <td>{{mb_field object=$object field="rpps"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$object field="cps"}}</th>
    <td>{{mb_field object=$object field="cps"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$object field="mail_apicrypt"}}</th>
    <td>{{mb_field object=$object field="mail_apicrypt"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$object field="secteur"}}</th>
    <td>{{mb_field object=$object field="secteur" emptyLabel="Choose"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$object field=contrat_acces_soins}}</th>
    <td>{{mb_field object=$object field=contrat_acces_soins}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$object field=option_coordination}}</th>
    <td>{{mb_field object=$object field=option_coordination}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$object field="cab"}}</th>
    <td>{{mb_field object=$object field="cab"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$object field="conv"}}</th>
    <td>{{mb_field object=$object field="conv"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$object field="zisd"}}</th>
    <td>{{mb_field object=$object field="zisd"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$object field="ik"}}</th>
    <td>{{mb_field object=$object field="ik"}}</td>
  </tr>
{{elseif $conf.ref_pays == 3}}
  <tr>
    <th>{{mb_label object=$object field='inami'}}</th>
    <td>{{mb_field object=$object field='inami'}}</td>
  </tr>
{{/if}}

<tr>
  <th>{{mb_label object=$object field="titres"}}</th>
  <td>{{mb_field object=$object field="titres"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$object field="compta_deleguee"}}</th>
  <td>{{mb_field object=$object field="compta_deleguee"}}</td>
</tr>

{{if $conf.ref_pays == 1}}
  <tr>
    <th>{{mb_label object=$object field="compte"}}</th>
    <td>{{mb_field object=$object field="compte"}}</td>
  </tr>
  
  {{if is_array($banques)}}
  <!-- Choix de la banque quand disponible -->
  <tr>
    <th>{{mb_label object=$object field="banque_id"}}</th>
    <td>
      <select name="banque_id" style="width: 150px;">
        <option value="">&mdash; Choix d'une banque</option>
        {{foreach from=$banques item="banque"}}
        <option value="{{$banque->_id}}" {{if $object->banque_id == $banque->_id}}selected = "selected"{{/if}}>
          {{$banque->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  {{/if}}
{{/if}}

{{if $conf.ref_pays == 2}}
  <tr>
    <th>{{mb_label object=$object field="ean"}}</th>
    <td>{{mb_field object=$object field="ean"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field="rcc"}}</th>
    <td>{{mb_field object=$object field="rcc"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field="adherent"}}</th>
    <td>{{mb_field object=$object field="adherent"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field="debut_bvr"}}</th>
    <td>{{mb_field object=$object field="debut_bvr"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field="electronic_bill"}}</th>
    <td>{{mb_field object=$object field="electronic_bill"}}</td>
  </tr>

  {{if $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
    <script>
      Main.add(function () {
        var form = getForm("{{$name_form}}");
        var url = new Url("tarmed", "ajax_specialite_autocomplete");
        url.autoComplete(form.specialite_tarmed, null, {
          minChars: 0,
          dropdown: true,
          select: "newspec",
          updateElement: function(selected) {
            $V(form.specialite_tarmed, selected.down(".newspec").getText(), false);
          }
        });
      });
    </script>
    <tr>
      <th>{{mb_label object=$object field="specialite_tarmed"}}</th>
      <td>{{mb_field object=$object field="specialite_tarmed" style="width:200px;"}}</td>
    </tr>
  {{/if}}
{{/if}}