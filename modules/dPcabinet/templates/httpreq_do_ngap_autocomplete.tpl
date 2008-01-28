<ul>
  {{foreach from=$result item=ngap}}
    <li>
      <strong>{{$ngap.code}}   ({{$ngap.tarif}})</strong>
      <small>{{$ngap.libelle}}</small>
    </li>
  {{/foreach}}
</ul>