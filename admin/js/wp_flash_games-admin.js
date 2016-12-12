(function( $ ) {
	
	// stop our admin menus from collapsing
	if( $('body[class*=" wpfg_"]').length || $('body[class*=" post-type-wpfg_"]').length) {

		$wpfg_menu_li = $('#toplevel_page_wpfg_dashboard_admin_page');

		$wpfg_menu_li
			.removeClass('wp-not-current-submenu')
			.addClass('wp-has-current-submenu')
			.addClass('wp-menu-open');

		$('a:first',$wpfg_menu_li)
			.removeClass('wp-not-current-submenu')
			.addClass('wp-has-submenu')
			.addClass('wp-has-current-submenu')
			.addClass('wp-menu-open');

		// set Games Genre active in wp_menu (admin panel)
		var gamesGenreEditPath = window.document.documentURI.split('edit-tags.php?');
		console.log(gamesGenreEditPath.length);
		var gamesGenreTaxPath = 'taxonomy=wpfg_genre&post_type=wpfg_games';

		if(gamesGenreEditPath[1] == gamesGenreTaxPath) {
			var selectedGameGenre = $wpfg_menu_li.find("li > a[href*='"+gamesGenreTaxPath+"']");
			selectedGameGenre
				.addClass('current')
				.parent()
				.addClass('current');
		}
	}

})( jQuery );
