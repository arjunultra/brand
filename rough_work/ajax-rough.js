let postURL = "party-form-changes.php ? selected_brand = " + brand_id;
function getProducts(brand_id) {
  jQuery.ajax({
    url: postURL,
    success: function (result) {
      if (result != "") {
        if ($("#product-container").length > 0) {
          $("#product-container").html(result);
        }
      }
    },
  });
}
// let post_url = "party-form-changes.php?selected_brand=" + brand_id;
