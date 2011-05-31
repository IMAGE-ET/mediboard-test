<ul style="text-align: left;">
  {{foreach from=$circonstances item=_circonstance}}
    <li>
      {{if $orumip_active}}
        <span>{{$_circonstance.libelle|emphasize:$_keywords_circonstance}}</span>
        <span class="view" style="display: none;">{{$_circonstance.libelle}}</span>
      {{else}}
        <span>{{$_circonstance.code|emphasize:$_keywords_circonstance}}</span>
        <span class="view" style="display: none;">{{$_circonstance.code}}</span>
      {{/if}}
      <span class="libelle_circonstance" style="display: none;">{{$_circonstance.libelle}}</span>
      <span class="code" style="display: none;">{{$_circonstance.code}}</span>
    </li>
  {{/foreach}}
</ul>