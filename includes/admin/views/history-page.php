<?php
/**
 * Generation History & Audit Log View.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$brand_name = ! empty( $options['brand_name'] ) ? $options['brand_name'] : 'AI Site Builder';
$brand_logo = ! empty( $options['brand_logo_url'] ) ? $options['brand_logo_url'] : '';
?>

<div class="wrap omnicraft-wrap">
	<!-- Top Bar -->
	<header class="omnicraft-header">
		<div class="omnicraft-brand">
			<?php if ( ! empty( $brand_logo ) ) : ?>
				<img src="<?php echo esc_url( $brand_logo ); ?>" alt="<?php echo esc_attr( $brand_name ); ?>" class="omnicraft-logo">
			<?php else : ?>
				<div class="omnicraft-brand-icon">
					<i class="fa-solid fa-clock-rotate-left"></i>
				</div>
			<?php endif; ?>
			<div class="omnicraft-brand-text">
				<h1><?php esc_html_e( 'Generation History & Logs', 'omnicraft-ai-builder' ); ?></h1>
				<p><?php esc_html_e( 'Audit trail of all AI generated landing pages and website drafts.', 'omnicraft-ai-builder' ); ?></p>
			</div>
		</div>

		<div class="omnicraft-header-meta">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=omnicraft-ai-builder' ) ); ?>" class="oc-btn oc-btn-primary">
				<i class="fa-solid fa-plus"></i> <?php esc_html_e( 'Create New Page', 'omnicraft-ai-builder' ); ?>
			</a>
			<?php if ( current_user_can( 'manage_options' ) ) : ?>
				<button type="button" id="oc-btn-clear-history" class="oc-btn oc-btn-outline-danger">
					<i class="fa-solid fa-trash-can"></i> <?php esc_html_e( 'Clear All History', 'omnicraft-ai-builder' ); ?>
				</button>
			<?php endif; ?>
		</div>
	</header>

	<!-- History Table Card -->
	<div class="omnicraft-main-card">
		<div class="oc-table-responsive">
			<table class="oc-table" id="oc-history-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Page Title', 'omnicraft-ai-builder' ); ?></th>
						<th><?php esc_html_e( 'Builder', 'omnicraft-ai-builder' ); ?></th>
						<th><?php esc_html_e( 'Input Source', 'omnicraft-ai-builder' ); ?></th>
						<th><?php esc_html_e( 'AI Provider / Model', 'omnicraft-ai-builder' ); ?></th>
						<th><?php esc_html_e( 'Created Date', 'omnicraft-ai-builder' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'omnicraft-ai-builder' ); ?></th>
					</tr>
				</thead>
				<tbody id="oc-history-tbody">
					<tr>
						<td colspan="6" class="oc-text-center">
							<div class="oc-spinner" style="margin:20px auto;"></div>
							<p><?php esc_html_e( 'Loading generation history...', 'omnicraft-ai-builder' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
