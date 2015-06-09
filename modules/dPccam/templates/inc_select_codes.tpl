<option>Choisir le niveau suivant</option>
{{foreach from=$list item=_chap key=key_chap}}
  <option  value="{{$_chap.rang}}" data-code-pere="{{$key_chap}}">
    {{$_chap.rang}} - {{$_chap.texte|lower}}
  </option>
{{/foreach}}