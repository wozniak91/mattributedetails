"use strict";

$(document).ready(function() {
  AttributesDetails.init("#attributesWrapper");
  var search = document.getElementById("attributesSearch");

  search.addEventListener("keyup", function(e) {
    var query = e.target.value;
    AttributesDetails.search(query);
  });
});
var AttributesDetails = {
  init: async function init(el) {
    this.el = document.querySelector(el);
    this.attributes = await this.get();
    return this.render(this.attributes);
  },
  get: function get() {
    return fetch("?ajax=1&action=getattributes").then(function(resp) {
      return resp.json();
    });
  },
  render: function render(attributes) {
    var list = document.createElement("div");
    list.classList.add("row");
    attributes.map(function(attribute) {
      var wrapper = document.createElement("div");
      wrapper.classList =
        "attribute-details col-lg-3 col-md-4 col-sm-6 col-xs-12";
      var article = document.createElement("article");
      article.classList.add("attribute-details__wrapper");
      var image = document.createElement("img");
      image.classList = "attribute-details__image img-responsive lazy";
      image.setAttribute(
        "src",
        "/modules/mattributedetails/images/".concat(attribute.cover_image)
      );
      var title = document.createElement("h3");
      title.classList.add("attribute-details__title");
      title.innerText = attribute.title;
      article.prepend(image);
      article.append(title);
      wrapper.append(article);
      list.prepend(wrapper);
    });
    this.el.innerHTML = "";
    return this.el.append(list);
  },
  search: function search(query) {
    return this.render(
      this.attributes.filter(function(attribute) {
        var title = attribute.title.toLocaleLowerCase();
        query = query.toLocaleLowerCase();
        return title.includes(query);
      })
    );
  }
};
