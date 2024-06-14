function loadContent(url, containerId) {
  jQuery.ajax({
    url: url,
    success: function (result) {
      if (result != "") {
        var container = $("#" + containerId);
        if (container.find("tr").length > 0) {
          container.find("tr:first").before(result);
        } else {
          container.append(result);
        }
      }
    },
  });
}
