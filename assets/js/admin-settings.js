/**
 * OmniCraft AI Builder - Admin Settings JS
 */

(function ($) {
	'use strict';

	$(document).ready(function () {
		initSettingsTabs();
		initLogoUploader();
		initProviderTesters();
	});

	/**
	 * Settings Tab Navigation
	 */
	function initSettingsTabs() {
		$('.oc-settings-tab-btn').on('click', function () {
			const tabId = $(this).data('tab');
			$('.oc-settings-tab-btn').removeClass('active');
			$('.oc-settings-tab-content').removeClass('active');

			$(this).addClass('active');
			$('#' + tabId).addClass('active');
		});
	}

	/**
	 * WordPress Media Uploader for Custom Brand Logo
	 */
	function initLogoUploader() {
		let mediaFrame;

		$('#oc-upload-logo-btn').on('click', function (e) {
			e.preventDefault();

			if (mediaFrame) {
				mediaFrame.open();
				return;
			}

			mediaFrame = wp.media({
				title: 'Select or Upload Brand Logo',
				button: {
					text: 'Use this logo'
				},
				multiple: false
			});

			mediaFrame.on('select', function () {
				const attachment = mediaFrame.state().get('selection').first().toJSON();
				$('#brand_logo_url').val(attachment.url);
			});

			mediaFrame.open();
		});
	}

	/**
	 * Dynamic Test Connection for all Providers
	 */
	function initProviderTesters() {
		$('.oc-btn-test-provider').on('click', function () {
			const provider = $(this).data('provider');
			const $btn = $(this);
			const origHtml = $btn.html();

			let apiKey = '';
			let model = '';
			let endpoint = '';

			if (provider === 'openai') {
				apiKey = $('#openai_api_key').val().trim();
				model = $('#openai_model').val();
			} else if (provider === 'anthropic') {
				apiKey = $('#anthropic_api_key').val().trim();
				model = $('#anthropic_model').val();
			} else if (provider === 'gemini') {
				apiKey = $('#gemini_api_key').val().trim();
				model = $('#gemini_model').val();
			} else if (provider === 'openrouter') {
				apiKey = $('#openrouter_api_key').val().trim();
				model = $('#openrouter_model').val().trim();
			} else if (provider === 'custom') {
				endpoint = $('#custom_endpoint').val().trim();
				apiKey = $('#custom_api_key').val().trim();
				model = $('#custom_model').val().trim();
			}

			$btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Testing...');

			$.ajax({
				url: omniCraftData.restUrl + 'test-connection',
				method: 'POST',
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-WP-Nonce', omniCraftData.nonce);
				},
				data: {
					provider: provider,
					api_key: apiKey,
					model: model,
					endpoint: endpoint
				},
				success: function (res) {
					$btn.prop('disabled', false).html(origHtml);
					if (res.success) {
						alert('✓ ' + (res.message || 'Connection successful!'));
					} else {
						alert('✗ ' + (res.message || 'Connection failed.'));
					}
				},
				error: function (xhr) {
					$btn.prop('disabled', false).html(origHtml);
					const err = xhr.responseJSON ? xhr.responseJSON.message : 'Connection test failed.';
					alert('✗ ' + err);
				}
			});
		});
	}

})(jQuery);
