<script type="text/javascript">

function refreshTotal() {
  var oForm = document.editFrm;
  if (!oForm.secteur1 || !oForm.secteur1) {
  	return;
  }
  
  
  var secteur1 = oForm.secteur1.value;
  var secteur2 = oForm.secteur2.value; 
  
  if (secteur1 == ""){
    secteur1 = 0;
  }
  
  if (secteur2 == ""){
    secteur2 = 0;
  }
  
  oForm._somme.value = parseFloat(secteur1) + parseFloat(secteur2);
  oForm._somme.value = Math.round(oForm._somme.value*100)/100;
}

function modifSecteur2(){
  var oForm = document.editFrm;
  var secteur1 = oForm.secteur1.value;
  var somme = oForm._somme.value;
  if (somme == "") {
    somme = 0;
  }
  if (secteur1 == "") {
    secteur = 0;
  }
  oForm.secteur2.value = parseFloat(somme) - parseFloat(secteur1); 
  oForm.secteur2.value = Math.round(oForm.secteur2.value*100)/100;
}

Main.add(function () {
  refreshTotal();
});

</script>

<table class="main">
  <tr>
    <td colspan="2" class="halfPane">
      <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;tarif_id=0">
      	{{tr}}CTarif-title-create{{/tr}}
      </a>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      {{include file=inc_list_tarifs.tpl}}
    </td>
    
    <td>
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_tarif_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_field object=$tarif field="tarif_id" hidden=1 prop=""}}

      <table class="form">
        {{if $tarif->_id}}
        <tr>
        	<th class="title modify text" colspan="2">
        		{{mb_include  module=system template=inc_object_history object=$tarif}}
        		{{mb_include  module=system template=inc_object_idsante400 object=$tarif}}
        		{{tr}}CTarif-title-modify{{/tr}} '{{$tarif}}'
        	</th>
        </tr>
        {{else}}
        <tr><th class="title" colspan="2">{{tr}}CTarif-title-create{{/tr}}</th></tr>
        {{/if}}
        <tr>
          <th>{{mb_label object=$tarif field="_type"}}</th>
          <td>
            {{if $user->_is_praticien || ($user->_is_secretaire && $tarif->_id)}}
			      {{mb_field object=$prat field="function_id" hidden=1 prop=""}}
			      <input type="hidden" name="chir_id" value="{{$prat->user_id}}" />
            <select name="_type">
              <option value="chir" {{if $tarif->chir_id}} selected="selected" {{/if}}>Tarif personnel</option>
              <option value="function" {{if $tarif->function_id}} selected="selected" {{/if}}>Tarif de cabinet</option>
            </select>
            
            {{else}}
			      <input  type="hidden" name="function_id" value="" />
            <select name="chir_id">
              <option value="">&mdash; Choisir un praticien</option>
              {{foreach from=$listPrat item=_prat}}
              <option class="mediuser" style="border-color: #{{$_prat->_ref_function->color}};" value="{{$_prat->_id}}"
                {{if $_prat->_id == $prat->_id}}selected="selected"{{/if}}>
                {{$_prat->_view}}
              </option>
              {{/foreach}}
            </select>
            {{/if}}
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$tarif field="description"}}</th>
          <td>{{mb_field object=$tarif field="description"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$tarif field=codes_ccam}}</th>
			    <td>
			    	{{foreach from=$tarif->_codes_ccam item=_code_ccam}}
						<div onmouseover="ObjectTooltip.createDOM(this, 'DetailCCAM-{{$_code_ccam}}');">{{$_code_ccam}}</div>
						<div id="DetailCCAM-{{$_code_ccam}}" style="display: none">
							{{mb_include module=system template=CMbObject_view object=$tarif->_new_actes.$_code_ccam show_derived=1}}
						</div>
			    	{{foreachelse}}
			    	<em>{{tr}}None{{/tr}}</em>
						{{/foreach}}
			    </td>
        </tr>

        <tr>
          <th>{{mb_label object=$tarif field=codes_ngap}}</th>
          <td>
         	  {{foreach from=$tarif->_codes_ngap item=_code_ngap}}
					  <div onmouseover="ObjectTooltip.createDOM(this, 'DetailNGAP-{{$_code_ngap}}');">{{$_code_ngap}}</div>
					  <div id="DetailNGAP-{{$_code_ngap}}" style="display: none">
				 	    {{mb_include module=system template=CMbObject_view object=$tarif->_new_actes.$_code_ngap show_derived=1}}
			 		  </div>
          	{{foreachelse}}
            <em>{{tr}}None{{/tr}}</em>
	 				{{/foreach}}
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$tarif field=secteur1}}</th>
          <td>
            {{if count($tarif->_new_actes)}}
	          	{{mb_value object=$tarif field=secteur1}}
						{{else}}
	          	{{mb_field object=$tarif field=secteur1 size=6 onchange="refreshTotal();"}}
	          	<input type="hidden" name="_tarif" />
						{{/if}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$tarif field=secteur2}}</th>
          <td>
            {{if count($tarif->_new_actes)}}
	          	{{mb_value object=$tarif field=secteur2}}
						{{else}}
	          	{{mb_field object=$tarif field=secteur2 size=6 onchange="refreshTotal();"}}
						{{/if}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$tarif field=_somme}}</th>
          <td>
            {{if count($tarif->_new_actes)}}
	          	{{mb_value object=$tarif field=_somme}}
						{{else}}
	            {{mb_field object=$tarif field=_somme onchange="modifSecteur2()"}}
						{{/if}}
          
          </td>
        </tr>
        
        <tr>
          <td class="button" colspan="2">
            {{if $tarif->_id}}
	            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
              {{if count($tarif->_new_actes) && !$tarif->_has_mto}}
              <input type="hidden" name="_add_mto" value="0" />
	            <button class="add" type="submit" onclick="$V(this.form._add_mto, '1');">{{tr}}Add{{/tr}} MTO</button>
              {{/if}}
	            <button class="trash" type="button" onclick="confirmDeletion(this.form, { typeName: 'le tarif',objName: this.form.description.value } )">{{tr}}Delete{{/tr}}</button>
            {{else}}
            <button class="new" type="submit" name="btnFuseAction">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>
        
      </table>
      
      </form>
      
      {{if $tarif->_id}}
	      {{if $tarif->_precode_ready}}
	      <div class="small-success">
	      	Les codages CCAM et NGAP de ce tarif sont suffisamment complets pour permettre une cotation réelle automatique. 
	      	complète automatiquement
	      </div>
	      {{else}}
	      <div class="small-warning">
	      	Les codages CCAM et NGAP de ce tarif ne sont pas assez complets pour permettre une cotation complète automatiquement.
	      </div>
				{{/if}}
			{{/if}}
			      
      <div class="big-info">
        Pour créer un tarif contenant des codes CCAM et NGAP, effectuer une cotation réelle
        pendant une consultation en trois étapes :
        <ul>
          <li><em>Ajouter</em> des actes dans le volet <strong>Actes</strong></li>
          <li><em>Valider</em> la cotation dans le volet <strong>Docs. et Règlements</strong></li>
          <li><em>Cliquer</em> <strong>Nouveau tarif</strong> dans cette même section</li>
        </ul>
      </div>
    </td>
  </tr>
</table>