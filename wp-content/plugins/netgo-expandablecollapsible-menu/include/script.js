jQuery(function($) {

	var $netgo_navigations = $("div.netgo_navigation");
	var options = window.netgo_navigation_options;

	// hide childs on load
	$netgo_navigations.find("ul ul").hide();
	$netgo_navigations.find(".sub-menu").hide();
	
	
	// open up onstart
	//$netgo_navigations.find("li.current_page_ancestor,li.current_page_parent,li.current_page_item").find("ul:first").show();
	
	// Click on a = possibly expand, or possibly go to link
	$netgo_navigations.find("ul a").live("click", function(e, isFromLI) {

		e.stopPropagation();
		
		$this = $(this);
		var do_expand = true;
		
		// get the options for this instance
		var widget_wrapper = $this.closest("div.netgo_navigation");
		var widget_options = options[widget_wrapper.attr("id").replace("-", "")];
		
		// Grab the ul that we will expand/collapse
		var next_ul = $this.next("ul");

		// Determine if this is the parent link of a ul or not
		if (!next_ul.length) {
			// Not a parent, does not have childs. Just go to the link.
			return true;
		}
		
		
		// click on link, don't do anything unless "clickable_parent"is true, then we expand!
		// or if the click comes from an LI-element, then do not go to link, just expand
		if (widget_options.clickable_parent || isFromLI) {
			// clickable parent, so expand and don't go to link
			e.preventDefault();
		} else {
			// not clickable parent, don't expand, go to link
			do_expand = false;
		}
		
		if (do_expand) { 
			
			if ($this.next("ul").length) {
				$this.next("ul:first").slideToggle("fast", function() {
					if ( $this.find("ul:first").is(":visible") ) {
						$this.removeClass("netgo-navigation-deselected");
						$this.addClass("netgo-navigation-selected");
					} else {
						$this.removeClass("netgo-navigation-selected");
						$this.addClass("netgo-navigation-deselected");
					}
				});
				
			}

		}

	});

	// Click on li = expand, but don't go to link
	$netgo_navigations.find("ul li").live("click", function(e) { 
		
		// click the first a below this one, because that's where the action is
		e.stopPropagation();
		var $this = $(this);
		
		// Trigger click event on the link.
		// Also pass extraParameter "true" that tells the click event to just expand and not go to the link
		$this.find("a:first").trigger("click", [true]);
		
		return;

	});
});