<ul>
  {{foreach from=$result item=ccam}}
    <li>
      <strong>{{$ccam.CODE}}</strong>
      <br />
      <small>{{$ccam.LIBELLELONG|truncate:35:"...":false}}</small>
    </li>
  {{/foreach}}
</ul>