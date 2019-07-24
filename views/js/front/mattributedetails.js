"use strict";
$(document).ready(function() {
    AttributesDetails.init('#attributes')
});



$(document).on('click', '#color_to_pick_list a', function(e) {

    var query = $(this).attr('title');
    AttributesDetails.search(query);

})

var AttributesDetails = {
  init: async function init(el) {

    this.el = document.querySelector(el);
    this.wrapper = document.createElement('div');
    this.wrapper.classList.add('mattributedetails');

    this.el.append(this.wrapper);
  },
  search: function search(query) {
    var _this = this;
    fetch("/index.php?fc=module&module=mattributedetails&controller=attributes&ajax=1&action=search&q=" + query).then(function(resp) {
       return resp.json();
    }).then(function(result) {
        _this.render(result);
    });
  },
  render: function render(attributes) {

    var _this = this;
    _this.wrapper.classList.remove('mattributedetails--active');
    _this.wrapper.innerHTML = '';

    attributes.map(function(attribute) {

        var article = document.createElement('article');
        article.classList.add('mattributedetails__content');
        var title = document.createElement('h4');
        title.classList.add('mattributedetails__title');
        title.textContent = attribute.title;
        var description = document.createElement('div');
        description.classList.add('mattributedetails__description');
        description.innerHTML = attribute.content;

        article.prepend(title);
        article.append(description);

        _this.wrapper.append(article);

    });
    if(attributes.length > 0) {
        setTimeout(function() {
            _this.wrapper.classList.add('mattributedetails--active');
        }, 100)
    }

  },
};
