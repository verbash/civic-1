/**
 * Civic-1 drawer navigation
 *
 * Modal drawer via native <dialog> (focus trap, Escape, ::backdrop).
 * Exclusive disclosure sections per APG navigation menu pattern.
 *
 * @see https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/
 * @see cv1-component-library.md
 */
( function () {
	const root = document.querySelector( '[data-cv1-drawer]' );
	if ( ! root ) {
		return;
	}

	const dialog = root.querySelector( 'dialog' );
	const openBtn = root.querySelector( '.cv1-drawer__open' );
	const closeBtn = root.querySelector( '[data-cv1-drawer-close]' );
	const pageMain =
		document.querySelector( '.wp-site-blocks > main' ) ||
		document.querySelector( 'main' );
	const siteHeader =
		document.querySelector( '.wp-site-blocks > header.wp-block-template-part' ) ||
		document.querySelector( 'header.wp-block-template-part' );
	const mobileQuery = window.matchMedia( '(max-width: 781px)' );

	if ( ! dialog || ! openBtn || ! closeBtn ) {
		return;
	}

	let scrollLockY = 0;

	function getAdminBarOffset() {
		return document.body.classList.contains( 'admin-bar' ) ? 46 : 0;
	}

	function pinSiteHeader() {
		if ( ! siteHeader || ! mobileQuery.matches ) {
			return;
		}
		const targetTop = getAdminBarOffset();
		const rect = siteHeader.getBoundingClientRect();
		const shift = targetTop - rect.top;
		if ( Math.abs( shift ) < 1 ) {
			return;
		}
		siteHeader.style.transform = 'translate3d(0,' + shift + 'px,0)';
		siteHeader.classList.add( 'cv1-drawer-header-pinned' );
	}

	function unpinSiteHeader() {
		if ( ! siteHeader ) {
			return;
		}
		siteHeader.style.transform = '';
		siteHeader.classList.remove( 'cv1-drawer-header-pinned' );
	}

	function setInert( inert ) {
		if ( ! pageMain ) {
			return;
		}
		if ( inert ) {
			pageMain.setAttribute( 'inert', '' );
		} else {
			pageMain.removeAttribute( 'inert' );
		}
	}

	function lockScroll() {
		scrollLockY = window.scrollY || window.pageYOffset || 0;
		document.body.style.position = 'fixed';
		document.body.style.top = '-' + scrollLockY + 'px';
		document.body.style.left = '0';
		document.body.style.right = '0';
		document.body.style.width = '100%';
		document.documentElement.classList.add( 'cv1-drawer-open' );
	}

	function unlockScroll() {
		document.body.style.position = '';
		document.body.style.top = '';
		document.body.style.left = '';
		document.body.style.right = '';
		document.body.style.width = '';
		document.documentElement.classList.remove( 'cv1-drawer-open' );
		window.scrollTo( 0, scrollLockY );
	}

	const scrim = dialog.querySelector( '[data-cv1-drawer-scrim]' );
	const motionQuery = window.matchMedia( '(prefers-reduced-motion: reduce)' );
	const fadeDurationMs = 280;
	let isClosing = false;

	function setMenuToggleOpen( isOpen ) {
		if ( isOpen ) {
			root.classList.add( 'cv1-drawer--menu-open' );
		} else {
			root.classList.remove( 'cv1-drawer--menu-open' );
		}
	}

	function runAfterFadeTransition( callback ) {
		if ( ! scrim || motionQuery.matches ) {
			callback();
			return;
		}

		let done = false;

		function finish() {
			if ( done ) {
				return;
			}
			done = true;
			dialog.removeEventListener( 'transitionend', onEnd );
			callback();
		}

		function onEnd( event ) {
			if ( event.target === dialog && event.propertyName === 'opacity' ) {
				finish();
			}
		}

		dialog.addEventListener( 'transitionend', onEnd );
		window.setTimeout( finish, fadeDurationMs );
	}

	function openDrawer() {
		if ( dialog.open ) {
			return;
		}
		lockScroll();
		pinSiteHeader();
		dialog.showModal();
		setMenuToggleOpen( true );
		setInert( true );
		dialog.classList.remove( 'cv1-drawer__dialog--visible' );

		window.requestAnimationFrame( function () {
			window.requestAnimationFrame( function () {
				dialog.classList.add( 'cv1-drawer__dialog--visible' );
			} );
		} );

		closeBtn.focus( { preventScroll: true } );
	}

	function closeDrawer() {
		if ( ! dialog.open || isClosing ) {
			return;
		}

		isClosing = true;
		setMenuToggleOpen( false );
		dialog.classList.remove( 'cv1-drawer__dialog--visible' );

		runAfterFadeTransition( function () {
			isClosing = false;
			if ( ! dialog.open ) {
				return;
			}
			dialog.close();
			setInert( false );
			collapseAllDisclosures();
			openBtn.focus( { preventScroll: true } );
		} );
	}

	function collapseDisclosure( button ) {
		const panelId = button.getAttribute( 'aria-controls' );
		const panel = panelId ? document.getElementById( panelId ) : null;
		button.setAttribute( 'aria-expanded', 'false' );
		if ( panel ) {
			panel.hidden = true;
		}
	}

	function expandDisclosure( button ) {
		const panelId = button.getAttribute( 'aria-controls' );
		const panel = panelId ? document.getElementById( panelId ) : null;
		button.setAttribute( 'aria-expanded', 'true' );
		if ( panel ) {
			panel.hidden = false;
		}
	}

	function collapseAllDisclosures() {
		root.querySelectorAll( '.cv1-drawer__disclosure[aria-expanded="true"]' ).forEach( collapseDisclosure );
	}

	openBtn.addEventListener( 'click', openDrawer );
	closeBtn.addEventListener( 'click', closeDrawer );

	dialog.addEventListener( 'cancel', function ( event ) {
		event.preventDefault();
		closeDrawer();
	} );

	dialog.addEventListener( 'close', function () {
		isClosing = false;
		setMenuToggleOpen( false );
		dialog.classList.remove( 'cv1-drawer__dialog--visible' );
		unpinSiteHeader();
		unlockScroll();
		setInert( false );
		collapseAllDisclosures();
	} );

	if ( scrim ) {
		scrim.addEventListener( 'click', closeDrawer );
	}

	root.querySelectorAll( '.cv1-drawer__disclosure' ).forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			const isOpen = button.getAttribute( 'aria-expanded' ) === 'true';

			if ( isOpen ) {
				collapseDisclosure( button );
				return;
			}

			root.querySelectorAll( '.cv1-drawer__disclosure[aria-expanded="true"]' ).forEach( function ( other ) {
				if ( other !== button ) {
					collapseDisclosure( other );
				}
			} );

			expandDisclosure( button );
		} );
	} );
} )();
