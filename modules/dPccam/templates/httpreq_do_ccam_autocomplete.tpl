<ul>
  {{foreach from=$result item=ccam}}
    <li>
    <span><strong>{{$ccam.CODE}}</strong></span>
    <span> - </span>
    <span>{{$ccam.LIBELLELONG|truncate:35:"...":false}}</span>
    </li>
  {{/foreach}}
</ul>