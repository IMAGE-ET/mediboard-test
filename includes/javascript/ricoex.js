Class.extend(Rico.Accordion, {
  setOptions: function(options) {
      this.options = {
         expandedBg          : '#63699c',
         hoverBg             : '#63699c',
         collapsedBg         : '#6b79a5',
         expandedTextColor   : '#ffffff',
         expandedFontWeight  : 'bold',
         hoverTextColor      : '#ffffff',
         collapsedTextColor  : '#ced7ef',
         collapsedFontWeight : 'normal',
         hoverTextColor      : '#ffffff',
         borderColor         : '#1f669b',
         panelHeight         : 200,
         showDelay           : 100,
         showSteps           : 10,
         onHideTab           : null,
         onShowTab           : null,
         onLoadShowTab       : 0
      }
      Object.extend(this.options, options || {});
   },
   
   showTab: function( accordionTab, animate ) {
     if ( this.lastExpandedTab == accordionTab )
        return;

      var doAnimate = arguments.length == 1 ? true : animate;

      if ( this.options.onHideTab )
         this.options.onHideTab(this.lastExpandedTab);

      this.lastExpandedTab.showCollapsed(); 
      var accordion = this;
      var lastExpandedTab = this.lastExpandedTab;

      this.lastExpandedTab.content.style.height = (this.options.panelHeight - 1) + 'px';
      accordionTab.content.style.display = '';

      accordionTab.titleBar.style.fontWeight = this.options.expandedFontWeight;

      if ( doAnimate ) {
         new Rico.Effect.AccordionSize( this.lastExpandedTab.content,
                                   accordionTab.content,
                                   1,
                                   this.options.panelHeight,
                                   this.options.showDelay,
                                   this.options.showSteps,
                                   { complete: function() {accordion.showTabDone(lastExpandedTab)} } );
         this.lastExpandedTab = accordionTab;
      }
      else {
         this.lastExpandedTab.content.style.height = "1px";
         accordionTab.content.style.height = this.options.panelHeight + "px";
         this.lastExpandedTab = accordionTab;
         this.showTabDone(lastExpandedTab);
      }
   },
   
   changeTabAndFocus: function(iIntexTab,oField ){
     this.showTabByIndex(iIntexTab);
     oField.focus();
   }
});