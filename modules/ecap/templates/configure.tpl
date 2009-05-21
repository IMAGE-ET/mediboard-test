{{* $id: $ *}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <tr>
    <th class="category" colspan="10">DHE e-Cap</th>
  </tr>

  {{assign var="mod" value="interop"}}
  {{assign var="var" value="base_url"}}
  <tr>
    <th>
      <label for="{{$mod}}[{{$var}}]" title="{{tr}}config-{{$mod}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$mod}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" size="60" name="{{$mod}}[{{$var}}]" value="{{$dPconfig.$mod.$var}}" />
			<br/>
      <div class="small-info">
        Il s'agit de l'ancienne variable de configuration.
        <br/>
        Elle restera utilisée si la nouvelle variable (ci-desosus) n'est pas renseignée.
      </div>
    </td>
  </tr> 

  {{assign var="mod" value="ecap"}}
  {{assign var="class" value="dhe"}}
  <tr>
    <th class="category" colspan="10">{{tr}}config-{{$mod}}-{{$class}}{{/tr}}</th>
  </tr>

  {{assign var="var" value="rooturl"}}
  <tr>
    <th>
      <label for="{{$mod}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$mod}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$mod}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" size="30" name="{{$mod}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$mod.$class.$var}}" />
      {{$paths.dhe}}
    </td>
  </tr> 
   
  {{assign var="class" value="soap"}}
  <tr>
    <th class="category" colspan="10">{{tr}}config-{{$mod}}-{{$class}}{{/tr}}</th>
  </tr>

  {{assign var="var" value="rooturl"}}
  <tr>
    <th>
      <label for="{{$mod}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$mod}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$mod}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" size="30" name="{{$mod}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$mod.$class.$var}}" />
      {{$paths.soap.documents}}
    </td>
  </tr> 
   
  {{assign var="var" value="user"}}
  <tr>
    <th>
      <label for="{{$mod}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$mod}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$mod}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" name="{{$mod}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$mod.$class.$var}}" />
    </td>
  </tr> 

  {{assign var="var" value="pass"}}
  <tr>
    <th>
      <label for="{{$mod}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$mod}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$mod}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" name="{{$mod}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$mod.$class.$var}}" />
    </td>
  </tr> 

	<tr>
	  <th class="category" colspan="2">Tags d'identifications</th>
	</tr>

  {{assign var=mod value=dPpatients}}
  {{assign var=class value=CPatient}}
  {{assign var="var" value="tag_ipp"}}
  <tr>
    <th>
      <label for="{{$mod}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$mod}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$mod}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" name="{{$mod}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$mod.$class.$var}}" />
      {{if $dPconfig.$mod.$class.$var != $tags.PA}}
			<div class="small-warning">
				Le tag IPP pour l'utilisation du module e-Cap dans cet établissement devrait être :
				'{{$tags.PA}}'
				<br />
			  <button type="submit" class="change" onclick="this.form.elements['{{$mod}}[{{$class}}][{{$var}}]'].value = '{{$tags.PA}}'">
			  	{{tr}}Restore{{/tr}} le bon tag
			  </button>
			</div>
			{{else}}	
			<div class="small-success">
				Le tag IPP est compatible avec l'utilisation du module e-Cap dans cet établissement.
			</div>
			{{/if}}
    </td>
  </tr>
  
  {{assign var=mod value=dPplanningOp}}
  {{assign var=class value=CSejour}}
  {{assign var="var" value="tag_dossier"}}
  <tr>
    <th>
      <label for="{{$mod}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$mod}}-{{$class}}-{{$var}}-desc{{/tr}}">
        {{tr}}config-{{$mod}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" name="{{$mod}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$mod.$class.$var}}" />
      {{if $dPconfig.$mod.$class.$var != $tags.SJ}}
			<div class="small-warning">
				Le tag 'Numéro de dossier' pour l'utilisation du module e-Cap dans cet établissement devrait être :
				'{{$tags.SJ}}'
				<br />
			  <button type="submit" class="change" onclick="this.form.elements['{{$mod}}[{{$class}}][{{$var}}]'].value = '{{$tags.SJ}}'">
			  	{{tr}}Restore{{/tr}} le bon tag
			  </button>
			</div>
			{{else}}	
			<div class="small-success">
				Le tag 'Numéro de dossier' est compatible avec l'utilisation du module e-Cap dans cet établissement.
			</div>
			{{/if}}
    </td>
  </tr>

  <tr>
    <td class="button" colspan="10">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

{{include file=inc_configure_actions.tpl}}