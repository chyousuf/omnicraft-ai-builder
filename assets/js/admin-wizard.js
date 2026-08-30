/**
 * OmniCraft AI Builder - Admin Wizard & Interactive Logic
 */

(function ($) {
	'use strict';

	let uploadedImageBase64 = '';
	let uploadedImageMime = '';

	$(document).ready(function () {
		initQuickPrompts();
		initTabs();
		initFileUpload();
		initScraper();
		initPalettes();
		initSectionSelector();
		initGenerator();
		initHistory();
	});

	/**
	 * Section Architecture Selector, Custom Section Creator & Modal Editor
	 */
	function initSectionSelector() {
		let $activeEditCard = null;
		const $modal = $('#oc-section-modal');

		function updateSectionCount() {
			const total = $('.oc-section-toggle-card').length;
			const selected = $('.oc-section-toggle-card.selected').length;
			$('#oc-section-count-badge').text(selected + ' / ' + total + ' Selected');
		}

		function openModal() {
			$modal.css('display', 'flex').hide().fadeIn(150);
		}

		function closeModal() {
			$modal.fadeOut(150, function () {
				$(this).css('display', 'none');
				$activeEditCard = null;
			});
		}

		// Toggle section selection when clicking card body (ignoring tool buttons)
		$(document).on('click', '.oc-section-toggle-card', function (e) {
			if ($(e.target).closest('.oc-card-tool-btn').length || $(e.target).hasClass('oc-card-tool-btn')) {
				return;
			}
			e.preventDefault();
			const $card = $(this);
			const $checkbox = $card.find('input[type="checkbox"]');
			const isChecked = !$checkbox.prop('checked');

			$checkbox.prop('checked', isChecked);
			$card.toggleClass('selected', isChecked);
			updateSectionCount();
		});

		// Select All Button
		$(document).on('click', '#oc-btn-select-all-sections', function (e) {
			e.preventDefault();
			$('.oc-section-toggle-card').each(function () {
				$(this).addClass('selected');
				$(this).find('input[type="checkbox"]').prop('checked', true);
			});
			updateSectionCount();
		});

		// Deselect All Button
		$(document).on('click', '#oc-btn-deselect-all-sections', function (e) {
			e.preventDefault();
			$('.oc-section-toggle-card').each(function () {
				$(this).removeClass('selected');
				$(this).find('input[type="checkbox"]').prop('checked', false);
			});
			updateSectionCount();
		});

		// Delete Section Handler
		$(document).on('click', '.oc-delete-sec-btn', function (e) {
			e.preventDefault();
			e.stopPropagation();
			const $card = $(this).closest('.oc-section-toggle-card');
			const title = $card.find('.oc-sec-title').text().trim();

			if (confirm('Are you sure you want to remove "' + title + '" from your blueprint?')) {
				$card.fadeOut(200, function () {
					$(this).remove();
					updateSectionCount();
				});
			}
		});

		// Edit Section Handler
		$(document).on('click', '.oc-edit-sec-btn', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$activeEditCard = $(this).closest('.oc-section-toggle-card');

			const type = $activeEditCard.attr('data-type') || 'custom';
			const tag = $activeEditCard.attr('data-tag') || $activeEditCard.find('.oc-sec-tag').text().trim();
			const title = $activeEditCard.attr('data-title') || $activeEditCard.find('.oc-sec-title').text().trim();
			const desc = $activeEditCard.attr('data-desc') || $activeEditCard.find('.oc-sec-desc').text().trim();

			$('#oc-modal-title').html('<i class="fa-solid fa-pen-to-square"></i> Edit Section Blueprint');
			$('#oc-modal-sec-type').val(type);
			$('#oc-modal-sec-tag').val(tag);
			$('#oc-modal-sec-title').val(title);
			$('#oc-modal-sec-desc').val(desc);
			$('#oc-modal-edit-id').val('editing');

			openModal();
		});

		// Open Add Custom Section Modal (Header button and bottom trigger card)
		$(document).on('click', '#oc-btn-add-section, #oc-add-section-trigger', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$activeEditCard = null;

			$('#oc-modal-title').html('<i class="fa-solid fa-circle-plus"></i> Add Custom Section');
			$('#oc-modal-sec-type').val('custom');
			$('#oc-modal-sec-tag').val('[CUSTOM]');
			$('#oc-modal-sec-title').val('');
			$('#oc-modal-sec-desc').val('');
			$('#oc-modal-edit-id').val('');

			openModal();
			setTimeout(function () {
				$('#oc-modal-sec-title').focus();
			}, 200);
		});

		// Close Modal Handlers
		$(document).on('click', '#oc-modal-close-btn, #oc-modal-cancel-btn, .oc-modal-backdrop', function (e) {
			e.preventDefault();
			closeModal();
		});

		// Save Modal Handler (Add or Update)
		$(document).on('click', '#oc-modal-save-btn', function (e) {
			e.preventDefault();
			const type = $('#oc-modal-sec-type').val();
			let tag = $('#oc-modal-sec-tag').val().trim() || '[CUSTOM]';
			if (!tag.startsWith('[')) tag = '[' + tag;
			if (!tag.endsWith(']')) tag = tag + ']';

			const title = $('#oc-modal-sec-title').val().trim();
			const desc = $('#oc-modal-sec-desc').val().trim();

			if (!title) {
				alert('Please enter a Section Title.');
				$('#oc-modal-sec-title').focus();
				return;
			}

			if ($activeEditCard && $activeEditCard.length) {
				// Update existing card
				$activeEditCard.attr('data-type', type);
				$activeEditCard.attr('data-tag', tag);
				$activeEditCard.attr('data-title', title);
				$activeEditCard.attr('data-desc', desc);
				$activeEditCard.find('input[type="checkbox"]').val(type);

				$activeEditCard.find('.oc-sec-tag').text(tag);
				$activeEditCard.find('.oc-sec-title').text(title);
				$activeEditCard.find('.oc-sec-desc').text(desc);
			} else {
				// Create new custom card
				const newCardHtml = `
					<div class="oc-section-toggle-card selected" data-type="${escapeHtml(type)}" data-tag="${escapeHtml(tag)}" data-title="${escapeHtml(title)}" data-desc="${escapeHtml(desc)}">
						<input type="checkbox" name="oc_sections" value="${escapeHtml(type)}" checked>
						<div class="oc-section-toggle-header">
							<span class="oc-sec-tag">${escapeHtml(tag)}</span>
							<div class="oc-card-tools">
								<button type="button" class="oc-card-tool-btn oc-edit-sec-btn" title="Edit Section"><i class="fa-solid fa-pen"></i></button>
								<button type="button" class="oc-card-tool-btn oc-delete-sec-btn" title="Delete Section"><i class="fa-solid fa-trash-can"></i></button>
								<span class="oc-sec-check"><i class="fa-solid fa-check"></i></span>
							</div>
						</div>
						<h4 class="oc-sec-title">${escapeHtml(title)}</h4>
						<p class="oc-sec-desc">${escapeHtml(desc || 'Custom generated section tailored to your business.')}</p>
					</div>
				`;

				$('#oc-add-section-trigger').before(newCardHtml);
			}

			closeModal();
			updateSectionCount();
		});

		function escapeHtml(text) {
			if (!text) return '';
			return $('<div>').text(text).html();
		}

		updateSectionCount();
	}

	/**
	 * Main Generation Stepper & Submission
	 */
	function initGenerator() {
		$('#oc-btn-generate').on('click', function () {
			const title = $('#oc-page-title').val().trim();
			const prompt = $('#oc-prompt').val().trim();
			const targetUrl = $('#oc-target-url').val().trim();
			const builderType = $('#oc-builder-type').val();
			const tone = $('#oc-tone').val();
			const colorPreset = $('input[name="oc_color_preset"]:checked').val() || 'indigo';

			// Collect Selected Section Blueprint Objects
			const selectedSections = [];
			$('.oc-section-toggle-card.selected').each(function () {
				const $card = $(this);
				selectedSections.push({
					type: $card.attr('data-type') || 'custom',
					tag: $card.attr('data-tag') || $card.find('.oc-sec-tag').text(),
					title: $card.attr('data-title') || $card.find('.oc-sec-title').text(),
					description: $card.attr('data-desc') || $card.find('.oc-sec-desc').text(),
				});
			});

			if (!title) {
				alert('Please enter a Page Name / Business Title.');
				$('#oc-page-title').focus();
				return;
			}

			if (!prompt && !targetUrl && !uploadedImageBase64) {
				alert('Please provide at least one input: a business description, reference URL, or design screenshot.');
				return;
			}

			if (selectedSections.length === 0) {
				alert('Please select at least one section to generate.');
				return;
			}

			const $btn = $(this);
			$btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Generating Website...');

			// Show Progress Stepper Card with dynamically highlighted section steps
			$('#oc-result-card').hide();
			$('#oc-progress-card').slideDown();
			resetStepper();

			// Stepper animation timeline
			stepActive('#step-analyze');
			setTimeout(function () {
				stepDone('#step-analyze');
				stepActive('#step-scrape');
			}, 2000);

			setTimeout(function () {
				stepDone('#step-scrape');
				stepActive('#step-copy');
			}, 4500);

			setTimeout(function () {
				stepDone('#step-copy');
				stepActive('#step-compile');
			}, 7000);

			const payload = {
				page_title: title,
				prompt: prompt,
				target_url: targetUrl,
				screenshot_base64: uploadedImageBase64,
				screenshot_mime: uploadedImageMime,
				builder_type: builderType,
				tone: tone,
				color_preset: colorPreset,
				custom_primary: colorPreset === 'custom' ? $('#oc-custom-primary').val() : '',
				custom_secondary: colorPreset === 'custom' ? $('#oc-custom-secondary').val() : '',
				selected_sections: selectedSections,
			};

			$.ajax({
				url: omniCraftData.restUrl + 'generate',
				method: 'POST',
				contentType: 'application/json',
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-WP-Nonce', omniCraftData.nonce);
				},
				data: JSON.stringify(payload),
				timeout: 180000, // 3 minutes
				success: function (res) {
					$btn.prop('disabled', false).html('<i class="fa-solid fa-wand-magic-sparkles"></i> Generate Complete Website');
					stepDone('#step-compile');
					stepActive('#step-publish');

					setTimeout(function () {
						stepDone('#step-publish');
						$('#oc-progress-card').slideUp(300, function () {
							// Show Result Card
							$('#oc-result-page-title').text(res.page_title || title);
							$('#oc-btn-edit-elementor').attr('href', res.elementor_edit_url);
							$('#oc-btn-view-live').attr('href', res.view_url);
							$('#oc-btn-edit-wp').attr('href', res.edit_url);

							if (res.builder_type !== 'elementor') {
								$('#oc-btn-edit-elementor').hide();
							} else {
								$('#oc-btn-edit-elementor').show();
							}

							$('#oc-result-card').slideDown();
						});
					}, 800);
				},
				error: function (xhr) {
					$btn.prop('disabled', false).html('<i class="fa-solid fa-wand-magic-sparkles"></i> Generate Complete Website');
					$('#oc-progress-card').hide();
					const err = xhr.responseJSON ? xhr.responseJSON.message : 'Generation failed. Check your API settings.';
					alert('Generation Error: ' + err);
				}
			});
		});
	}

	/**
	 * Quick Idea Tags
	 */
	function initQuickPrompts() {
		$('.oc-tag-btn').on('click', function () {
			const title = $(this).data('title');
			const prompt = $(this).data('prompt');
			$('#oc-page-title').val(title);
			$('#oc-prompt').val(prompt).focus();
		});
	}

	/**
	 * Tab Navigation in Wizard
	 */
	function initTabs() {
		$('.oc-tab-btn').on('click', function () {
			const target = $(this).data('tab');
			$('.oc-tab-btn').removeClass('active');
			$('.oc-tab-pane').removeClass('active');
			$(this).addClass('active');
			$('#' + target).addClass('active');
		});
	}

	/**
	 * Palette Card Selection & Custom Color Sync
	 */
	function initPalettes() {
		$('.oc-palette-card').on('click', function () {
			$('.oc-palette-card').removeClass('selected');
			$(this).addClass('selected');
			const radio = $(this).find('input[type="radio"]');
			radio.prop('checked', true);

			if (radio.val() === 'custom') {
				$('#oc-custom-color-pickers').slideDown(200);
			} else {
				$('#oc-custom-color-pickers').slideUp(200);
			}
		});

		// Sync color input with text inputs and live swatch
		$('#oc-custom-primary').on('input change', function () {
			const val = $(this).val();
			$('#oc-custom-primary-text').val(val);
			updateCustomSwatch();
		});

		$('#oc-custom-primary-text').on('input change', function () {
			const val = $(this).val();
			if (val.match(/^#[0-9A-Fa-f]{6}$/)) {
				$('#oc-custom-primary').val(val);
				updateCustomSwatch();
			}
		});

		$('#oc-custom-secondary').on('input change', function () {
			const val = $(this).val();
			$('#oc-custom-secondary-text').val(val);
			updateCustomSwatch();
		});

		$('#oc-custom-secondary-text').on('input change', function () {
			const val = $(this).val();
			if (val.match(/^#[0-9A-Fa-f]{6}$/)) {
				$('#oc-custom-secondary').val(val);
				updateCustomSwatch();
			}
		});

		function updateCustomSwatch() {
			const p = $('#oc-custom-primary').val();
			const s = $('#oc-custom-secondary').val();
			$('#oc-custom-swatch').css('background', 'linear-gradient(135deg, ' + p + ' 50%, ' + s + ' 50%)');
		}
	}

	/**
	 * Image Upload & Drag & Drop Handling
	 */
	function initFileUpload() {
		const $dropzone = $('#oc-dropzone');
		const $fileInput = $('#oc-file-input');
		const $previewContainer = $('#oc-image-preview-container');
		const $previewImg = $('#oc-preview-img');
		const $removeBtn = $('#oc-remove-img-btn');
		const $selectMediaBtn = $('#oc-btn-select-media');

		$selectMediaBtn.on('click', function (e) {
			e.stopPropagation();
			$fileInput.trigger('click');
		});

		$dropzone.on('dragover dragenter', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$dropzone.addClass('dragover');
		});

		$dropzone.on('dragleave drop', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$dropzone.removeClass('dragover');
		});

		$dropzone.on('drop', function (e) {
			const files = e.originalEvent.dataTransfer.files;
			if (files && files.length > 0) {
				handleFile(files[0]);
			}
		});

		$fileInput.on('change', function () {
			if (this.files && this.files.length > 0) {
				handleFile(this.files[0]);
			}
		});

		$removeBtn.on('click', function (e) {
			e.stopPropagation();
			uploadedImageBase64 = '';
			uploadedImageMime = '';
			$previewImg.attr('src', '');
			$previewContainer.hide();
			$('.oc-dropzone-inner').show();
			$fileInput.val('');
		});

		function handleFile(file) {
			if (!file.type.match('image.*')) {
				alert('Please select an image file (PNG, JPG, WEBP).');
				return;
			}

			if (file.size > 5 * 1024 * 1024) {
				alert('File size exceeds 5MB limit.');
				return;
			}

			uploadedImageMime = file.type;
			const reader = new FileReader();
			reader.onload = function (e) {
				const fullData = e.target.result;
				$previewImg.attr('src', fullData);
				$('.oc-dropzone-inner').hide();
				$previewContainer.show();

				// Strip data:image/...;base64,
				const base64Data = fullData.split(',')[1];
				uploadedImageBase64 = base64Data;
			};
			reader.readAsDataURL(file);
		}
	}

	/**
	 * Reference URL Scraping
	 */
	function initScraper() {
		$('#oc-btn-scrape-preview').on('click', function () {
			const url = $('#oc-target-url').val().trim();
			if (!url) {
				alert('Please enter a website URL.');
				return;
			}

			const $btn = $(this);
			const origHtml = $btn.html();
			$btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Analyzing...');

			$.ajax({
				url: omniCraftData.restUrl + 'scrape-url',
				method: 'POST',
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-WP-Nonce', omniCraftData.nonce);
				},
				data: { url: url },
				success: function (res) {
					$btn.prop('disabled', false).html(origHtml);
					if (res.success && res.data) {
						const d = res.data;
						$('#oc-scraped-title').text(d.title || url);
						$('#oc-scraped-desc').text(d.meta_description || 'No description found.');
						
						let tagsHtml = '';
						if (d.detected_sections && d.detected_sections.length) {
							d.detected_sections.forEach(function (sec) {
								tagsHtml += '<span class="oc-badge-pill"><i class="fa-solid fa-layer-group"></i> ' + sec + '</span> ';
							});
						}
						if (d.color_hints && d.color_hints.length) {
							d.color_hints.forEach(function (col) {
								tagsHtml += '<span class="oc-badge-pill" style="background:' + col + '; color:#fff;">' + col + '</span> ';
							});
						}
						$('#oc-scraped-tags').html(tagsHtml);
						$('#oc-scraped-info').slideDown();
					}
				},
				error: function (xhr) {
					$btn.prop('disabled', false).html(origHtml);
					const err = xhr.responseJSON ? xhr.responseJSON.message : 'Scraping failed.';
					alert('Scraping Error: ' + err);
				}
			});
		});
	}

	/**
	 * History Table Listing & Delete Handlers
	 */
	function initHistory() {
		const $tbody = $('#oc-history-tbody');
		if (!$tbody.length) return;

		loadHistory();

		function loadHistory() {
			$.ajax({
				url: omniCraftData.restUrl + 'history?limit=50',
				method: 'GET',
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-WP-Nonce', omniCraftData.nonce);
				},
				success: function (res) {
					if (res.success && res.history && res.history.length > 0) {
						let rows = '';
						res.history.forEach(function (item) {
							const builderBadge = item.builder_type === 'elementor' 
								? '<span class="oc-badge-pill" style="background:#fce7f3;color:#be185d;"><i class="fa-brands fa-elementor"></i> Elementor</span>'
								: '<span class="oc-badge-pill" style="background:#e0e7ff;color:#4338ca;"><i class="fa-brands fa-wordpress"></i> Gutenberg</span>';

							const inputBadge = '<span class="oc-badge-pill">' + item.input_type.toUpperCase() + '</span>';

							let actions = '';
							if (item.page_status !== 'deleted') {
								if (item.elementor_edit_url && item.builder_type === 'elementor') {
									actions += '<a href="' + item.elementor_edit_url + '" class="oc-btn oc-btn-sm oc-btn-primary" target="_blank" style="margin-right:6px;"><i class="fa-solid fa-pen-ruler"></i> Edit</a>';
								}
								actions += '<a href="' + item.page_url + '" class="oc-btn oc-btn-sm oc-btn-secondary" target="_blank" style="margin-right:6px;"><i class="fa-solid fa-eye"></i> View</a>';
							} else {
								actions += '<span style="color:#94a3b8;font-size:12px;">(Page Trashed)</span> ';
							}

							actions += '<button type="button" class="oc-btn oc-btn-sm oc-btn-outline-danger oc-btn-del-history" data-id="' + item.id + '"><i class="fa-solid fa-trash"></i></button>';

							rows += '<tr>' +
								'<td><strong>' + escapeHtml(item.page_title) + '</strong></td>' +
								'<td>' + builderBadge + '</td>' +
								'<td>' + inputBadge + '</td>' +
								'<td>' + escapeHtml(item.model || item.provider) + '</td>' +
								'<td>' + escapeHtml(item.created_at) + '</td>' +
								'<td>' + actions + '</td>' +
								'</tr>';
						});
						$tbody.html(rows);
					} else {
						$tbody.html('<tr><td colspan="6" class="oc-text-center" style="padding:30px;color:#64748b;">No generation history found. Click "Create New Page" to generate your first website!</td></tr>');
					}
				},
				error: function () {
					$tbody.html('<tr><td colspan="6" class="oc-text-center" style="color:#ef4444;">Failed to load generation history.</td></tr>');
				}
			});
		}

		// Delete single history item
		$(document).on('click', '.oc-btn-del-history', function () {
			if (!confirm('Are you sure you want to remove this log record? (The WordPress page will remain intact).')) return;
			const id = $(this).data('id');
			const $row = $(this).closest('tr');

			$.ajax({
				url: omniCraftData.restUrl + 'history/' + id,
				method: 'DELETE',
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-WP-Nonce', omniCraftData.nonce);
				},
				success: function () {
					$row.fadeOut(300, function () { $(this).remove(); });
				},
				error: function () {
					alert('Failed to delete history item.');
				}
			});
		});

		// Clear all history
		$('#oc-btn-clear-history').on('click', function () {
			if (!confirm('Are you sure you want to clear all history records?')) return;
			$.ajax({
				url: omniCraftData.restUrl + 'history/clear',
				method: 'DELETE',
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-WP-Nonce', omniCraftData.nonce);
				},
				success: function () {
					loadHistory();
				},
				error: function () {
					alert('Failed to clear history.');
				}
			});
		});
	}

	function escapeHtml(text) {
		if (!text) return '';
		return $('<div>').text(text).html();
	}

})(jQuery);
