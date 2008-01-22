<ul>
  {{foreach from=$result item=ngap}}
    <li>
      <strong>{{$ngap.code}}   ({{$ngap.tarif}})</strong>
      <small>{{$ngap.libelle|truncate:35:"...":false}}</small>
    </li>
  {{/foreach}}
</ul>