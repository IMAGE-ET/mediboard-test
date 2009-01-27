{{* $Id$ *}}

{{mb_include_script module="dPpatients" script="autocomplete"}}

<script type="text/javascript">
function setClose(id, view) {
  window.opener.Medecin.set(id, view);
  window.close();
}

Main.add(function () {
  if (document.editFrm) {
    initInseeFields("editFrm", "cp", "ville","tel");
  }
});
</script>

<table class="main">
  <tr>
    <td class="greedyPane">
    
      <form name="find" action="?" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      {{if $dialog}}
      <input type="hidden" name="a" value="vw_medecins" />
      <input type="hidden" name="dialog" value="1" />
      {{else}}
      <input type="hidden" name="tab" value="{{$tab}}" />
      {{/if}}
      <input type="hidden" name="new" value="1" />
      
      <table class="form">
        <tr>
          <th class="category" colspan="2">Recherche d'un correspondant</th>
        </tr>
  
        <tr>
          <th><label for="medecin_nom" title="Nom complet ou partiel du correspondant recherché">Nom</label></th>
          <td><input tabindex="1" type="text" name="medecin_nom" value="{{$nom|stripslashes}}" /></td>
        </tr>
        
        <tr>
          <th><label for="medecin_prenom" title="Prénom complet ou partiel du correspondant recherché">Prénom</label></th>
          <td><input tabindex="2" type="text" name="medecin_prenom" value="{{$prenom|stripslashes}}" /></td>
        </tr>
        
        <tr>
          <th><label for="medecin_dept" title="Département du correspondant recherché">Département (00 pour tous)</label></th>
          <td><input tabindex="3" type="text" name="medecin_dept" value="{{$departement|stripslashes}}" /></td>
        </tr>
        
        <tr>
          <th><label for="medecin_type" title="Type de correspondant recherché">Type</label></th>
          <td>
            <select name="medecin_type">
              <option value=""> &mdash; Tous</option>
              {{foreach from=$list_types item=curr_type key=key}}
                <option value="{{$key}}" {{if $type == $key}}selected="selected"{{/if}}>{{$curr_type}}</option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        
        <tr>
          <td class="button" colspan="2"><button class="search" type="submit">Rechercher</button></td>
        </tr>
      </table>

      </form>

      {{if !$dialog}}
      <form name="fusion" action="?" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="a" value="fusion_medecin" />
      <input type="hidden" name="objects_class" value="CMedecin" />
      <input type="hidden" name="readonly_class" value="true" />
      {{/if}}
      
      <table class="tbl">
        <tr>
          {{if !$dialog}}
          <th><button type="submit" class="search notext" title="Fusion">{{tr}}Merge{{/tr}}</button></th>
          {{/if}}
          <th>{{mb_title class=CMedecin field=nom}}</th>
          <th>{{mb_title class=CMedecin field=adresse}}</th>
          <th>{{mb_title class=CMedecin field=ville}}</th>
          <th>{{mb_title class=CMedecin field=cp}}</th>
          <th>{{mb_title class=CMedecin field=tel}}</th>
          <th>{{mb_title class=CMedecin field=fax}}</th>
          <th>{{mb_title class=CMedecin field=portable}}</th>
          <th>{{mb_title class=CMedecin field=type}}</th>
          {{if $dialog}}
          <th>{{tr}}Select{{/tr}}</th>
          {{/if}}
        </tr>

        {{foreach from=$medecins item=curr_medecin}}
        {{assign var=medecin_id value=$curr_medecin->_id}}
        <tr>
          {{mb_ternary var=href test=$dialog value="#choose" other="?m=$m&tab=$tab&medecin_id=$medecin_id"}}

          {{if !$dialog}}
          <td><input type="checkbox" name="objects_id[]" value="{{$curr_medecin->_id}}" /></td>
          {{/if}}

          <td class="text">
          	<script type="text/javascript">
          	</script>
          
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$curr_medecin->_id}}, '{{$curr_medecin->_view|smarty:nodefaults|JSAttribute}}' )"{{/if}}>
              {{$curr_medecin->_view}}
            </a>
          </td>
          <td class="text">
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$curr_medecin->_id}}, '{{$curr_medecin->_view|smarty:nodefaults|JSAttribute}}' )"{{/if}}>
              {{$curr_medecin->adresse}}
            </a>
          </td>
          <td class="text">
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$curr_medecin->_id}}, '{{$curr_medecin->_view|smarty:nodefaults|JSAttribute}}' )"{{/if}}>
              {{$curr_medecin->ville}}
            </a>
          </td>
          <td>
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$curr_medecin->_id}}, '{{$curr_medecin->_view|smarty:nodefaults|JSAttribute}}' )"{{/if}}>
              {{$curr_medecin->cp}}
            </a>
          </td>
          <td>
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$curr_medecin->_id}}, '{{$curr_medecin->_view|smarty:nodefaults|JSAttribute}}' )"{{/if}}>
              {{mb_value object=$curr_medecin field=tel}}
            </a>
          </td>
          <td>
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$curr_medecin->_id}}, '{{$curr_medecin->_view|smarty:nodefaults|JSAttribute}}' )"{{/if}}>
              {{mb_value object=$curr_medecin field=fax}}
            </a>
          </td>
          <td>
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$curr_medecin->_id}}, '{{$curr_medecin->_view|smarty:nodefaults|JSAttribute}}' )"{{/if}}>
              {{mb_value object=$curr_medecin field=portable}}
            </a>
          </td>
          <td>
            <a href="{{$href}}" {{if $dialog}}onclick="setClose({{$curr_medecin->_id}}, '{{$curr_medecin->_view|smarty:nodefaults|JSAttribute}}' )"{{/if}}>
              {{mb_value object=$curr_medecin field=type}}
            </a>
          </td>
          {{if $dialog}}
            <td>
              <button type="button" class="tick" onclick="setClose({{$curr_medecin->_id}}, '{{$curr_medecin->_view|smarty:nodefaults|JSAttribute}}' )">
              	{{tr}}Select{{/tr}}
              </button>
            </td>
          {{/if}}
        </tr>
        {{foreachelse}}
        <tr><td colspan="20">{{tr}}CMedecin.none{{/tr}}</td></tr>
        {{/foreach}}
      </table>

      {{if !$dialog}}
      </form>
      {{/if}}

    </td>
    
    {{if !$dialog}}
    <td class="pane">
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_medecins_aed" />
      {{mb_field object=$medecin field="medecin_id" hidden=1 prop=""}}
      <input type="hidden" name="del" value="0" />
      <table class="form">
        {{if !$dialog && $medecin->_id}}
        <tr>
          <td colspan="2"><a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;new=1">Créer un nouveau correspondant</a></td>
        </tr>
        {{/if}}
        <tr>
          <th class="category" colspan="2">
            {{if $medecin->_id}}
	            <a style="float:right;" href="#" onclick="view_log('CMedecin',{{$medecin->_id}})">
                <img src="images/icons/history.gif" alt="historique" />
              </a>
              Modification de la fiche de {{$medecin->_view}}
            {{else}}
              Création d'une fiche
            {{/if}}
          </th>
        </tr>

        <tr>
          <th>{{mb_label object=$medecin field="nom"}}</th>
          <td>{{mb_field object=$medecin field="nom"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="prenom"}}</th>
          <td>{{mb_field object=$medecin field="prenom"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="adresse"}}</th>
          <td>{{mb_field object=$medecin field="adresse"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="cp"}}</th>
          <td>
            {{mb_field object=$medecin field="cp" size="31" maxlength="5"}}
            <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="ville"}}</th>
          <td>
            {{mb_field object=$medecin field="ville" size="31"}}
            <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="tel"}}</th>
          <td>{{mb_field object=$medecin field="tel"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="fax"}}</th>
          <td>{{mb_field object=$medecin field="fax"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="portable"}}</th>
          <td>{{mb_field object=$medecin field="portable"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="email"}}</th>
          <td>{{mb_field object=$medecin field="email"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="type"}}</th>
          <td>{{mb_field object=$medecin field="type"}}</td>
        </tr>

        <tr>
          <th>{{mb_label object=$medecin field="disciplines"}}</th>
          <td>{{mb_field object=$medecin field="disciplines"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$medecin field="orientations"}}</th>
          <td>{{mb_field object=$medecin field="orientations"}}</td>
        </tr>

        <tr>
          <th>{{mb_label object=$medecin field="complementaires"}}</th>
          <td>{{mb_field object=$medecin field="complementaires"}}</td>
        </tr>

        <tr>
          <td class="button" colspan="4">
            {{if $medecin->_id}}
            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'le correspondant',objName:'{{$medecin->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
            {{else}}
            <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
      
      <!-- Patients liés -->
      {{if $medecin->_id}}
      <table class="form">
        <tr>
          <th class="category" colspan="2">Patients liés</th>
        </tr>
        <tr>
          <th>{{tr}}CMedecin-back-patients_traites{{/tr}}</th>
          <td>{{$medecin->_count_patients_traites}}</td>
        </tr>
        <tr>
          <th>{{tr}}CMedecin-back-patients_correspondants{{/tr}}</th>
          <td>{{$medecin->_count_patients_correspondants}}</td>
        </tr>
      </table>
      {{/if}}
      
    </td>
    {{/if}}
  </tr>
</table>
      