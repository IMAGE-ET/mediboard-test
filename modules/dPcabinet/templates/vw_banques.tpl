<table class="form">
  <tr>
    <td class="halfPane">
			<a href="?m={{$m}}&amp;tab={{$tab}}&amp;banque_id=0" class="button new">
			  Créer une banque
			</a>
			
			 <table class="tbl">
			 <tr>
			   <th colspan="3" class="title">Liste des banques</th>
			 </tr>
			 <tr>
			   <th class="category">Nom</th>
			   <th class="category">Description</th>
			 </tr>
			  {{foreach from=$banques item=_banque}}
			 <tr {{if $_banque->_id == $banque->_id}}class="selected"{{/if}}>
			   <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;banque_id={{$_banque->_id}}">{{$_banque->nom}}</a></td>
			   <td class="text">{{$_banque->description|nl2br}}</td>
			 </tr>
			 {{/foreach}}
			 </table>
 		 </td> 
		 <td class="halfPane">
   <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
   <input type="hidden" name="dosql" value="do_banque_aed" />
   <input type="hidden" name="banque_id" value="{{$banque->_id}}" />
   <input type="hidden" name="del" value="0" />
   <table class="form">
   <tr>
     {{if $banque->_id}}
     <th class="title modify" colspan="2">
       <div class="idsante400" id="{{$banque->_class_name}}-{{$banque->_id}}"></div>
       <a style="float:right;" href="#nothing" onclick="view_log('{{$banque->_class_name}}',{{$banque->_id}})">
       <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
       </a>
       Modification de la banque &lsquo;{{$banque->nom}}&rsquo;
     </th>
     {{else}}
     <th class="title" colspan="2">
       Création d'une banque
     </th>
     {{/if}}
   </tr>
   <tr>
     <th>{{mb_label object=$banque field="nom"}}</th>
     <td>{{mb_field object=$banque field="nom"}}</td>
   </tr>       
   <tr>
     <th>{{mb_label object=$banque field="description"}}</th>
     <td>{{mb_field object=$banque field="description"}}</td>
   </tr>    
   <tr>
     <td class="button" colspan="2">
       {{if $banque->_id}}
       <button class="modify" type="submit">Valider</button>
       <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la banque ',objName:'{{$banque->nom|smarty:nodefaults|JSAttribute}}'})">
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







