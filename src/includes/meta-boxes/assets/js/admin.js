/* global iandeAdminParams */
(function ( $ ) {
	'use strict';

	/**
	 * Theme Options and Metaboxes.
	 */
	$( function () {

		/**
		 * Image field.
		 */
		$( '.iande-upload-image .button' ).on( 'click', function ( e ) {
			e.preventDefault();

			var uploadFrame,
				uploadInput = $(this).siblings( '.image' ),
				uploadPreview = $(this).siblings( '.preview' );

			// If the media frame already exists, reopen it.
			if ( uploadFrame ) {
				uploadFrame.open();

				return;
			}

			// Create the media frame.
			uploadFrame = wp.media.frames.downloadable_file = wp.media({
				title: iandeAdminParams.uploadTitle,
				button: {
					text: iandeAdminParams.uploadButton
				},
				multiple: false,
				library: {
					type: 'image'
				}
			});

			uploadFrame.on( 'select', function () {
				var attachment = uploadFrame.state().get( 'selection' ).first().toJSON();
				uploadPreview.attr( 'src', attachment.url );
				uploadInput.val( attachment.id );
			});

			// Finally, open the modal.
			uploadFrame.open();
		});

		$( '.iande-upload-image .delete' ).click( function ( e ) {
			e.preventDefault();

			var wrapper      = $( this ).parents( '.iande-upload-image' ),
				defaultImage = $( '.default-image', wrapper ).text();

			$( '.image', wrapper ).val( '' );
			$( '.preview', wrapper ).attr( 'src', defaultImage );
		});

		/**
		 * Upload.
		 */
		$( '.iande-upload-button' ).on( 'click', function ( e ) {
			e.preventDefault();

			var uploadFrame,
				uploadInput = $( this ).prev( 'input' );

			// If the media frame already exists, reopen it.
			if ( uploadFrame ) {
				uploadFrame.open();

				return;
			}

			// Create the media frame.
			uploadFrame = wp.media.frames.downloadable_file = wp.media({
				title: iandeAdminParams.uploadTitle,
				button: {
					text: iandeAdminParams.uploadButton
				},
				multiple: false
			});

			uploadFrame.on( 'select', function () {
				var attachment = uploadFrame.state().get( 'selection').first().toJSON();
				uploadInput.val( attachment.url );
			});

			// Finally, open the modal.
			uploadFrame.open();
		});

		/**
		 * Color Picker.
		 */
		$( '.iande-color-field' ).wpColorPicker();

		/**
		 * Image plupload adds.
		 */
		$( '.iande-gallery-container' ).on( 'click', '.iande-gallery-add', function ( e ) {
			e.preventDefault();

			var galleryFrame,
				galleryWrap = $( this ).parent( '.iande-gallery-container' ),
				imageGalleryIds = $( '.iande-gallery-field', galleryWrap ),
				images = $( 'ul.iande-gallery-images', galleryWrap ),
				attachmentIds = imageGalleryIds.val();

			// If the media frame already exists, reopen it.
			if ( galleryFrame ) {
				galleryFrame.open();

				return;
			}

			// Create the media frame.
			galleryFrame = wp.media.frames.downloadable_file = wp.media({
				title: iandeAdminParams.galleryTitle,
				button: {
					text: iandeAdminParams.galleryButton
				},
				multiple: true,
				library: {
					type: 'image'
				}
			});

			// When an image is selected, run a callback.
			galleryFrame.on( 'select', function () {

				var selection = galleryFrame.state().get( 'selection' );

				selection.map( function ( attachment ) {

					attachment = attachment.toJSON();

					if ( attachment.id ) {
						attachmentIds = attachmentIds ? attachmentIds + ',' + attachment.id : attachment.id;

						images.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment.url + '" /><ul class="actions"><li><a href="#" class="delete" title="' + iandeAdminParams.galleryRemove + '">X</a></li></ul></li>' );
					}

				});

				imageGalleryIds.val( attachmentIds );
			});

			// Finally, open the modal.
			galleryFrame.open();
		});

		/**
		 * Image plupload ordering.
		 */
		$( '.iande-gallery-container' ).on( 'mouseover', 'ul.iande-gallery-images', function () {
			var galleryWrap = $( this ).parent( '.iande-gallery-container' ),
				imageGalleryIds = $( '.iande-gallery-field', galleryWrap );

			// Call the sortable action.
			$( this ).sortable({
				items: 'li.image',
				cursor: 'move',
				scrollSensitivity: 40,
				forcePlaceholderSize: true,
				forceHelperSize: false,
				helper: 'clone',
				opacity: 0.65,
				placeholder: 'wc-metabox-sortable-placeholder',
				start: function ( event, ui ) {
					ui.item.css('background-color', '#f6f6f6');
				}, stop: function ( event, ui ) {
					ui.item.removeAttr( 'style' );
				}, update: function () {
					var attachmentIds = '';

					// Gets the current ids.
					$( 'li.image', $( this ) ).css( 'cursor', 'default' ).each( function () {
						var attachmentId = $( this ).attr( 'data-attachment_id' );
						attachmentIds = attachmentIds + attachmentId + ',';
					});

					// Return the new value.
					imageGalleryIds.val( attachmentIds );
				}
			});
		});

		/**
		 * Image plupload remove link.
		 */
		$( '.iande-gallery-container' ).on( 'click', 'a.delete', function ( e ) {
			e.preventDefault();

			var galleryWrap = $( this ).parents( '.iande-gallery-container' ),
				imageGalleryIds = $( '.iande-gallery-field', galleryWrap ),
				attachmentIds = '';

			// Remove the item.
			$( this ).closest( 'li.image' ).remove();

			// Gets the current ids.
			$( 'ul li.image', galleryWrap ).css( 'cursor', 'default' ).each( function () {
				var attachmentId = $( this ).attr( 'data-attachment_id' );
				attachmentIds = attachmentIds ? attachmentIds + ',' + attachmentId : attachmentId;
			});

			// Return the new value.
			imageGalleryIds.val( attachmentIds );
		});
		/**
		 * Save editor field in add term screen
		 */
		$( 'body.edit-tags-php .wp-editor-wrap' ).on( 'mouseover mouseout', function( e ) {
			tinyMCE.triggerSave();
		});

	});
}( jQuery ));