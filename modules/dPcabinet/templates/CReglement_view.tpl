{{include file=CMbObject_view.tpl}}

{{if $can->admin}}
  <form name="reglement-delete" action="?m={{$m}}" method="post" onsubmit="return confirm('Vraiment ?') && onSubmitFormAjax(this, function(){location.reload()})">
    <input type="hidden" name="m" value="dPcabinet" />
    <input type="hidden" name="del" value="1" />
    <input type="hidden" name="dosql" value="do_reglement_aed" />
    <input type="hidden" name="reglement_id" value="{{$object->_id}}" />
    <button class="trash" type="submit">
      {{tr}}Delete{{/tr}}
    </button>
  </form>
{{/if}}
