<script type="text/javascript">
Main.add(function(){
  var buttonClasses = $w("edit hslip trash submit new print cancel modify search lookup lock tick down "+
                      "up change add remove stop send send-cancel send-again send-problem send-auto");
                      
  var buttonsContainers = $$("#buttons td");
  buttonClasses.each(function(c){
    buttonsContainers[0].insert('<button class="'+c+' notext">'+c+'</button><br />');
    buttonsContainers[1].insert('<button class="'+c+'">'+c+'</button><br />');
    buttonsContainers[2].insert('<a href="#1" class="button '+c+' notext">'+c+'</a><br />');
    buttonsContainers[3].insert('<a href="#1" class="button '+c+'">'+c+'</a><br />');
  });
  
  var tooltip = $("tooltipTpl").clone(true).show();
  tooltip.addClassName("tooltip").select(".content")[0].update("tooltip");
  
  var postit = $("tooltipTpl").clone(true).show();
  postit.addClassName("postit").setStyle({marginLeft: '100px'}).select(".content")[0].update("postit");
  
  var form = getForm("test");
  Calendar.regField(form.dateTime);
  Calendar.regField(form.time);
  Calendar.regField(form.date);
  Calendar.regField(form.dateInline, null, {inline: true, container: $(form.dateInline).up(), noView: true});
  
  $("tooltip-container").insert(tooltip).insert(postit);
});
</script>

<button class="change" onclick="$$('body')[0].toggleClassName('touchscreen')">Touchscreen</button>

<h1>header 1</h1>
<h2>header 2</h2>
<h3>header 3</h3>

<hr />

<ul class="control_tabs">
  <li><a href="#tab1">normal</a></li>
  <li><a href="#tab2" class="active">active</a></li>
  <li><a href="#tab3" class="empty">empty</a></li>
  <li><a href="#tab3" class="empty active">empty active</a></li>
  <li><a href="#tab4" class="wrong">wrong</a></li>
  <li><a href="#tab4" class="wrong active">wrong active</a></li>
</ul>
<hr class="control_tabs"/>

<table class="main">
  <tr>
    <td style="width: 0.1%;">

<ul class="control_tabs_vertical">
  <li><a href="#tab1">normal</a></li>
  <li><a href="#tab2" class="active">active</a></li>
  <li><a href="#tab3" class="empty">empty</a></li>
  <li><a href="#tab3" class="empty active">empty active</a></li>
  <li><a href="#tab4" class="wrong">wrong</a></li>
  <li><a href="#tab4" class="wrong active">wrong active</a></li>
</ul>

    </td>
    <td style="width: 0.1%;">
      
<table style="width: 0.1%;" id="buttons">
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>

    </td>
    <td>
      
<table class="tbl">
  <tr>
    <th class="title" colspan="4">Title 1</th>
  </tr>
  <tr>
    <th>Title 1</th>
    <th>Title 2</th>
    <th>Title 3</th>
    <th>Title 4</th>
  </tr>
  <tr >
    <td></td>
    <td class="ok">ok</td>
    <td class="warning">warning</td>
    <td class="error">error</td>
  </tr>
  <tr>
    <td>Cell 1 - 1</td>
    <td>Cell 1 - 2</td>
    <td>Cell 1 - 3</td>
    <td>Cell 1 - 4</td>
  </tr>
  <tr>
    <td>Cell 2 - 1</td>
    <td colspan="2">Cell 2 - 2-3</td>
    <td>Cell 2 - 4</td>
  </tr>
  <tr>
    <td colspan="4" class="text">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. Cras elementum ultrices diam. Maecenas ligula massa, varius a, semper congue, euismod non, mi. Proin porttitor, orci nec nonummy molestie, enim est eleifend mi, non fermentum diam nisl sit amet erat. Duis semper. Duis arcu massa, scelerisque vitae, consequat in, pretium a, enim. Pellentesque congue. Ut in risus volutpat libero pharetra tempor. Cras vestibulum bibendum augue. Praesent egestas leo in pede.</td>
  </tr>
  <tr>
    <td>Cell 4 - 1</td>
    <td>Cell 4 - 2</td>
    <td>Cell 4 - 3</td>
    <td>Cell 4 - 4</td>
  </tr>
</table>

<br />

<form action="?" name="test" method="post" onsubmit="return false">
<table class="form">
  <tr>
    <th class="title" colspan="4">Title 1</th>
  </tr>
  <tr>
    <th class="category" colspan="2">Category 1</th>
    <th class="category" colspan="2">Category 2</th>
  </tr>
  <tr>
    <th>
      <label class="notNull">Title 1</label>
    </th>
    <td>
      <input type="text" value="text" /><br />
      <input type="text" value="text" class="autocomplete" /><br />
      <input type="password" value="password" />
    </td>
    <th rowspan="2">
      <label>Title 2</label>
    </th>
    <td rowspan="2">
      <input type="hidden" class="date" name="dateInline" />
    </td>
  </tr>
  <tr>
    <th>
      <label class="canNull">Title 3</label>
    </th>
    <td>
      <textarea></textarea>
    </td>
  </tr>
  <tr>
    <th>
      <label class="notNullOK">Title 5</label>
    </th>
    <td>
      <select>
        <option>Option 1</option>
        <option value="1">Option 2</option>
        <option value="2">Option 3</option>
        <optgroup label="Optgroup 1">
          <option value="3">Option 4</option>
          <option value="4">Option 5</option>
        </optgroup>
      </select>
    </td>
    <th>
      <label>Title 6</label>
    </th>
    <td>
      <input type="file" />
    </td>
  </tr>
  <tr>
    <th>
      <label>Title 7</label>
    </th>
    <td>
      <input type="hidden" class="dateTime" name="dateTime" value="{{$smarty.now|@date_format:"%Y-%m-%d %H:%M:%S"}}" />
    </td>
    <th>
      <label>Title 8</label>
    </th>
    <td>
      <input type="hidden" class="time" name="time" value="{{$smarty.now|@date_format:"%H:%M:%S"}}" />
    </td>
  </tr>
  <tr>
    <th>
      <label>Title 7</label>
    </th>
    <td>
      <input type="hidden" class="date" name="date" value="{{$smarty.now|@date_format:"%Y-%m-%d"}}" />
    </td>
    <th>
      <label>Title 8</label>
    </th>
    <td>
      <input type="checkbox" /> 1
      <input type="checkbox" /> 2
      <br />
      <input type="radio" /> 1
      <input type="radio" /> 2
    </td>
  </tr>
  <tr>
    <td colspan="10">
      <button class="tick">button</button>
      <a class="button tick">a.button</a>
      <input type="checkbox" />
      <input type="radio" />
      <input type="text" />
      <select>
        <option>select</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="4">
      <button class="submit">{{tr}}Save{{/tr}}</button>
      <button class="trash">{{tr}}Remove{{/tr}}</button>
    </td>
  </tr>
</table>
</form>

    </td>
    <td>

<div class="small-error">small-error</div>
<div class="small-warning">small-warning</div>
<div class="small-info">small-info</div>
<div class="small-success">small-success</div>

<div class="big-error">big-error</div>
<div class="big-warning">big-warning</div>
<div class="big-info">big-info</div>
<div class="big-success">big-success</div>

<div class="error">error</div>
<div class="warning">warning</div>
<div class="message">message</div>
<div class="loading">loading</div>

    </td>
  </tr>
</table>

<div id="tooltip-container"></div>