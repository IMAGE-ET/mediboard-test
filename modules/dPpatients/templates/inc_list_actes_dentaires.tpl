{{foreach from=$actes_dentaires item=_acte_dentaire}}
  <fieldset>
    <legend>
      {{$_acte_dentaire->code}} : <span class="compact">{{$_acte_dentaire->libelleLong}}</span>
    </legend>
    {{$_acte_dentaire->commentaire}}
  </fieldset>
{{/foreach}}
