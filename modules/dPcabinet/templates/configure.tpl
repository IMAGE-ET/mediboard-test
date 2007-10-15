<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <!-- Mode d'addictions -->  
  <tr>
    <th class="category" colspan="100">Mode d'addictions</th>
  </tr>
  
  <tr>
    {{assign var="var" value="addictions"}}
    <th colspan="3">
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <select class="bool" name="{{$m}}[{{$var}}]">
        <option value="0" {{if 0 == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-0{{/tr}}</option>
        <option value="1" {{if 1 == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>

  <!-- CPlageconsult -->  
  {{assign var="class" value="CPlageconsult"}}
    
  <tr>
    <th class="category" colspan="100">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  <tr>
    {{assign var="var" value="hours_start"}}
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

    {{assign var="var" value="hours_stop"}}
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

    {{assign var="var" value="minutes_interval"}}
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
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>
</form>
<br />
<table class="form">
  <tr>
    <th class="category" colspan="2">Gestion des banques</th>
  </tr>
     <tr>
       <td class="halfPane">
   <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;banque_id=0" class="buttonnew">
     Créer une banque
   </a>
   <table class="tbl">
   <tr>
     <th colspan="3" class="title">Liste des banques</th>
   </tr>
   <tr>
     <th class="category">Nom</th>
     <th class="category">Description</th>
   </tr>
{{foreach from=$banques item=_banque}}
   <tr {{if $_banque->_id == $banque->_id}}class="selected"{{/if}}>
     <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;banque_id={{$_banque->_id}}">{{$_banque->nom}}</a></td>
     <td class="text">{{$_banque->description|nl2br}}</td>
   </tr>
   {{/foreach}}
   </table>
 </td> 
 <td class="halfPane">
   <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
   <input type="hidden" name="dosql" value="do_banque_aed" />
   <input type="hidden" name="banque_id" value="{{$banque->_id}}" />
   <input type="hidden" name="del" value="0" />
   <table class="form">
   <tr>
     {{if $banque->_id}}
     <th class="title modify" colspan="2">
       <div class="idsante400" id="{{$banque->_class_name}}-{{$banque->_id}}"></div>
       <a style="float:right;" href="#nothing" onclick="view_log('{{$banque->_class_name}}',{{$banque->_id}})">
       <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
       </a>
       Modification de la banque &lsquo;{{$banque->nom}}&rsquo;
     </th>
     {{else}}
     <th class="title" colspan="2">
       Création d'une banque
     </th>
     {{/if}}
   </tr>
   <tr>
     <th>{{mb_label object=$banque field="nom"}}</th>
     <td>{{mb_field object=$banque field="nom"}}</td>
   </tr>       
   <tr>
     <th>{{mb_label object=$banque field="description"}}</th>
     <td>{{mb_field object=$banque field="description"}}</td>
   </tr>    
   <tr>
     <td class="button" colspan="2">
       {{if $banque->_id}}
       <button class="modify" type="submit">Valider</button>
       <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la banque ',objName:'{{$banque->nom|smarty:nodefaults|JSAttribute}}'})">
         Supprimer
       </button>
       {{else}}
       <button class="submit" name="btnFuseAction" type="submit">Créer</button>
       {{/if}}
     </td>
   </tr>
   </table>   
  </form>
  </td>
   </tr>
</table>







