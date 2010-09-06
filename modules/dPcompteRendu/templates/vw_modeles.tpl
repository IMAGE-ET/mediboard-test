
<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-owner', true);
});
</script>

<form name="selectPrat" action="?" method="get">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="form">
        
<tr>
  <th class="category" colspan="10">{{tr}}CCompteRendu-filter{{/tr}}</th>
</tr>

<tr>
  <th>{{mb_label object=$filtre field=chir_id}}</th>
  <td>
    <select name="chir_id" onchange="submit()">
      <option value="">&mdash; {{tr}}CCompteRendu-choose-user{{/tr}}</option>
      {{foreach from=$praticiens item=curr_prat}}
        <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->_id}}" 
        	{{if $curr_prat->_id == $filtre->chir_id}} selected="selected" {{/if}}>
          {{$curr_prat->_view}}
        </option>
      {{/foreach}}
    </select>
  </td>

  <th>{{mb_label object=$filtre field=object_class}}</th>
  <td>
	 {{assign var=_spec value=$filtre->_specs.object_class}}
    <select name="object_class" onchange="this.form.submit()">
      <option value="">&mdash; {{tr}}CCompteRendu-type-all{{/tr}}</option>
      {{foreach from=$_spec->_locales item=_locale key=_object_class}}
        <option value="{{$_object_class}}" {{if $filtre->object_class == $_object_class}} selected="selected" {{/if}}>{{$_locale}}</option>
      {{/foreach}}
    </select>
	</td>

  <th>{{mb_label object=$filtre field=type}}</th>
  <td>{{mb_field object=$filtre field=type onchange="this.form.submit()" canNull=true defaultOption="&mdash; Tous les types"}}</td>
</tr>

</table>

</form>

{{if $user->_id}}

  <ul id="tabs-owner" class="control_tabs">
  	{{foreach from=$modeles key=owner item=_modeles}}
    <li>
    	<a href="#owner-{{$owner}}" {{if !$_modeles|@count}} class="empty" {{/if}}>
    	  {{$owners.$owner}} 
			  <small>({{$_modeles|@count}})</small>
			</a>
		</li>
  	{{/foreach}}
  </ul>
  <hr class="control_tabs" />
  
  {{foreach from=$modeles key=owner item=_modeles}}
	  <div id="owner-{{$owner}}" style="display: none;">
	  {{include file=inc_modeles.tpl modeles=$modeles.$owner}}
	  </div>
  {{/foreach}}
  
{{else}}

  <div class="small-info">
    Veuillez choisir un utilisateur pour consulter ses modèles
  </div>
  
{{/if}}
