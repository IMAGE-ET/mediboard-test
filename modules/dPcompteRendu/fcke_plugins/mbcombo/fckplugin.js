/*function clickListener(e) {
  if ( e.target.tagName == 'SPAN') {
    for (var i = 0; i < aMbCombos.length; i++) {
      if (e.target.className == aMbCombos[i].spanClass) {
      FCKSelection.SelectNode( e.target );
      }
    }
  }  
}

if (!FCKBrowserInfo.IsIE && bAutoSelectSpans) {
  FCK.EditorDocument.addEventListener('click', clickListener, true ) ;
}*/

for (var i = 0; i < aMbCombos.length; i++) {

  var oMbCombo = aMbCombos[i];
  
  // Defines command class
  var FCKMbComboCommand = function() {
    this.Name = oMbCombo.commandName;
    this.spanClass = oMbCombo.spanClass;
  }
  FCKMbComboCommand.prototype.Execute = function(itemId, item) {
    FCK.Focus();
    FCK.InsertHtml("<span class=' " + this.spanClass + "'>" + itemId + "</span>&nbsp;");
  }
  
  FCKMbComboCommand.prototype.GetState = function() {
    return FCK_TRISTATE_OFF ;
  }
  
  
  //Registers command object
  var oCommand = new FCKMbComboCommand();
  FCKCommands.RegisterCommand(oCommand.Name, oCommand);
  
  
  //Defines toolbar item class
  var FCKToolbarMbCombo = function() {
    this.Command =  FCKCommands.GetCommand(oMbCombo.commandName);
    this.options = oMbCombo.options;
    this.CommandName  = oMbCombo.commandName;
    this.CommandLabel = oMbCombo.commandLabel;

    // Format combo way
    this.Style = FCK_TOOLBARITEM_ICONTEXT ;
    this.PanelWidth = 300 ;
    this.PanelMaxHeight = 300 ;
  }
  //Inherit from FCKToolbarSpecialCombo.
  FCKToolbarMbCombo.prototype = new FCKToolbarSpecialCombo;
  FCKToolbarMbCombo.prototype.GetLabel = function() {
    return this.CommandLabel;
  }
  
  FCKToolbarMbCombo.prototype.CreateItems = function( targetSpecialCombo ) {
    for (var i = 0; i < this.options.length; i++) {
      var oOption = this.options[i];
      this._Combo.AddItem(oOption.item, "<span style='font-size: 10px'>" + oOption.view + "</span>", oOption.shortview);
    }
  }

  // Registers toolbar item object
  FCKToolbarItems.RegisterItem( oMbCombo.commandName, new FCKToolbarMbCombo) ;
}