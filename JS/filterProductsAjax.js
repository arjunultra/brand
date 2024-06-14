function fetchAndDisplay(url, containerId) {
  jQuery.ajax({
    url: url,
    success: function (result) {
      alert(result);
      if (result != "" && $(containerId).length > 0) {
        $(containerId).html(result);
      }
    },
  });
}
