// Used in big DOM with lots of IDs, check
Element.warnDuplicates = Prototype.emptyFunction;

CheckRegression = {
  run: function() {
    alert('run it');
  },
  show: function() {
    alert('show it');
  }
};
