  <form name="Edit-CChambre" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="dosql" value="do_chambre_aed" />
    <input type="hidden" name="del" value="0" />
    {{mb_key object=$chambre}}

    <table class="form">
    <tr>
      {{if $chambre->_id}}
      <th class="title modify text" colspan="2">
        {{mb_include module=system object=$chambre template=inc_object_notes     }}
        {{mb_include module=system object=$chambre template=inc_object_idsante400}}
        {{mb_include module=system object=$chambre template=inc_object_history   }}
        {{mb_include module=system object=$chambre template=inc_object_uf        }}
        {{tr}}CChambre-title-modify{{/tr}} '{{$chambre}}'
      {{else}}
      <th class="title text" colspan="2">
        {{tr}}CChambre-title-create{{/tr}}
      </th>
      {{/if}}
    </tr>
    
    <tr>
      <th>{{mb_label object=$chambre field=nom}}</th>
      <td>{{mb_field object=$chambre field=nom}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$chambre field=service_id}}</th>
      <td>{{mb_field object=$chambre field=service_id options=$services}}</td>
    </tr>    
    
    <tr>
      <th>{{mb_label object=$chambre field=caracteristiques}}</th>
      <td>{{mb_field object=$chambre field=caracteristiques}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$chambre field=lits_alpha}}</th>
      <td>{{mb_field object=$chambre field=lits_alpha}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$chambre field=annule}}</th>
      <td>{{mb_field object=$chambre field=annule}}</td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        {{if $chambre->_id}}
        <button class="submit" type="submit">{{tr}}Validate{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la chambre',objName: $V(this.form.nom) })">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
    </table>
    
  </form>
  
  {{if $chambre->_id}}
  <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id={{$chambre->_id}}&amp;lit_id=0">
    {{tr}}CLit-title-create{{/tr}}
  </a>
  
  <table class="tbl">
    <tr>
      <th class="category" colspan="2">
        {{tr}}CChambre-back-lits{{/tr}}
      </th>
    </tr>
    {{foreach from=$chambre->_ref_lits item=_lit}}
    <tr {{if $lit->_id == $_lit->_id}} class="selected" {{/if}}>
      <td>
        {{mb_include module=system template=inc_object_notes      object=$_lit}}
        {{mb_include module=system template=inc_object_idsante400 object=$_lit}}
        {{mb_include module=system template=inc_object_history    object=$_lit}}
        {{mb_include module=system template=inc_object_uf         object=$_lit }}
        <a href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id={{$_lit->chambre_id}}&amp;lit_id={{$_lit->_id}}">
          {{$_lit->nom}}
          {{if $_lit->nom_complet}}
            ({{$_lit->nom_complet}})
          {{/if}}
        </a>
      </td>
    </tr>
    {{/foreach}}
  </table>
  
  <form name="Edit-CLit" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
    
  <input type="hidden" name="dosql" value="do_lit_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$lit}}

  <input type="hidden" name="chambre_id" value="{{$chambre->_id}}" />

    <table class="form">
    <tr>
      <th>{{mb_label object=$lit field=nom}}</th>
      <td>{{mb_field object=$lit field=nom}}</td>
      <td rowspan="2">
        {{if $lit->_id}}
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le lit', objName: $V(this.form.nom)})">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$lit field=nom_complet}}</th>
      <td>{{mb_field object=$lit field=nom_complet}}</td>
    </tr>
    </table>
  </form>
  {{/if}}   