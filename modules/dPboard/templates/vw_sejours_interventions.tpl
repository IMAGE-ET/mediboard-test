{{mb_include_script module="dPplanningOp" script="ccam_selector"}}

<table class="main">
	{{if $prat->_id}}
  <tr>
    <td>
      <form name="filters" action="?" method="get" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="dPboard" />

      <table class="form">
      
        <tr>
          <th colspan="4" class="category">Statistiques cliniques</th>
        </tr>

        <tr>
          <td>{{mb_label object=$filterSejour field="_date_min_stat"}}</td>
          <td class="date">{{mb_field object=$filterSejour field="_date_min_stat" form="filters" register=true canNull="false"}} </td>
          <td>{{mb_label object=$filterSejour field="_date_max_stat"}}</td>
          <td class="date">{{mb_field object=$filterSejour field="_date_max_stat" form="filters" register=true canNull="false"}} </td>
        </tr>

        <tr>
          <td>{{mb_label object=$filterSejour field="type"}}</td>
          <td>
            <select name="type">
              <option value="">&mdash; Tous les types d'hospi</option>
              <option value="1" {{if $filterSejour->type == "1"}}selected="selected"{{/if}}>Hospi complètes + ambu</option>
              {{foreach from=$filterSejour->_specs.type->_locales key=key_hospi item=curr_hospi}}
              <option value="{{$key_hospi}}" {{if $key_hospi == $filterSejour->type}}selected="selected"{{/if}}>
                {{$curr_hospi}}
              </option>
              {{/foreach}}
            </select>
          </td>
          <td>{{mb_label object=$filterOperation field="codes_ccam"}}</td>
          <td>
            {{mb_field object=$filterOperation field="codes_ccam" canNull="true" size="20"}}
            <button class="search" type="button" onclick="CCAMSelector.init()">Rechercher</button>   
            <script type="text/javascript">
              CCAMSelector.init = function(){
                this.sForm = "filters";
                this.sView = "codes_ccam";
                this.sChir = "_chir";
                this.sClass = "_class_name";
                this.pop();
              }
            </script>
            
          </td>
        </tr>

        <tr>
          <td colspan="4" class="button"><button type="submit" class="search">Afficher</button></td>
        </tr>

        <tr>
          <td colspan="4" class="button">
            <img alt="Admissions par type d'hospitalisation" src='?m=dPstats&amp;a=graph_patpartypehospi&amp;suppressHeaders=1&amp;debut={{$filterSejour->_date_min_stat}}&amp;fin={{$filterSejour->_date_max_stat}}&amp;prat_id={{$filterSejour->praticien_id}}&amp;type_adm={{$filterSejour->type}}' />
            <img alt="Nombre d'interventions" src='?m=dPstats&amp;a=graph_activite&amp;suppressHeaders=1&amp;debut={{$filterSejour->_date_min_stat}}&amp;fin={{$filterSejour->_date_max_stat}}&amp;prat_id={{$filterSejour->praticien_id}}&amp;codes_ccam={{$filterOperation->codes_ccam}}' />
          </td>
        </tr>
        
      </table>
      
      </form>
    </td>
  </tr>
  {{/if}}
</table>