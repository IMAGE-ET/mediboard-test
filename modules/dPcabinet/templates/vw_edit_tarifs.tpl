<script type="text/javascript">

var Tarif = {
  add: function(value){
    var oForm = document.editFrm;
    if(oForm.secteur1.value==''){
      oForm.secteur1.value = 0;
    } 
    oForm.secteur1.value = parseFloat(oForm.secteur1.value) + parseFloat(value);
  },
  
  del: function(value){
    var oForm = document.editFrm;
    if(oForm.secteur1.value==''){
      oForm.secteur1.value = 0;
    } 
    oForm.secteur1.value = parseFloat(oForm.secteur1.value) - parseFloat(value);
    Math.round(oForm.secteur1.value*100)/100;
    
  }
}

function refreshTotal() {
  var oForm = document.editFrm;
  var secteur1 = oForm.secteur1.value;
  var secteur2 = oForm.secteur2.value;
  if(secteur1 == ""){
    secteur1 = 0;
  }
  if(secteur2 == ""){
    secteur2 = 0;
  }
  oForm._somme.value = parseFloat(secteur1) + parseFloat(secteur2);
  oForm._somme.value = Math.round(oForm._somme.value*100)/100;
}

function modifSecteur2(){
  var oForm = document.editFrm;
  var secteur1 = oForm.secteur1.value;
  var somme = oForm._somme.value;
  if(somme == ""){
    somme = 0;
  }
  if(secteur1 == ""){
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
      <table class="tbl">
        <tr>
          <th colspan="3" class="title">{{tr}}CMediusers-back-tarifs{{/tr}}</th>
        </tr>
        
        {{if !$user->_is_praticien && !$user->_is_secretaire}}
        <tr>
          <td class="text">
            <div class="big-info">
              N'étant pas praticien, vous n'avez pas accès à la liste de tarifs personnels.
            </div>
          </td>
        </tr>
        {{/if}}
        
        {{if $user->_is_secretaire}}
        <tr>
          <td colspan="3">
            <form action="?" name="selection" method="get">
              <input type="hidden" name="tarif_id" value="" />
              <input type="hidden" name="m" value="{{$m}}" />
              <select name="prat_id" onchange="this.form.submit()">
                <option value="">&mdash; Aucun praticien</option>
                {{foreach from=$listPrat item=_prat}}
                <option class="mediuser" style="border-color: #{{$_prat->_ref_function->color}};" value="{{$_prat->_id}}"
                {{if $_prat->_id == $prat->_id}}selected="selected"{{/if}}>
                  {{$_prat->_view}}
                </option>
                {{/foreach}}
              </select>
            </form>
          </td>
        </tr>
        {{/if}}
        

        {{if $user->_is_praticien || $user->_is_secretaire}}
        <tr>
          <th>{{mb_label class=CTarif field=description}}</th>
          <th>{{mb_label class=CTarif field=secteur1}}</th>
          <th>{{mb_label class=CTarif field=secteur2}}</th>
        </tr>

        {{foreach from=$listeTarifsChir item=_tarif}}
        <tr {{if $_tarif->_id == $tarif->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;tarif_id={{$_tarif->_id}}">
            	{{mb_value object=$_tarif field=description}}
            </a>
          </td>
          <td>{{mb_value object=$_tarif field=secteur1}}</td>
          <td>{{mb_value object=$_tarif field=secteur2}}</td>
        </tr>
        {{/foreach}}
        {{/if}}
      </table>
    
      <table class="tbl">
        <tr><th colspan="3" class="title">{{tr}}CFunctions-back-tarifs{{/tr}}</th></tr>

        <tr>
          <th>{{mb_label class=CTarif field=description}}</th>
          <th>{{mb_label class=CTarif field=secteur1}}</th>
          <th>{{mb_label class=CTarif field=secteur2}}</th>
        </tr>

        {{foreach from=$listeTarifsSpe item=_tarif}}
        <tr {{if $_tarif->_id == $tarif->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;tarif_id={{$_tarif->_id}}">
            	{{mb_value object=$_tarif field=description}}
            </a>
          </td>
          <td>{{mb_value object=$_tarif field=secteur1}}</td>
          <td>{{mb_value object=$_tarif field=secteur2}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    
    <td>
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_tarif_aed" />
      <input type="hidden" name="del" value="0" />
      {{mb_field object=$tarif field="tarif_id" hidden=1 prop=""}}

      <table class="form">
        {{if $tarif->_id}}
        <tr><th class="title modify" colspan="2">{{tr}}CTarif-title-modify{{/tr}} '{{$tarif->_view}}'</th></tr>
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
						{{$_code_ccam}}<br />
			    	{{foreachelse}}
			    	<em>{{tr}}None{{/tr}}</em>
						{{/foreach}}
			    </td>
        </tr>

        <tr>
          <th>{{mb_label object=$tarif field=codes_ngap}}</th>
         <td>
         	{{foreach from=$tarif->_codes_ngap item=_code_ngap}}
					{{$_code_ngap}}<br />
         	{{foreachelse}}
         	<em>{{tr}}None{{/tr}}</em>
					{{/foreach}}
         </td>
        </tr>

        <tr>
          <th>{{mb_label object=$tarif field="secteur1"}}</th>
          <td>{{mb_field object=$tarif field="secteur1" size="6" onChange="refreshTotal();"}}<input type="hidden" name="_tarif" /></td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$tarif field="secteur2"}}</th>
          <td>{{mb_field object=$tarif field="secteur2" size="6" onChange="refreshTotal();"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$tarif field=_somme}}</th>
          <td>
            {{mb_field object=$tarif field="_somme" onchange="modifSecteur2()"}}
          </td>
        </tr>
        
        <tr>
          <td class="button" colspan="2">
            {{if $tarif->_id}}
            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le tarif',objName:'{{$tarif->description|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
            {{else}}
            <button class="submit" type="submit" name="btnFuseAction">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>
        
      </table>
      
      </form>
      
      <div class="big-info">
        Compte-tenu du grand nombre de paramètres possibles pour les cotations NGAP et CCAM,
        il n'est pas possible de manipuler ces codes dans la présente interface.<br />
        Pour créer un tarif contenant des codes CCAM et NGAP, effectuer une cotation réelle
        pendant une consultation en trois étapes :
        <ul>
          <li><em>Ajouter</em> des actes dans le volet <strong>Actes</strong></li>
          <li><em>Valider</em> la cotation la cotation dans le volet <strong>Docs. et Règlements</strong>, section <strong>Règlement</strong></li>
          <li><em>Cliquer</em> <strong>Nouveau tarif</strong> dans cette même section</li>
        </ul>
      </div>
    </td>
  </tr>
</table>