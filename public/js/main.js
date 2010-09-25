var global = {
  'ghpage': 'http://github.com/mynameiszanders/eventing/raw/master/',
  'canconsole': window.console && console.log ? true : false,
  'ghselect': 'code.ghpage',
  'mask': new Element('div', {'class': 'modalMask'})
};

window.addEvent('domready', function() {
  grabPaths();
  window.addEvent('resize', function(e) {
    resizeModal();
  });
  // Insert and set the initial dimensions as the page loads.
  global.mask.inject(document.body, 'top');
  resizeModal();
});

function resizeModal() {
  
}

function grabPaths() {
  var paths = $$('code.ghpage');
  var count = $type(paths) == 'array' ? paths.length : 0;
  global.canconsole && console.log('Found '+count+' GitHub filepaths.');
  paths.each(function(element, index) {
    element.addEvent('click', function(e) {
      if(e) e.stop();
      var path = this.innerText;
      global.canconsole && console.log('Loading: ' + path);
      loadCodeModal(path);
      return false;
    });
  });
}

function loadCodeModal() {
  global.mask.setStyle('display', 'block');
  
}