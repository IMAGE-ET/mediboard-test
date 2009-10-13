{{mb_include_script module=dPcompteRendu script=aideSaisie}}

<script type="text/javascript">
var form;
Main.add(function () {
  form = getForm("test");
  /*$('dom-creator').insert(
    DOM.div({className: 'small-info'}, 
      DOM.a({href: 'http://www.mozilla-europe.org', target: '_blank'}, 
        'Cette info est générée par le DOM creator !'
      )
    )
  );*/
  
  form.elements.sorted.sortByLabel();
  
  var tabs = Control.Tabs.create('tab_categories', true);
  
  Calendar.regProgressiveField(form.progressive, {container: document.body});
  
  new AideSaisie.AutoComplete(form.name, "rques", "type", "appareil", "rques", "CAntecedent", 25);
});

</script>

{{if !$dialog}}
<a href="?m={{$m}}&amp;a={{$tab}}&amp;dialog=1">Lancer cette page sans les menus</a>
{{else}}
<a href="?m={{$m}}&amp;tab={{$a}}">Lancer cette page avec les menus</a>
{{/if}}

<button class="cancel">Annuler</button> <a class="button cancel" href="#1">Annuler</a>
<button class="cancel notext">Annuler</button> <a class="button cancel notext" href="#1">Annuler</a>

<div id="dom-creator"></div>

<div id="modal" style="display: none;">test blah blah</div>

<button type="button" onclick="modal($('modal'))">Modal window</button>
<button type="button" onclick="Modal.alert($('dom-creator'), {okLabel: 'Close', onValidate: function(){console.debug('Ok')} })">Alert !</button>
<button type="button" onclick="Modal.confirm($('dom-creator'), {onValidate: function(v){console.debug('Vous avez dit '+(v?'oui :)':'non :('))} })">Confirm ?</button>
<a href="http://www.google.fr" onclick="return window.open(this)">Popup</a>

<form name="test" action="?" method="get" onsubmit="if (checkForm(this)) {Console.trace('form.submit()');} return false;" id="form-test-id">
  <textarea name="rques"></textarea>
  
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="progressive" value="2009-0-0" />
  
  <table class="form">
  {{foreach from=$specs item=class key=spec}}
    <tr>
      <th>{{mb_title object=$object field=$spec}}</th>
      <td>{{mb_field object=$object field=$spec form=test register=1 increment=1}}</td>
    </tr>
  {{/foreach}}
  </table>
  
  <table class="form">
  {{foreach from=$specs item=class key=spec}}
    <tr>
      <th>{{mb_title object=$object field=$spec}}</th>
      <td>{{mb_value object=$object field=$spec}}</td>
    </tr>
  {{/foreach}}
  </table>
  
  <table>
    <tr>
      <td>
        <select name="select_tree_1" class="select-tree" onchange="Console.debug(this.value+':'+this.options[this.selectedIndex].text);">
          <option value="1">Thomas</option>
          <option value="2" >Romain</option>
          <option value="11" >123</option>
          <optgroup label="Salariés">
            <option value="3" >Alexis</option>
            <option value="4" >Fabien</option>
          </optgroup>
          <optgroup label="Stagiaires">
            <option value="8"  selected="selected">Alexandre</option>
            <option value="9" ></option>
            <option value="10" >encore un</option>
          </optgroup>
        </select>
        
        <select id="sorted">
          <option value="c">&mdash;</option>
          <option value="c">c</option>
          <option value="e">e</option>
          <option value="b">b</option>
          <option value="a">a</option>
          <option value="d">d</option>
          <option value="f">f</option>
        </select>
      </td>
    </tr>
    <tr>
      <td><label for="text_1">Champ masqué</label><input type="text" name="text_1" class="mask|+(99)S99S99S99P99P notNull"/></td>
    </tr>
    <tr>
      <td>
        <label for="user_username">Login</label><input type="text" name="user_username" value="fabien" class="str" /><br />
        <label for="text_2">Mot de passe</label><input type="password" name="text_2" value="123456789" 
               class="password minLength|6 notContaining|user_username notNear|user_username alphaAndNum"
               onkeyup="checkFormElement(this)" />
               <div id="text_2_message"></div>
        <button type="button" name="button_1" onclick="Console.debug($(this.form.text_2).caret(3, 6, 'toto'))">Caret</button>
      </td>
    </tr>
    <tr>
      <td><button type="submit" name="submit_1">OK</button></td>
    </tr>
  </table>
</form>


<div>
  
<ul id="tab_categories" class="control_tabs_vertical" style="margin-top: 2em;">
  <li><a href="#cat1">cat 1</a></li>
  <li><a href="#cat2">cat 2</a></li>
</ul>

<table class="tbl" style="width: auto">
  <tr>
    <th style="width: 50%;">test1</th>
    <th style="width: 50%;">test2</th>
  </tr>
  <tbody id="cat1" style="display: none;">
    <tr>
      <td>test1</td>
      <td>test2</td>
    </tr>
    <tr>
      <td>test11</td>
      <td>test22</td>
    </tr>
  </tbody>
  <tbody id="cat2" style="display: none;">
    <tr>
      <td>tdf hj4h</td>
      <td>tesertyertyt2</td>
    </tr>
    <tr>
      <td>hj fh4jgj</td>
      <td>teetyerty</td>
    </tr>
    <tr>
      <td>ghfgvfg</td>
      <td>vhfghfgjnkg45kg45kkfh k4fhk54hg</td>
    </tr>
    <tr>
      <td>esfdgdfgdhcd</td>
      <td>hdfghgfhd</td>
    </tr>
  </tbody>
</table>

</div>