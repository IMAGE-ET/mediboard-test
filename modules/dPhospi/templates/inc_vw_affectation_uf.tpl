{{mb_script module=hospi script=affectation_uf}}

<form name="affect_uf" action="?m={{$m}}" method="post" onsubmit="return AffectationUf.onSubmit(this);" style="text-align:left;">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_affectation_aed" />
  
  <input type="hidden" name="affectation_id" value="{{$affectation->_id}}" />
  <input type="hidden" name="del" value="0" />

    <fieldset>
      <legend>{{tr}}CAffectation-uf_hebergement_id{{/tr}}</legend>
       <table class="form">  
				{{foreach from=$hebergement key=nom item=objects   name=lesservices}}
					<tr>
						<th style="width: 20%">{{if $smarty.foreach.lesservices.last}}Lit
                {{elseif  $smarty.foreach.lesservices.first}}Service
                {{else}}Chambre{{/if}}
            </th>
		        <th style="width: 15%;">{{$nom}}</th>
						
		        <td>
		        {{foreach from=$objects item=_object  name=service}}                 
		          {{$_object->_ref_uf->libelle}}{{if !$smarty.foreach.service.last}},{{/if}}
		        {{/foreach}}
		        </td>
					</tr>
				{{/foreach}}
	      <tr>
	        <th colspan="2"></th>
	         <td>
	           <input type="hidden" name="uf_hebergement_id" value="{{$affectation->uf_hebergement_id}}"/>
            {{assign var=found_checked value=0}}
  	        {{foreach from=$choixhebergment item=_choix}}
  	          <input type="radio" name="uf_hebergement_id_radio_view" value="{{$_choix->_ref_uf->_id}}"
                {{if $affectation->uf_hebergement_id == $_choix->_ref_uf->_id}}
                  checked="checked"
                  {{assign var=found_checked value=1}}
                {{/if}}
                onclick="$V(this.form.uf_hebergement_id, this.value); $V(this.form.uf_hebergement_id_view, '')"> {{$_choix->_ref_uf->libelle}}
  	        {{/foreach}}
            &mdash; Autre :
            <input type="text" class="autocomplete" name="uf_hebergement_id_view"
            {{if !$found_checked}}value="{{$affectation->_ref_uf_hebergement->code}}{{/if}}"/>
            
            
            <script type="text/javascript">
              Main.add(function() {
                var form = getForm("affect_uf");
                var url = new Url("system", "httpreq_field_autocomplete");
                url.addParam("class", "CUniteFonctionnelle");
                url.addParam("field", "code");
                url.addParam("limit", 30);
                url.addParam("view_field", "code");
                url.addParam("show_view", true);
                url.addParam("input_field", "uf_hebergement_id_view");
                url.addParam("wholeString", false);
                url.autoComplete(form.uf_hebergement_id_view, null, {
                  minChars: 1,
                  method: "get",
                  select: "view",
                  dropdown: true,
                  afterUpdateElement: function(field,selected){
                    var form = field.form;
                    $V(form.uf_hebergement_id, selected.getAttribute("id").split("-")[2]);
                    if (form.uf_hebergement_id_radio_view.length) {
                      $A(form.uf_hebergement_id_radio_view).each(function(elt) {
                        elt.checked = "";
                      });
                    }
                    else {
                      form.uf_hebergement_id_radio_view.checked = "";
                    }
                  } });
                } );
            </script>
	        </td>
	      </tr>
      </table>
    </fieldset>
    
    <fieldset id="soins"  >
      <legend>{{tr}}CAffectation-uf_soins_id{{/tr}}</legend>
       <table class="form"> 
        <tr>
        	<th style="width: 20%">Service</th>
          <th style="width: 15%">{{$nomservice}}</th>
           <td>
          {{foreach from=$services item=service  name=servicesoin}}                 
            {{$service->_ref_uf->libelle}}{{if !$smarty.foreach.servicesoin.last}},{{/if}}
          {{/foreach}}
          </td>
        </tr>
        <tr>
          <th colspan="2"></th>
           <td>
             <input type="hidden" name="uf_soins_id" value="{{$affectation->uf_soins_id}}"/>
             {{assign var=found_checked value=0}}
             {{foreach from=$choixsoins item=_choix}}
              <input type="radio" name="uf_soins_id_radio_view" value="{{$_choix->_ref_uf->_id}}"
               onclick="$V(this.form.uf_soins_id, this.value); $V(this.form.uf_soins_id_view, '')"
              {{if $affectation->uf_soins_id == $_choix->_ref_uf->uf_id}}
              checked="checked"
              {{assign var=found_checked value=1}}
              {{/if}} > {{$_choix->_ref_uf->libelle}}   
          {{/foreach}}
          &mdash; Autre :
            <input type="text" class="autocomplete" name="uf_soins_id_view"
            {{if !$found_checked}}value="{{$affectation->_ref_uf_soins->code}}{{/if}}"/>
            
            <script type="text/javascript">
              Main.add(function() {
                var form = getForm("affect_uf");
                var url = new Url("system", "httpreq_field_autocomplete");
                url.addParam("class", "CUniteFonctionnelle");
                url.addParam("field", "code");
                url.addParam("limit", 30);
                url.addParam("view_field", "code");
                url.addParam("show_view", true);
                url.addParam("input_field", "uf_soins_id_view");
                url.addParam("wholeString", false);
                url.autoComplete(form.uf_soins_id_view, null, {
                  minChars: 1,
                  method: "get",
                  select: "view",
                  dropdown: true,
                  afterUpdateElement: function(field,selected){
                    var form = field.form;
                    $V(form.uf_soins_id, selected.getAttribute("id").split("-")[2]);
                    if (form.uf_soins_id_radio_view.length) {
                      $A(form.uf_soins_id_radio_view).each(function(elt) {
                        elt.checked = "";
                      });
                    }
                    else {
                      form.uf_soins_id_radio_view.checked = "";
                    }
                  } });
                } );
            </script>
          </td>
        </tr>
      </table>
    </fieldset>
    
    <fieldset >
      <legend>{{tr}}CAffectation-uf_medicale_id{{/tr}}</legend>
       <table class="form"> 
			 {{foreach from=$medical key=nom item=objects name=medicale}}
          <tr>
          	<th style="width: 20%">{{if $smarty.foreach.medicale.last}}Praticien{{else}}Fonction{{/if}}</th>
            <th style="width: 15%">{{$nom}}</th>
            <td>
            {{foreach from=$objects item=_object  name=service}}                 
              {{$_object->_ref_uf->libelle}}{{if !$smarty.foreach.service.last}},{{/if}}
            {{/foreach}}
            </td>
          </tr>
        {{/foreach}}
      <tr>
        <th colspan="2"></th>
         <td>
           {{assign var=found_checked value=0}}
           <input type="hidden" name="uf_medicale_id" value="{{$affectation->uf_medicale_id}}" />
            {{foreach from=$choixmedical item=_choix}}
              <label>
                <input type="radio" name="uf_medicale_id_radio_view" value="{{$_choix->_ref_uf->_id}}"
                onclick="$V(this.form.uf_medicale_id, this.value); $V(this.form.uf_medicale_id_view, '')"
                {{if $affectation->uf_medicale_id == $_choix->_ref_uf->_id}}
                  checked="checked"
                  {{assign var=found_checked value=1}}
                {{/if}}> 
                {{$_choix->_ref_uf->libelle}}
                </label>
            {{/foreach}}
            &mdash; Autre :
            <input type="text" class="autocomplete" name="uf_medicale_id_view"
            {{if !$found_checked}}value="{{$affectation->_ref_uf_medicale->code}}{{/if}}"/>
            
            <script type="text/javascript">
              Main.add(function() {
                var form = getForm("affect_uf");
                var url = new Url("system", "httpreq_field_autocomplete");
                url.addParam("class", "CUniteFonctionnelle");
                url.addParam("field", "code");
                url.addParam("limit", 30);
                url.addParam("view_field", "code");
                url.addParam("show_view", true);
                url.addParam("input_field", "uf_medicale_id_view");
                url.addParam("wholeString", false);
                url.autoComplete(form.uf_medicale_id_view, null, {
                  minChars: 1,
                  method: "get",
                  select: "view",
                  dropdown: true,
                  afterUpdateElement: function(field,selected){
                    var form = field.form;
                    $V(form.uf_medicale_id, selected.getAttribute("id").split("-")[2]);
                    if (form.uf_medicale_id_radio_view.length) {
                      $A(form.uf_medicale_id_radio_view).each(function(elt) {
                        elt.checked = "";
                      });
                    }
                    else {
                      form.uf_medicale_id_radio_view.checked = "";
                    }
                  } });
                } );
            </script>
        </td>
      </tr>
      </table>
    </fieldset>
  <button class="submit" type="submit">{{tr}}Ajouter{{/tr}}</button>
</form>