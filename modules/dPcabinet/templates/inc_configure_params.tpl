<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <colgroup>
    <col style="width: 30%;" />
  </colgroup>

  <!-- Prise de rendez-vous -->  
  <tr>
    <th class="category" colspan="2">Prise de rendez-vous</th>
    <th class="category">Aide</th>
  </tr>
  
  <tr>
    {{assign var="var" value="keepchir"}}
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <label for="{{$m}}[{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$var}}]" value="1" {{if $dPconfig.$m.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$var}}]" value="0" {{if $dPconfig.$m.$var == "0"}}checked="checked"{{/if}}/>
    </td>
    
    <td rowspan="100">
      <div class="big-info">
        <b>Format des champs auto :</b>
        <ul>
          <li><tt>%N</tt> - Nom praticien interv</li>
          <li><tt>%P</tt> - Pr�nom praticien interv</li>
          <li><tt>%S</tt> - Initiales praticien interv</li>
          <li><tt>%L</tt> - Libell� intervention</li>
          <li><tt>%I</tt> - Jour intervention</li>
          <li><tt>%i</tt> - Heure intervention</li>
          <li><tt>%E</tt> - Jour d'entr�e</li>
          <li><tt>%e</tt> - Heure d'entr�e</li>
          <li><tt>%T</tt> - Type de s�jour (A, O, E...)</li>
        </ul>
      </div>
    </td>
  </tr>

  <!-- CConsultAnesth -->  
  {{assign var="class" value="CConsultAnesth"}}
    
  <tr>
    <th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  <tr>
    {{assign var="var" value="feuille_anesthesie"}}
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="str" name="{{$m}}[{{$class}}][{{$var}}]">
        <option value="print_fiche" {{if "print_fiche" == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-print_fiche{{/tr}}</option>
        <option value="print_fiche1" {{if "print_fiche1" == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-print_fiche1{{/tr}}</option>
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="format_auto_motif"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>
  
  {{assign var="var" value="format_auto_rques"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input type="text" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dPconfig.$m.$class.$var}}" />
    </td>
  </tr>

  <!-- CPlageconsult -->  
  {{assign var="class" value="CPlageconsult"}}
    
  <tr>
    <th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="hours_start"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$hours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>
          {{$_hour|string_format:"%02d"}}
        </option>
      {{/foreach}}
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="hours_stop"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$hours item=_hour}}
        <option value="{{$_hour}}" {{if $_hour == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>
          {{$_hour|string_format:"%02d"}}
        </option>
      {{/foreach}}
      </select>
    </td>
  </tr>
  
  {{assign var="var" value="minutes_interval"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}]">
      {{foreach from=$intervals item=_interval}}
        <option value="{{$_interval}}" {{if $_interval == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>
          {{$_interval|string_format:"%02d"}}
        </option>
      {{/foreach}}
      </select>
    </td>
  </tr>
  
  
  <!-- CPrescription -->  
  {{assign var="class" value="CPrescription"}}
  <tr>
    <th class="category" colspan="2">Prescriptions</th>
  </tr>
  <tr>
    {{assign var="var" value="view_prescription"}}
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/>
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>

</form>