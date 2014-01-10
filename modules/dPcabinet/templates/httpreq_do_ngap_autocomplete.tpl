<ul>
  {{if $spe_undefined}}
    <li disabled><i>{{tr}}CActeNGAP-specialty-undefined{{/tr}}</i></li>
  {{/if}}
  {{foreach from=$result item=ngap}}
    <li>
      <strong><span class="code">{{$ngap.code}}</span> (<span class="tarif">{{$ngap.tarif}}</span>)</strong>
      {{if $ngap.lettre_cle=='1'}}<strong><small>{{$ngap.libelle}}</small></strong>
      {{else}}<small>{{$ngap.libelle}}</small>
      {{/if}}
    </li>
  {{foreachelse}}
    <li>
      <i>Aucun acte NGAP correspondant</i>
    </li>
  {{/foreach}}
</ul>