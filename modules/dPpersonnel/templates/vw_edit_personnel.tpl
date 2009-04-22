{{mb_include_script module="system" script="object_selector"}}

<table class="main">
  <tr>
    <td>
	  <a href="?m={{$m}}&amp;tab={{$tab}}&amp;personnel_id=0" class="button new">
		Créer un personnel
	  </a>
	  <table class="form">
        <tr>
          <td>
            <form name="filterFrm" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">
              <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="tab" value="{{$tab}}" />
              <input type="hidden" name="dialog" value="{{$dialog}}" />
              <table class="form">
                <tr>
                  <th colspan="4" class="title">Recherche d'un membre du personnel</th>
                </tr>
                <tr>
                  <th>{{mb_label object=$filter field="_user_last_name"}}</th>
                  <td>{{mb_field object=$filter field="_user_last_name"}}</td>
                  <th>{{mb_label object=$filter field="_user_first_name"}}</th>
                  <td>{{mb_field object=$filter field="_user_first_name"}}</td>
                </tr>
                <tr>
                  <th>{{mb_label object=$filter field="emplacement"}}</th>
                  <td>{{mb_field object=$filter defaultOption="&mdash; Tous" canNull=true field="emplacement"}}</td>
                </tr>
                <tr>
                  <td class="button" colspan="6">
                    <button class="search" type="submit">{{tr}}Show{{/tr}}</button>
                  </td>
                </tr>
              </table>
            </form>
          </td>
        </tr> 
       </table> 
       <table class="tbl">
          <tr>
	        <th colspan="3">Liste du personnel</th>
		  </tr>
		<tr>
		  <th>Nom</th>
		  <th colspan="2">Emplacement</th>
		</tr>
		{{foreach from=$personnels item=_personnel}}
		<tr {{if $_personnel->_id == $personnel->_id}}class="selected"{{/if}}>
		  <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;personnel_id={{$_personnel->_id}}">{{$_personnel->_ref_user->_view}}</a></td>
			
			{{if $_personnel->actif}}
		  <td colspan="2">{{tr}}CPersonnel.emplacement.{{$_personnel->emplacement}}{{/tr}}</td>
			{{else}}
		  <td>{{tr}}CPersonnel.emplacement.{{$_personnel->emplacement}}{{/tr}}</td>
		  <td class="cancelled">INACTIF</td>
			{{/if}}
		</tr>
		{{/foreach}}
	  </table>
	</td>

	
	<td>
	  <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_personnel_aed" />
		<input type="hidden" name="personnel_id" value="{{$personnel->_id}}" />
		<input type="hidden" name="del" value="0" />

		<table class="form">
		  <tr>
		  {{if $personnel->_id}}
		    <th class="title modify" colspan="2">
		      <div class="idsante400" id="{{$personnel->_class_name}}-{{$personnel->_id}}"></div>
		      <a style="float:right;" href="#nothing" onclick="view_log('{{$personnel->_class_name}}',{{$personnel->_id}})">
		      <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
		      </a>
		      Modification du personnel &lsquo;{{$personnel->_view}}&rsquo;
		    </th>
		    {{else}}
		    <th class="title" colspan="2">
		      Création d'un personnel
		    </th>
		    {{/if}}
		  </tr>
		  
		  <tr>
		    <th>{{mb_label object=$personnel field="user_id"}}</th>
        <td>
          <input type="text" name="user_id" class="notNull" value="{{$personnel->user_id}}"/>
          <input type="hidden" name="object_class" value="CMediusers" />
          <button class="search" type="button" onclick="ObjectSelector.initEdit()">Chercher</button>
          <script type="text/javascript">
           ObjectSelector.initEdit = function(){
              this.sForm     = "editFrm";
              this.sId       = "user_id";
              this.sClass    = "object_class";  
              this.onlyclass = "true";
              this.pop();
            }
          </script>
        </td>
		  </tr>
		  
		  <tr>
		    <th>{{mb_label object=$personnel field="emplacement"}}</th>
		    <td>{{mb_field object=$personnel field="emplacement"}}</td>
		  </tr>
		  
		  <tr>
		    <th>{{mb_label object=$personnel field="actif"}}</th>
		    <td>{{mb_field object=$personnel field="actif"}}</td>
		  </tr>
		         
		  <tr>
		    <td class="button" colspan="2">
		      {{if $personnel->_id}}
		      <button class="modify" type="submit">Valider</button>
		      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le personnel ',objName:'{{$personnel->_view|smarty:nodefaults|JSAttribute}}'})">
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