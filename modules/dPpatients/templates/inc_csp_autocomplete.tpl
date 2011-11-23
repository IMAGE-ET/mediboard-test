<ul>
  {{foreach from=$matches item=_match}}
    <li data-id="{{$_match.CODE}}">{{$_match.LIBELLE}}</li>
  {{/foreach}}
</ul>