{{mb_include_script module="system" script="object_selector"}}

<script type="text/javascript">

function pageMain() {
  regFieldCalendar("editAffectation", "debut", true);
  regFieldCalendar("editAffectation", "fin", true);
}

</script>

<table class="main">
<tr>
<td>

<table class="form">
  <tr>
    <td>
     <form name="filterFrm" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="dialog" value="{{$dialog}}" />
    
      <table class="form">
        <tr>
          <th colspan="2" class="title">Recherche sur le personnel</th>
          <th colspan="2" class="title">Recherche sur l'objet</th>
        </tr>
        <tr>
          <th class="category" colspan="10">
            {{if $affectations|@count == 50}}
            Plus de 50 affectations, seules les 50 plus récentes sont affichées
            {{else}}
            {{$affectations|@count}} affectations trouvées
            {{/if}}
          </th>
        </tr>
        
        <tr>
          <th>
            {{mb_label object=$filter field=user_id}}
          </th>
          <td>
            <select name="user_id">
	            <option value="">&mdash; Personnel de bloc</option>
  	          {{foreach from=$personnels item=_personnel}}
    	        <option value="{{$_personnel->_id}}">{{$_personnel->_view}}</option>
              {{/foreach}}
            </select>
          </td>
          <td>
            {{mb_label object=$filter field="object_class"}}
            <select name="object_class" class="str maxLength|25">
            <option value="">&mdash; Toutes les classes</option>
            {{foreach from=$classes item=curr_class}}
            <option value="{{$curr_class}}" {{if $curr_class == $filter->object_class}}selected="selected"{{/if}}>
             {{$curr_class}}
            </option>
            {{/foreach}}
            </select>
          </td>
        </tr>

        <tr>
          <td>
          <td />
          <td>
            {{mb_label object=$filter field="object_id"}}
            {{mb_field object=$filter field="object_id" canNull=true}}
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
           <button class="search" type="submit">{{tr}}Show{{/tr}}</button>
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
          <th colspan="1">Personnel</th>
          <th colspan="3">Objet</th>
        </tr>
        <tr>
          <th>Nom</th>
          <th>Classe</th>
          <th>Id Mediboard</th>
        </tr>
       {{foreach from=$affectations item=_affectation}}
       <tr>
         <td>
           <a href="?m={{$m}}&amp;tab={{$tab}}&amp;affect_id={{$_affectation->_id}}">
             {{$_affectation->_ref_user->_view}}
           </a>
         </td>
         <td>{{$_affectation->object_class}}</td>
         <td>{{$_affectation->object_id}}</td>
       </tr>
       {{/foreach}}
     </table>
   </td>
  </tr>
</table>

</td>
<td>

    <form name="editAffectation" action="index.php?m={{$m}}" method="post">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="del" value="0" />

<table class="form" style="valign: top">
  <tr>		    
    <th class="title" colspan="2">Création d'une affectation</th>
  </tr>
  <tr>  
    <th>{{mb_label object=$affectation field="object_id"}}</th>
    <td>
      <input name="object_id" class="notNull" />
      <button class="search" type="button" onclick="ObjectSelector.initEdit()">{{tr}}Search{{/tr}}</button>
      <script type="text/javascript">
        ObjectSelector.initEdit = function(){
          this.sForm     = "editAffectation";
          this.sId       = "object_id";
          this.sClass    = "object_class";
          this.onlyclass = "false";
          this.pop();
        }
      </script>
      </td>
  </tr>
  <tr>  
    <th>{{mb_label object=$affectation field="object_class"}}</th>
    <td>
      <select name="object_class" class="notNull">
        <option value="">&mdash; Choisir une classe</option>
        {{foreach from=$classes item=curr_class}}
        <option value="{{$curr_class}}" {{if $affectation->_object_class == $curr_class}}selected="selected"{{/if}}>
          {{$curr_class}}
        </option>
        {{/foreach}}
      </select>
    </td>  
  </tr>
  <tr>  
    <th>{{mb_label object=$affectation field="user_id"}}</th>
    <td>
      <input name="user_id" class="notNull" />
      <input type="hidden" name="object_class_CMediusers" value="CMediusers" />
      <button class="search" type="button" onclick="ObjectSelector.initEditUser()">{{tr}}Search{{/tr}}</button>
      <script type="text/javascript">
        ObjectSelector.initEditUser = function() {
          this.sForm     = "editAffectation";
          this.sId       = "user_id";
          this.sClass    = "object_class_CMediusers";
          this.onlyclass = "true";
          this.pop();
        }
      </script>
    </td>
  </tr>
  
  <tr>  
    <th>{{mb_label object=$affectation field="realise"}}</th>
    <td>{{mb_field object=$affectation field="realise"}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$affectation field="debut"}}</th>
    <td class="date">{{mb_field object=$affectation field="debut" form="editAffectation"}}</td>
  </tr>

  <tr>  
    <th>{{mb_label object=$affectation field="fin"}}</th>
    <td class="date">{{mb_field object=$affectation field="fin" form="editAffectation"}}</td>
  </tr>

  <tr>
    <td colspan="2" style="text-align: center">
      <button class="submit" type="submit" name="envoyer">{{tr}}Create{{/tr}}</button>
    </td>
  </tr>
  
</table>
    </form>
</td>
</tr>
</table>
   