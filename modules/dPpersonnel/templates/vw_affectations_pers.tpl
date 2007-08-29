{{mb_include_script module="system" script="object_selector"}}


<table class="main">
  <tr>
    <td>
     <form name="filterFrm" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="dialog" value="{{$dialog}}" />
    
      <table class="form">
        <tr>
          <th colspan="3" class="title">Recherche sur le personnel</th>
          <th colspan="3" class="title">Recherche sur l'objet</th>
        </tr>
        <tr>
          <th class="category" colspan="6">
            {{if $affectations|@count == 50}}
            Plus de 50 identifiants, seuls les 50 plus récents sont affichés
            {{else}}
            {{$affectations|@count}} identifiants trouvés
            {{/if}}
          </th>
        </tr>
        
        <tr>
          <td colspan="3">
            Personne affectée
            <input name="user_id" value="{{$filter->user_id}}" />
            <input type="hidden" name="_class_mediusers" value="CMediusers" />
            <button class="search" type="button" onclick="ObjectSelector.initFilter_()">Chercher</button>
            <script type="text/javascript">
              ObjectSelector.initFilter_ = function(){
               this.sForm     = "filterFrm";
               this.sId       = "user_id";
               this.sClass    = "_class_mediusers";  
               this.onlyclass = "true";
               this.pop();
              }
            </script>
          </td>
          
          <td>
            {{mb_label object=$filter field="object_class"}}
            <select name="object_class" class="str maxLength|25">
            <option value="">&mdash; Toutes les classes</option>
            {{foreach from=$listClasses|smarty:nodefaults item=curr_class}}
            <option value="{{$curr_class}}" {{if $curr_class == $filter->object_class}}selected="selected"{{/if}}>
             {{$curr_class}}
            </option>
            {{/foreach}}
            </select>
          

          {{mb_label object=$filter field="object_id"}}
                      <input name="object_id" class="ref" value="{{$filter->object_id}}" />
            <button class="search" type="button" onclick="ObjectSelector.initFilter()">Chercher</button>
            <script type="text/javascript">
              ObjectSelector.initFilter = function(){
               this.sForm     = "filterFrm";
               this.sId       = "object_id";
               this.sClass    = "object_class";  
               this.onlyclass = "false";
               this.pop();
              }
            </script>
         </td>
         
         
       </tr>
       <tr>
         <td class="button" colspan="6">
           <button class="search" type="submit">Afficher</button>
        </td>
      </tr>
    </table>
   </form>
  </td>
  </tr>
  
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th colspan="2">Personnel</th>
          <th colspan="3">Objet</th>
        </tr>
        <tr>
          <th>Id Mediboard</th>
          <th>Nom</th>
          <th>Classe</th>
          <th>Id Mediboard</th>
        </tr>
       {{foreach from=$affectations item=_affectation}}
       <tr>
         <td>{{$_affectation.user->user_id}}</td>
         <td>{{$_affectation.user->_view}}</td>
         <td>{{$_affectation.object->object_class}}</td>
         <td>{{$_affectation.object->object_id}}</td>
       </tr>
       {{/foreach}}
     </table>
   </td>
 </tr>
</table>
