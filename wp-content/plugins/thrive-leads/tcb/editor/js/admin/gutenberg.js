( function ( $ ) {
	var ThriveGutenbergSwitch = {

		/**
		 * Check if we're using the block editor
		 * @returns {boolean}
		 */
		isGutenbergActive: function () {
			return typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined';
		},

		init: function () {
			var self = this;
			this.$gutenberg = $( '#editor' );
			this.$architectNotificationContent = $( '#thrive-gutenberg-switch' ).html();
			this.$architectDisplay = $( '<div id="tar-display">' ).append( this.$architectNotificationContent );
			this.$architectLauncher = this.$architectDisplay.find( '#thrive_preview_button' );
			this.isPostBox = this.$architectNotificationContent.indexOf( 'postbox' ) !== - 1;

			$( window ).on( 'storage.tcb', function ( e ) {
				var current_post = wp.data.select( "core/editor" ).getCurrentPost(),
					post;

				try {
					post = JSON.parse( e.originalEvent.newValue );
				} catch ( e ) {

				}

				if ( post && post.ID && e.originalEvent.key === 'tve_post_options_change' && post.ID === Number( current_post.id ) ) {
					window.location.reload();
				}
			} );

			wp.data.subscribe( function () {
				var coreEditor = wp.data.select( 'core/editor' );
				if ( coreEditor ) {
					var isSavingPost = coreEditor.isSavingPost(),
						isAutosavingPost = coreEditor.isAutosavingPost();

					if ( isSavingPost && ! isAutosavingPost ) {
						var data = JSON.stringify( coreEditor.getCurrentPost() );

						window.localStorage.setItem( 'tve_post_options_change', data );
					}
				}

				/**
				 * On data subscribe check if our elements exists
				 */
				setTimeout( function () {
					self.render();
				}, 1 );
			} );
		},
		render: function () {
			var self = this,
				shouldBindEvents = false;
			if ( this.isPostBox ) {
				if ( ! $( '#tar-display' ).length ) {
					var $postTitle = this.$gutenberg.find( '.editor-post-title' );
					if ( $postTitle.length ) {
						if ( $postTitle[ 0 ].tagName === 'DIV' ) {
							$postTitle.append( this.$architectDisplay );
						} else {
							$postTitle.after( this.$architectDisplay );
						}
					}

					this.$gutenberg.find( '.editor-block-list__layout,.block-editor-block-list__layout' ).hide();
					this.$gutenberg.find( '.editor-post-title__block' ).css( 'margin-bottom', '0' );
					this.$gutenberg.find( '.editor-writing-flow__click-redirect,.block-editor-writing-flow__click-redirect' ).hide();
					this.$gutenberg.find( '.edit-post-header-toolbar' ).css( 'visibility', 'hidden' );
					shouldBindEvents = true;
				}
			} else {
				if ( ! $( '#thrive_preview_button' ).length ) {
					this.$gutenberg.find( '.edit-post-header-toolbar' ).append( this.$architectLauncher );
					this.$architectLauncher.on( 'click', function () {
						self.$gutenberg.find( '.editor-block-list__layout' ).hide();
					} );
					this.$gutenberg.find( '.edit-post-header-toolbar' ).css( 'visibility', 'visible' );
					shouldBindEvents = true;
				}
			}

			// so we can use saved styles
			$( '.editor-block-list__layout,.block-editor-block-list__layout' ).addClass( 'tcb-style-wrap' );
			if ( shouldBindEvents ) {
				this.bindEvents();
			}
		},

		bindEvents: function () {
			var self = this;
			$( '#tcb2-show-wp-editor' ).on( 'click', function () {
				var $editlink = self.$gutenberg.find( '.tcb-enable-editor' ),
					$postbox = $editlink.closest( '.postbox' );

				$.ajax( {
					type: 'post',
					url: ajaxurl,
					dataType: 'json',
					data: {
						_nonce: TCB_Post_Edit_Data.admin_nonce,
						post_id: this.getAttribute( 'data-id' ),
						action: 'tcb_admin_ajax_controller',
						route: 'disable_tcb'
					}
				} ).done( function ( response ) {
				} );

				$postbox.next( '.tcb-flags' ).find( 'input' ).prop( 'disabled', false );
				$postbox.remove();
				self.$gutenberg.find( '.editor-block-list__layout,.block-editor-block-list__layout' ).show();
				self.isPostBox = false;
				self.render();

				self.fixBlocksPreview();
			} );

			this.$architectLauncher.on( 'click', function () {
				$.ajax( {
					type: 'post',
					url: ajaxurl,
					dataType: 'json',
					data: {
						_nonce: TCB_Post_Edit_Data.admin_nonce,
						post_id: this.getAttribute( 'data-id' ),
						action: 'tcb_admin_ajax_controller',
						route: 'change_post_status_gutenberg'
					}
				} )
			} );
		},
		/**
		 * Fix block height once returning to gutenberg editor
		 */
		fixBlocksPreview: function () {
			var blocks = document.querySelectorAll( '[data-type*="thrive"] iframe' ),
				tveOuterHeight = function ( el ) {
					if ( ! el ) {
						return 0;
					}
					var height = el.offsetHeight,
						style = getComputedStyle( el );

					height += parseInt( style.marginTop ) + parseInt( style.marginBottom );
					return height;
				};

			Array.prototype.forEach.call( blocks, function ( iframe ) {
				var iframeDocument = iframe.contentDocument;

				iframe.style.setProperty(
					'height',
					''
				);
				iframe.parentNode.style.setProperty(
					'height',
					''
				);
				/**
				 * Fix countdown resizing
				 */
				Array.prototype.forEach.call( iframeDocument.body.querySelectorAll( '.tve-countdown' ), function ( countdown ) {
					countdown.style.setProperty(
						'--tve-countdown-size',
						''
					);
				} );


				var height = tveOuterHeight(
					iframeDocument.body
				);

				iframe.style.setProperty(
					'height',
					height + 'px'
				);
				iframe.parentNode.style.setProperty(
					'height',
					height + 'px'
				);
			} );
		}
	};

	$( function () {
		if ( ThriveGutenbergSwitch.isGutenbergActive() ) {
			ThriveGutenbergSwitch.init();
		}
		window.addEventListener( 'load', function () {
			$( '.tcb-revert' ).on( 'click', function () {
				if ( confirm( 'Are you sure you want to DELETE all of the content that was created in this landing page and revert to the theme page? \n If you click OK, any custom content you added to the landing page will be deleted.' ) ) {
					location.href = location.href + '&tve_revert_theme=1&nonce=' + this.dataset.nonce;
					$( '#editor' ).find( '.edit-post-header-toolbar' ).css( 'visibility', 'visible' );
				}
			} );
		} );
	} );

}( jQuery ) );
