{{mb_include_script module="dPpatients" script="autocomplete"}}

<script type="text/javascript">
Main.add(function () {
  initInseeFields("etabExterne", "cp", "ville");
});
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;etab_id=0" class="button new">
        Créer un établissement externe
      </a>
      <table class="tbl">
        <tr>
          <th>Liste des établissements externes</th>
        </tr>
        {{foreach from=$listEtabExternes item=curr_etab}}
        <tr {{if $curr_etab->_id == $etabExterne->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;etab_id={{$curr_etab->_id}}">
              {{$curr_etab->nom}}
            </a>
          </td>
          
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      <form name="etabExterne" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_etabExterne_aed" />
	  <input type="hidden" name="etab_id" value="{{$etabExterne->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
          {{if $etabExterne->etab_id}}
          
           <div class="idsante400" id="CEtabExterne-{{$etabExterne->etab_id}}"></div>
           
            <a style="float:right;" href="#" onclick="view_log('CEtabExterne',{{$etabExterne->etab_id}})">
              <img src="images/icons/history.gif" alt="historique" />
            </a>
            Modification de l'établissement &lsquo;{{$etabExterne->nom}}&rsquo;
          {{else}}
            Création d'un établissement externe
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="nom"}}</th>
          <td>{{mb_field object=$etabExterne field="nom" tabindex="1"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="raison_sociale"}}</th>
          <td>{{mb_field object=$etabExterne field="raison_sociale" tabindex="2"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="adresse"}}</th>
          <td>{{mb_field object=$etabExterne field="adresse" tabindex="3"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="cp"}}</th>
          <td>{{mb_field object=$etabExterne field="cp" tabindex="4"}}
            <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$etabExterne field="ville"}}</th>
          <td>{{mb_field object=$etabExterne field="ville" tabindex="5"}}
        	 <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="tel"}}</th>
		      <td>{{mb_field object=$etabExterne field="tel" tabindex="6"}}</td>
        </tr>
        <tr>
           <th>{{mb_label object=$etabExterne field="fax"}}</th>
		       <td>{{mb_field object=$etabExterne field="fax" tabindex="7"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="finess"}}</th>
          <td>{{mb_field object=$etabExterne field="finess" tabindex="8"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="siret"}}</th>
          <td>{{mb_field object=$etabExterne field="siret"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$etabExterne field="ape"}}</th>
          <td>{{mb_field object=$etabExterne field="ape"}}</td>
 		</tr>
        <tr>
          <td class="button" colspan="2">
          {{if $etabExterne->_id}}
            <button class="modify" type="submit" name="modify">
              {{tr}}Modify{{/tr}}
            </button>
            <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'l\'établissement',objName:'{{$etabExterne->nom|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
          {{else}}
            <button class="new" type="submit" name="create">
              {{tr}}Create{{/tr}}
            </button>
          {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>