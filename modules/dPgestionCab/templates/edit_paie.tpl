<!-- $Id$ -->

<script type="text/javascript">
function printFiche(iFiche_id) {
  var url = new Url("dPgestionCab", "print_fiche");
  url.addParam("fiche_paie_id", iFiche_id);
  url.popup(700, 550, "Fiche");
}

function saveFiche() {
  var oForm = document.forms.editFrm;
  oForm._final_store.value = "1";
  oForm.submit();
}
</script>

<table class="main">
  <tr>
    <td colspan="2">
      <form name="userSelector" action="?" method="get">

      <input type="hidden" name="m" value="{{$m}}" />
      {{mb_label class=CParamsPaie field=employecab_id}}
      <select name="employecab_id" onchange="this.form.submit()">
      {{foreach from=$listEmployes item=_employe}}
        <option value="{{$_employe->employecab_id}}" {{if $_employe->employecab_id == $employe->employecab_id}}selected="selected"{{/if}}>
          {{$_employe}}
        </option>
      {{/foreach}}
      </select>

      </form>

      {{if $fichePaie->_id}}
      <br />
      <a class="button new" href="?m={{$m}}&amp;tab=edit_paie&amp;fiche_paie_id=0">
				{{tr}}CFichePaie-title-create{{/tr}}
      </a>
      {{/if}}
    </td>
  </tr>
    
  <tr>
    <td class="halfPane">
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      
      <input type="hidden" name="dosql" value="do_fichePaie_aed" />
      <input type="hidden" name="m" value="dPgestionCab" />
      <input type="hidden" name="del" value="0" />
      
      <input type="hidden" name="_final_store" value="0" />
      {{mb_key object=$fichePaie}}
      {{mb_field object=$fichePaie field=params_paie_id hidden=1}}
      
      <table class="form">
        {{if $fichePaie->_id}}
	        <tr>
	          <th class="title modify"colspan="2">
	            {{mb_include module=system template=inc_object_idsante400 object=$fichePaie}}
	            {{mb_include module=system template=inc_object_history object=$fichePaie}}
	            
			        {{if $fichePaie->_locked}}
			        	{{$fichePaie}} Cloturée
			        {{else}}
	  	          {{tr}}CFichePaie-title-modify{{/tr}} '{{$fichePaie}}'
			        {{/if}}
	          </th>
	        </tr>
        {{else}}
	        <tr>
	          <th class="title" colspan="2">{{tr}}CFichePaie-title-create{{/tr}}</th>
	        </tr>
        {{/if}}
        
        <tr>
          <th>{{mb_label object=$fichePaie field="debut"}}</th>
          <td>{{mb_field object=$fichePaie field="debut" form="editFrm" register=true}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$fichePaie field="fin"}} </th>
          <td>{{mb_field object=$fichePaie field="fin" form="editFrm" register=true}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$fichePaie field="salaire"}}</th>
          <td>{{mb_field object=$fichePaie field="salaire"}}</td> 
        </tr>
        
        <tr>
          <th>{{mb_label object=$fichePaie field="heures"}}</th>
          <td>{{mb_field object=$fichePaie field="heures"}}h</td> 
        </tr>
        
        <tr>
          <th>{{mb_label object=$fichePaie field="heures_comp"}}</th>
          <td>{{mb_field object=$fichePaie field="heures_comp"}}h</td> 
        </tr>
        
        <tr>
          <th>{{mb_label object=$fichePaie field="heures_sup"}}</th>
          <td>{{mb_field object=$fichePaie field="heures_sup"}}h</td> 
        </tr>
        
        <tr>
          <th>{{mb_label object=$fichePaie field="precarite"}}</th>
          <td>{{mb_field object=$fichePaie field="precarite"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$fichePaie field="anciennete"}}</th>
          <td>{{mb_field object=$fichePaie field="anciennete"}}</td> 
        </tr>
        
        <tr>
          <th>{{mb_label object=$fichePaie field="conges_payes"}}</th>
          <td>{{mb_field object=$fichePaie field="conges_payes"}}</td> 
        </tr>
        
        <tr>
          <th>{{mb_label object=$fichePaie field="prime_speciale"}}</th>
          <td>{{mb_field object=$fichePaie field="prime_speciale"}}</td>
        </tr>
        
        <tr>
          <td class="button" colspan="2">
            {{if !$fichePaie->_locked}}
	            {{if $fichePaie->_id}}
	            <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
	            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la ',objName:'{{$fichePaie->_view|smarty:nodefaults|JSAttribute}}'})">
	              {{tr}}Delete{{/tr}}
	            </button>
	            <button class="print" type="button" onclick="printFiche(this.form.fiche_paie_id.value)">
	              {{tr}}Print{{/tr}}
	            </button>
	            <button class="tick" type="button" onclick="saveFiche()">
	              {{tr}}Enclose{{/tr}}
	            </button>
	            {{else}}
	            <button class="new" type="submit">{{tr}}Create{{/tr}}</button>
	            {{/if}}
            {{else}}
            <button class="print" type="button" onclick="printFiche(this.form.fiche_paie_id.value)">
              {{tr}}Print{{/tr}}
            </button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
    
    <td class="halfPane">
      <table class="form">
        <tr>
          <th class="title" colspan="3">Anciennes Fiches de paie</th>
        </tr>
        {{foreach from=$listFiches item=_fiche}}
        <tr>
          <td class="text">
            <a href="?m=dPgestionCab&amp;tab=edit_paie&amp;fiche_paie_id={{$_fiche->_id}}" onmouseover="ObjectTooltip.createEx(this, '{{$_fiche->_guid}}');" >
              {{$_fiche}}
            </a>
          </td>
          <td class="button" style="width: 1%">
            <button class="print" type="button" onclick="printFiche({{$_fiche->_id}})">
              {{tr}}Print{{/tr}}
            </button>

            {{if $_fiche->_locked}}
            CLOTUREE
            {{else}}
            <form name="editFrm{{$_fiche->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_fichePaie_aed" />
            <input type="hidden" name="m" value="dPgestionCab" />
            <input type="hidden" name="del" value="0" />

            {{mb_key object=$_fiche}}

            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la ',objName:'{{$_fiche->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
            </form>
            {{/if}}
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td>
            <em>CFichePaie.none </em>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>