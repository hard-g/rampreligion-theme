(function(){
	// Highlight the current nav item. Gutenberg does not allow any way to customize this.
	const { ptDirectoryUri, ptDirectoryUriRelative } = window.rampreligion;
	const navItems = document.querySelectorAll('.wp-block-navigation-item');

	navItems.forEach((navItem) => {
		const navItemLink = navItem.querySelector('a');
		const href = navItemLink.getAttribute('href');
		const isCurrent = href === ptDirectoryUri || href === ptDirectoryUriRelative;

		if (isCurrent) {
			navItem.classList.add('current-menu-item');
		}
	})

}());
