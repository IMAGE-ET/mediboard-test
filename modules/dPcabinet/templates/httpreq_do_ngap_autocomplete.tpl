<ul>
  {{foreach from=$result item=ngap}}
    <li>
      <strong><span class="code">{{$ngap.code}}</span> (<span class="tarif">{{$ngap.tarif}}</span>)</strong>
      <small>{{$ngap.libelle}}</small>
    </li>
  {{/foreach}}
</ul>