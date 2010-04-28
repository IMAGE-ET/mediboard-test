{{* $id: $ *}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  {{assign var=m value=ecap}}
  
  {{mb_include module=system template=configure_handler class_handler=CEcObjectHandler}}

  {{assign var=class value=CMouvSejourEcap}}
  <tr>
    <th class="category" colspan="10">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>

  {{mb_include module=system template=inc_config_bool var=handle_dhe}}

  {{assign var=class value=dhe}}
  <tr>
    <th class="category" colspan="10">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>

  {{mb_include module=system template=inc_config_str var=rooturl}}
  <tr>
    <th />
    <td>{{$paths.dhe}}</td>
  </tr> 
   
  <tr>
    <th class="category" colspan="2">Tags d'identifications</th>
  </tr>
  
  {{mb_include module=sip template=inc_config_tags pat=$tags.PA sej=$tags.SJ}} 

  <tr>
    <td class="button" colspan="10">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>

</form>
