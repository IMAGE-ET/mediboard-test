{{mb_include_script module="system" script="object_selector"}}

      {{if $can->edit}}
      <form name="editEtablissement" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_etablissement_aed" />
      <input type="hidden" name="facture_id" value="{{$etablissement->_id}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="_class_name" value="CGroups" />
      <table class="form">
        <tr>
          {{if $etablissement->_id}}
          <th class="title modify" colspan="2">
     	 				Modification de l'établissement 
          </th>
          {{else}}
          <th class="title" colspan="2">
      			Création d'un établissement
          </th>
          {{/if}}
        </tr>
        <tr>	
          	<th>{{mb_label object=$etablissement field="group_id"}}</th>
            <td>
            	{{mb_field object=$etablissement field="group_id" hidden=true}}
		            {{if $etablissement->group_id}}
	    	        	<input type="text" size="30" readonly="readonly" ondblclick="ObjectSelector.init()" name="_group_view" value="{{$etablissement->_ref_group->_view|stripslashes}}" />
	    	        {{else}}
	    	        	<input type="text" size="30" readonly="readonly" ondblclick="ObjectSelector.init()" name="_group_view" value="" />
	    	        {{/if}}
	        	  		<button type="button" onclick="ObjectSelector.init()" class="search">Rechercher</button>       	  	
	        	    	<script type="text/javascript">
	                  ObjectSelector.init = function(){
	                    this.sForm     = "editEtablissement";
	                    this.sId       = "group_id";
	                    this.sView     = "_group_view";
	                    this.sClass    = "_class_name";
	                    this.onlyclass = "true";
	                   
	                    this.pop();
	                  } 
	               	</script>
        	 	</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $etablissement->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l'etablissement',objName:'{{$etablissement->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
      {{/if}}
