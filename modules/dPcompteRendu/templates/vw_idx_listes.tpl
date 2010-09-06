<!--  $Id$ -->

<script type="text/javascript">
Main.add(function () {
  if(oForm = document.addFrm) {
    document.addFrm._new.focus();
	}
});
</script>

<table class="main">

<tr>
  <td>
    
    <a href="?m={{$m}}&amp;tab={{$tab}}&amp;liste_id=0" class="button new">{{tr}}CListeChoix-title-create{{/tr}}</a> 

    <form name="Filter" action="?" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <table class="form">
        <tr>
          <th class="category" colspan="10">{{tr}}Filter{{/tr}}</th>
        </tr>
        <tr>
          <th><label for="filter_user_id">Utilisateur</label></th>
          <td>
            <select name="filter_user_id" onchange="this.form.submit()">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
							{{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$user->_id}}
            </select>
          </td>
        </tr>
      </table>
    </form>
    
		{{main}}Control.Tabs.create("tabs-owner", true);{{/main}}

	  <ul id="tabs-owner" class="control_tabs">
		  {{foreach from=$listes key=owner item=_listes}}
		  <li>
		    <a href="#owner-{{$owner}}" {{if !$_listes|@count}} class="empty" {{/if}}>
		      {{$owners.$owner}} 
		      <small>({{$_listes|@count}})</small>
		    </a>
		  </li>
		  {{/foreach}}
	  </ul>
	  <hr class="control_tabs" />

    <table class="tbl">
    
    <tr>
      <th>{{mb_title class=CListeChoix field=nom}}</th>
      <th>{{mb_title class=CListeChoix field=valeurs}}</th>
      <th>{{mb_title class=CListeChoix field=compte_rendu_id}}</th>
    </tr>
    
    {{foreach from=$listes key=owner item=_listes}}
    <tbody id="owner-{{$owner}}" style="display: none;">
      {{foreach from=$_listes item=_liste}}
      <tr {{if $_liste->_id == $liste->_id}} class="selected" {{/if}}>
        <td class="text">
          <a href="?m={{$m}}&amp;tab={{$tab}}&amp;liste_id={{$_liste->_id}}">
          	{{mb_value object=$_liste field=nom}}
					</a>
        </td>
        <td>
          {{$_liste->_valeurs|@count}}
        </td>
        <td class="text">
        	{{assign var=modele value=$_liste->_ref_modele}}
          {{if $modele->_id}}
            {{$modele}} ({{tr}}{{$modele->object_class}}{{/tr}})
          {{else}}
            &mdash; {{tr}}All{{/tr}}
          {{/if}}
        </td>
      </tr>
      {{foreachelse}}
      <tr>
        <td colspan="10">{{tr}}CListeChoix.none{{/tr}}</td>
      </tr>
      {{/foreach}}
		</tbody>
    {{/foreach}}
		
    </table>
  </td>
  
  <td>

    <form name="Edit" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)" class="{{$liste->_spec}}">

    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_liste_aed" />
    {{mb_key object=$liste}}

    <table class="form">

    {{mb_include module=system template=inc_form_table_header object=$liste}}
  
    <tr>
      <th>{{mb_label object=$liste field=chir_id}}</th>
      <td>
        <select name="chir_id" class="{{$liste->_props.chir_id}}" style="width: 12em;">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
					{{mb_include module=mediusers template=inc_options_mediuser list=$prats selected=$liste->chir_id}}
        </select>
      </td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$liste field=function_id}}</th>
      <td>
        <select name="function_id" class="{{$liste->_props.function_id}}" style="width: 12em;">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_function list=$funcs selected=$liste->function_id}}
        </select>
      </td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$liste field=group_id}}</th>
      <td>{{mb_field object=$liste field=group_id}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$liste field=nom}}</th>
      <td>{{mb_field object=$liste field=nom}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$liste field=compte_rendu_id}}</th>
      <td>
        <select name="compte_rendu_id" >
          <option value="">&mdash; {{tr}}All{{/tr}}</option>
          
          {{foreach from=$modeles key=owner item=_modeles}}
          <optgroup label="{{$owners.$owner}}">
            {{foreach from=$_modeles item=_modele}}
            <option value="{{$_modele->_id}}" {{if $liste->compte_rendu_id == $_modele->_id}} selected="selected" {{/if}}>
              {{$_modele->nom}} ({{tr}}{{$_modele->object_class}}{{/tr}})
            </option>
            {{foreachelse}}
            <option disabled="disabled">{{tr}}None{{/tr}}</option>
            {{/foreach}}
          </optgroup>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        {{if $liste->_id}}
        <button class="modify" type="submit">
          {{tr}}Save{{/tr}}
        </button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la liste',objName:$V(this.form.nom)})">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">
          {{tr}}Create{{/tr}}
        </button>
        {{/if}}
      </td>
    </tr>

    </table>
    
    </form>

  
  {{if $liste->_id}}
 
    {{if $liste->_valeurs|@count}}
    <table class="tbl">
      <tr><th class="category" colspan="2">Choix disponibles</th></tr>
      {{foreach from=$liste->_valeurs item=_valeur name=choix}}
      <tr>
        <td class="text">{{$_valeur|nl2br}}</td>
        <td style="width: 1%">
          <form name="DelChoix-{{$smarty.foreach.choix.iteration}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
          <input type="hidden" name="dosql" value="do_liste_aed" />
          <input type="hidden" name="del" value="0" />
          {{mb_key object=$liste}}

          {{mb_field object=$liste field=valeurs hidden=1}}
          <input type="hidden" name="_del" value="{{$_valeur}}" />
          <button class="remove notext" type="submit">{{tr}}Delete{{/tr}}</button>
          </form>
        </td>
      </tr>
      {{/foreach}}
    </table>
    {{/if}}
     
    <form name="addFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
    
    <input type="hidden" name="dosql" value="do_liste_aed" />
    <input type="hidden" name="del" value="0" />
    {{mb_key object=$liste}}

    {{mb_field object=$liste field=valeurs hidden=1}}

    <table class="form">
      <tr>
      	<th class="category" colspan="2">Ajouter un choix</th>
      </tr>
      <tr>
        <td>
	        <textarea name="_new"></textarea>
	      </td>
	    </tr>
	    <tr>
        <td class="button">
	        <button type="submit" class="add">{{tr}}Add{{/tr}}</button>
      	</td>
     	</tr>
    </table>

    </form>

  {{/if}}
  </td>  
</tr>
</table>