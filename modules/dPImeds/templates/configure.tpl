<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">

  <tr>
    <th class="title" colspan="100">{{tr}}{{$m}}{{/tr}}</th>
  </tr>

  {{assign var="var" value="url"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" class="url" size="50" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}"/>
    </td>
  </tr>  
  
  {{assign var="var" value="remote_url"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" class="url" size="50" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}"/>
    </td>
  </tr>  
    
  {{assign var="var" value="soap_url"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td class="text">
      <input type="text" class="url" size="50" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}"/>
      {{$soap_path}}
      <br/>
      <div class="big-info">
        L'URL du serveur est prise par défaut, mais il est permis de fournir une URL spécifique pour les services web.
        <br/>Actuellement l'<strong>URL utilisée pour les services web</strong> est :
        <pre>{{$soap_url}}</pre>
      </div>
    </td>
  </tr>  

  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>

</table>

</form>

<table class="form">  
  <tr>
    <th colspan="2" class="title">Identifiants externes de {{$etab->_view}}</th>
  </tr>
  <tr>
    <th>CSDV</th>
    <td>
      <form name="editFrmCSDV" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPsante400" />
        <input type="hidden" name="dosql" value="do_idsante400_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="id_sante400_id" value="{{$idCSDV->_id}}" />
        <input type="hidden" name="object_class" value="CGroups" />
        <input type="hidden" name="object_id" value="{{$etab->_id}}" />
        <input type="hidden" name="tag" value="Imeds csdv" />
        <input type="hidden" name="last_update" value="{{$today}}" />
        <input type="text"  name="id400" value="{{$idCSDV->id400}}" />
        <button type="submit" class="notext submit">{{tr}}Submit{{/tr}}</button>
      </form>
    </td>
  </tr>
  <tr>
    <th>CDIV</th>
    <td>
      <form name="editFrmCDIV" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPsante400" />
        <input type="hidden" name="dosql" value="do_idsante400_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="id_sante400_id" value="{{$idCDIV->_id}}" />
        <input type="hidden" name="object_class" value="CGroups" />
        <input type="hidden" name="object_id" value="{{$etab->_id}}" />
        <input type="hidden" name="tag" value="Imeds cdiv" />
        <input type="hidden" name="last_update" value="{{$today}}" />
        <input type="text" name="id400" value="{{$idCDIV->id400}}" />
        <button type="submit" class="notext submit">{{tr}}Submit{{/tr}}</button>
      </form>
    </td>
  </tr>  
  <tr>
    <th>CIDC</th>
    <td>
      <form name="editFrmCIDC" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPsante400" />
        <input type="hidden" name="dosql" value="do_idsante400_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="id_sante400_id" value="{{$idCIDC->_id}}" />
        <input type="hidden" name="object_class" value="CGroups" />
        <input type="hidden" name="object_id" value="{{$etab->_id}}" />
        <input type="hidden" name="tag" value="Imeds cidc" />
        <input type="hidden" name="last_update" value="{{$today}}" />
        <input type="text" name="id400" value="{{$idCIDC->id400}}" />
        <button type="submit" class="notext submit">{{tr}}Submit{{/tr}}</button>
      </form>
    </td>
  </tr>  
</table>

