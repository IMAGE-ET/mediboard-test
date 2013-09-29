{{assign var=class value=CFile}}

<form name="EditConfig-{{$class}}-ooo" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <col style="width: 50%" />

  {{mb_include module=system template=inc_config_bool var=ooo_active}}
  {{mb_include module=system template=inc_config_str var=ooo_path size=40}}
  {{mb_include module=system template=inc_config_str var=python_path size=40}}
  
  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>

<table class="tbl">
  <tr>
    <td class="button">
      <button type="button" class="change" onclick="new Url('dPfiles', 'state_ooo').requestUpdate('openoffice');">
        {{tr}}Status{{/tr}}
      </button>
    </td>
    <td id="openoffice"></td>
  </tr>
</table>

<div class="big-info">
  Pour lancer OpenOffice, exécutez ces commandes dans un terminal en <strong>root</strong>: 
  <br/>
  <code>
    > su <em>[nom de l'utilisateur Apache]</em>
  </code>
  <br/>
  <code>
    >  export HOME=/tmp; <em>[path]</em>/soffice -accept="socket,host=localhost,port=8100;urp;StarOffice.ServiceManager" -no-logo -headless -nofirststartwizard -no-restore >> /dev/null &
  </code>
</div>
