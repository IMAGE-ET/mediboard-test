<ul>
  {{foreach from=$result item=ngap}}
    <li>
      <strong><span class="code">{{$ngap.code}}</span> (<span class="tarif">{{$ngap.tarif}}</span>)</strong>
      {{if $ngap.lettre_cle=='1'}}<strong><small>{{$ngap.libelle}}</small></strong>
      {{else}}<small>{{$ngap.libelle}}</small>
      {{/if}}
    </li>
  {{foreachelse}}
    <li>
      {{if !$specialite}}
        {{tr}}CActeNGAP-specialty-unspecified{{/tr}}
      {{else}}
        <i>Aucun acte NGAP correspondant</i>
      {{/if}}
    </li>
  {{/foreach}}
</ul>