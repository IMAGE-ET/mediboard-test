<table class="tbl" >
  {{foreach from=$pack->_back.modele_links item=_link}}
  <tr>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_link->_ref_modele->_guid}}')">
        {{$_link}}
      </span>
    </td>
    <td class="narrow">
      <form name="Del-{{$_link->_guid}}" action="?" method="post" onsubmit="return Pack.onSubmitModele(this);">
        {{mb_class object=$_link}}
        {{mb_key   object=$_link}}
        <input type="hidden" name="del" value="1" />
        <button class="remove notext compact" type="submit">{{tr}}Delete{{/tr}}</button>
      </form>
    </td>
  </tr>

  {{foreachelse}}
  <tr>
    <td class="empty">{{tr}}CPack-back-modele_links.empty{{/tr}}</td>
  </tr>

  {{/foreach}}
</table>
