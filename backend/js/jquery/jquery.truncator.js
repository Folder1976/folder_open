(function($) {

  var trailing_whitespace = true;

  $.fn.truncate = function(options) {

    var opts = $.extend({}, $.fn.truncate.defaults, options);

    $(this).each(function() {

      var content_length = $.trim(squeeze($(this).text())).length;
      if (content_length <= opts.max_length)
        return;  // bail early if not overlong

      var actual_max_length = opts.max_length - opts.more.length - 3;  // 3 for " ()"
      var truncated_node = recursivelyTruncate(this, actual_max_length);
      var full_node = $(this).hide();

      truncated_node.insertAfter(full_node);

      findNodeForMore(truncated_node).append(' <a style="text-decoration:none;" href="#show more content">'+opts.more+'</a>');
      findNodeForLess(full_node).append(' <a style="text-decoration:none;" href="#show less content">'+opts.less+'</a>');

      truncated_node.find('a:last').click(function() {
        truncated_node.hide(); full_node.show(); return false;
      });
      full_node.find('a:last').click(function() {
        truncated_node.show(); full_node.hide(); return false;
      });

    });
  }

  // Note that the " (more)" bit counts towards the max length  so a max
  // length of 10 would truncate "1234567890" to "12 (more)".
  $.fn.truncate.defaults = {
    max_length: 100,
    more: '&#8594;',
    less: '&#8592;'
  };

  function recursivelyTruncate(node, max_length) {
    return (node.nodeType == 3) ? truncateText(node, max_length) : truncateNode(node, max_length);
  }

  function truncateNode(node, max_length) {
    var node = $(node);
    var new_node = node.clone().empty();
    var truncatedChild;
    node.contents().each(function() {
      var remaining_length = max_length - new_node.text().length;
      if (remaining_length == 0) return;  // breaks the loop
      truncatedChild = recursivelyTruncate(this, remaining_length);
      if (truncatedChild) new_node.append(truncatedChild);
    });
    return new_node;
  }

  function truncateText(node, max_length) {
    var text = squeeze(node.data);
    if (trailing_whitespace)  // remove initial whitespace if last text
      text = text.replace(/^ /, '');  // node had trailing whitespace.
    trailing_whitespace = !!text.match(/ $/);
    var text = text.slice(0, max_length);
    // Ensure HTML entities are encoded
    // http://debuggable.com/posts/encode-html-entities-with-jquery:480f4dd6-13cc-4ce9-8071-4710cbdd56cb
    text = $('<div/>').text(text).html();
    return text;
  }

  // Collapses a sequence of whitespace into a single space.
  function squeeze(string) {
    return string.replace(/\s+/g, ' ');
  }

  // Finds the last, innermost block-level element
  function findNodeForMore(node) {
    var $node = $(node);
    var last_child = $node.children(":last");
    if (!last_child) return node;
    var display = last_child.css('display');
    if (!display || display=='inline') return $node;
    return findNodeForMore(last_child);
  };

  // Finds the last child if it's a p; otherwise the parent
  function findNodeForLess(node) {
    var $node = $(node);
    var last_child = $node.children(":last");
    if (last_child && last_child.is('p')) return last_child;
    return node;
  };

})(jQuery);



var tooltip = null;


var tooltip_fn = function(element, text) {
    $(element).hover(
        function(e) {
            $(tooltip).text(text).css('display', 'block');
            $(element).mousemove(
                function(e) {

                    var mouseX = e.pageX;
                    var mouseY = e.pageY;

                    $(tooltip).css({ 'top': mouseY + 3 + "px", 'left': mouseX + 3 + "px" });
                }
            );
        },
        function() {
            $(tooltip).css('display', 'none');
        }
    );
};
var api = null;
var api2 = null;
var closeAll = function() {
    api.close();

	$("#f1").attr("value", '');
	$("#f2").attr("value", '');
	$("#f3").attr("value", '');
	$("#f4").attr("value", '');
}
var openForm = function() {

api.load();
}

$(document).ready(function() {
    var el = 0;// api2 = $("#okwindow").overlay({ api: true });
    api = $("#overlay").overlay({
        api: true,
        oneInstance: false,
        closeOnClick: false,
        expose: { color: '#000', opacity: 0.7, closeOnClick: false }
    });
    //, onClose: function() { /*$("#").overlay(); }*/
    $('.truncInfoCategory').truncate({ max_length: 100 });
    $('.truncInfo').truncate({ max_length: 100 });

	//   
	$("#f5").click(function() {
        var Name = $("#f1").attr("value");
        var Email = $("#f2").attr("value");
        var Phone = $("#f3").attr("value");
        var Message = $("#f4").attr("value");

		if (Email == '' && Phone == '') {
			alert(', ,   email  .');
		}
		else {
			IMPOST.FrontOffice.Services.GoodsService.SendZayavka(el, Name, Email, Phone, Message, DescrOk, DescrError);
		}
        return false;
    });

    tooltip = $("#tooltip.tooltip");
    var tovari = $('a.pricep');

    $(".close_in").click(function() { closeAll(); });

    $(tovari).each(function(index, element) {
        tooltip_fn($(element), "    ");
        //$(element).overlay();
        $(element).click(function() {
            el = parseInt($(element).attr("rev"));

			var parent = $(element).parent();

			while ($(parent).hasClass('forZayavka') != true) {
				parent = $(parent).parent();
			}

			$('#zayaGoodsName').text($(parent).find('.goodsName').text());
			$('#zayaBrandName').text($(parent).find('.brandName').text());
            openForm();
        });
    });
});




function DescrOk(data) {
    closeAll();
    $("#okwindow").text("   ");
    api2.load();
}
function DescrError(data) {
    closeAll();
    $("#okwindow").text(" ");
    api2.load();
}

