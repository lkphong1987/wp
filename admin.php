<?php
//grab our display options
$display_options = get_option('wp_cp_display_options');

//secondary form tab specific actions
if(!empty($_GET['tab'])) {
	if($_GET['tab'] == 'display') {
		//restore default display options
		if(!empty($_POST['restore'])) {
			$defaults = wp_cp_default_display_options();
			if(!empty($defaults)) {
				update_option("wp_cp_display_options", $defaults);
			}
		}
	} 
	elseif($_GET['tab'] == 'styling') {
		//restore default styling options
		if(!empty($_POST['restore'])) {
			$defaults = wp_cp_default_styling_options();
			if(!empty($defaults)) {
				update_option("wp_cp_styling_options", $defaults);
			}
		}
	} 
	elseif($_GET['tab'] == 'extra') {
		//restore default styling options
		if(!empty($_POST['restore'])) {
			$defaults = wp_cp_default_extra_options();
			if(!empty($defaults)) {
				update_option("wp_cp_extra_options", $defaults);
			}
		}
	} 
}
?>
<div class="wrap wp-cp-admin">

	<?php 
	//if no tab is set  yet, default to the first one, display options
	if(empty($_GET['tab'])) {
		$_GET['tab'] = 'display';
	} 
	?>

	<!--hidden h2 for admin notice placement-->
	<h2 style='display: none;'></h2>
	
		<!-- Tab Navigation -->
		<h2 class="nav-tab-wrapper">
			<a href="?page=wp_cp&tab=display" class="nav-tab <?php echo $_GET['tab'] == 'display' ? 'nav-tab-active' : ''; ?>"><?php _e('Display Options', 'wp_cp'); ?></a>
			<a href="?page=wp_cp&tab=styling" class="nav-tab <?php echo $_GET['tab'] == 'styling' ? 'nav-tab-active' : ''; ?>"><?php _e('Style Options', 'wp_cp'); ?></a>
			<a href="?page=wp_cp&tab=extra" class="nav-tab <?php echo $_GET['tab'] == 'extra' ? 'nav-tab-active' : ''; ?>"><?php _e('Extras', 'wp_cp'); ?></a>
			<a href="?page=wp_cp&tab=license" class="nav-tab <?php echo $_GET['tab'] == 'license' ? 'nav-tab-active' : ''; ?>"><?php _e('License', 'wp_cp'); ?></a>
			<?php if(!empty($display_options['wp_cp_enable_archive']) && $display_options['wp_cp_enable_archive'] == "1") { ?>
				<?php if(!empty($display_options['wp_cp_archive_slug'])){$wp_cp_slug = $display_options['wp_cp_archive_slug'];}else{$wp_cp_slug = 'cp';} ?>
				<?php $wp_cp_slug = site_url() . "/" . $wp_cp_slug . "/"; ?>
				<a href="<?php echo $wp_cp_slug; ?>" class="nav-tab" target="_blank" style="color: #0073aa;" title="<?php _e('Coupon Archive', 'wp_cp'); ?>"><?php echo $wp_cp_slug; ?></a>
			<?php } ?>
		</h2>
		
		<!-- Main Form -->
		<form method="post" action="options.php" id="wp-cp-options-form" <?php echo $_GET['tab'] == 'extra' ? "enctype='multipart/form-data'" : ""; ?>>
			<?php
			if($_GET['tab'] == 'display') {
				settings_fields( 'wp_cp_display_options' );
				do_settings_sections('wp_cp_display_options', 'wp_cp_display_settings');
				submit_button();
			}
			elseif($_GET['tab'] == 'styling') {
				echo "<input type='hidden' name='section' id='subnav-section' />";
				echo "<div class='wp-cp-subnav'>";
					echo "<a href='#styling-colors' id='colors-section' rel='colors' class='active'><span class='dashicons dashicons-art'></span>" . __('Colors', 'wp_cp') . "</a>";
					echo "<a href='#styling-fonts' id='fonts-section' rel='fonts'><span class='dashicons dashicons-edit'></span>" . __('Fonts', 'wp_cp') . "</a>";
				echo "</div>";
				settings_fields('wp_cp_styling_options');
				echo "<section id='styling-colors' class='section-content active'>";
					wp_cp_section('wp_cp_styling_options', 'wp_cp_colors');
				echo "</section>";
				echo "<section id='styling-fonts' class='section-content hide'>";
					wp_cp_section('wp_cp_styling_options', 'wp_cp_fonts');
				echo "</section>";
				submit_button();
			} 
			elseif($_GET['tab'] == 'extra') {
				echo "<input type='hidden' name='section' id='subnav-section' />";
				echo "<div class='wp-cp-subnav'>";
					echo "<a href='#extra-general' id='general-section' rel='general' class='active'><span class='dashicons dashicons-dashboard'></span>" . __('General', 'wp_cp') . "</a>";
					echo "<a href='#extra-tools' id='tools-section' rel='tools'><span class='dashicons dashicons-admin-tools'></span>" . __('Tools', 'wp_cp') . "</a>";
				echo "</div>";
				settings_fields( 'wp_cp_extra_options' );
				echo "<section id='extra-general' class='section-content active'>";
					wp_cp_section('wp_cp_extra_options', 'wp_cp_extra_general');
				echo "</section>";
				echo "<section id='extra-tools' class='section-content hide'>";
					wp_cp_section('wp_cp_extra_options', 'wp_cp_extra_tools');
				echo "</section>";
				//do_settings_sections( 'wp_cp_extra_options' );
				submit_button();
			}
			elseif($_GET['tab'] == 'license') {
				$license_info = wp_cp_edd_check_license();
				$license = get_option('wp_cp_edd_license_key');
				$status = get_option('wp_cp_edd_license_status');
				settings_fields('wp_cp_edd_license');

				?>

				<table class="form-table">
					<tbody>
						<tr>
							<th><?php _e('License Key', 'wp_cp'); ?></th>
							<td>
								<input id="wp_cp_edd_license_key" name="wp_cp_edd_license_key" type="password" class="regular-text" value="<?php esc_attr_e($license); ?>" />
								<label class="description" for="wp_cp_edd_license_key"><?php _e('Enter your license key', 'wp_cp'); ?></label>
							</td>
						</tr>
						<?php if( false !== $license ) { ?>
							<tr>
								<th><?php _e('Activate License', 'wp_cp'); ?></th>
								<td>
									<?php if( $status !== false && $status == 'valid' ) { ?>
										<?php wp_nonce_field( 'wp_cp_edd_nonce', 'wp_cp_edd_nonce' ); ?>
										<input type="submit" class="button-secondary" name="wp_cp_edd_license_deactivate" value="<?php _e('Deactivate License', 'wp_cp'); ?>"/>
										<span style="color:green; display: block; margin-top: 10px;"><?php _e('License is activated.', 'wp_cp'); ?></span>
									<?php } else {
										wp_nonce_field( 'wp_cp_edd_nonce', 'wp_cp_edd_nonce' ); ?>
										<input type="submit" class="button-secondary" name="wp_cp_edd_license_activate" value="<?php _e('Activate License', 'wp_cp'); ?>"/>
										<span style="color:red; display: block; margin-top: 10px;"><?php _e('License is not activated.', 'wp_cp'); ?></span>
									<?php } ?>
								</td>
							</tr>
							<?php if(!empty($license_info)) { ?>

								<!-- Customer Email Address -->
								<?php if(!empty($license_info->customer_email)) { ?>
									<tr>
										<th><?php _e('Customer Email', 'wp_cp'); ?></th>
										<td><?php echo $license_info->customer_email; ?></td>
									</tr>
								<?php } ?>

								<!-- License Status (Active/Expired) -->
								<?php if(!empty($license_info->license)) { ?>
									<tr>
										<th><?php _e('License Status', 'wp_cp'); ?></th>
										<td <?php if($license_info->license == "expired"){echo "style='color: red;'";} ?>>
											<?php echo $license_info->license; ?>
											<?php if(!empty($license) && $license_info->license == "expired") { ?>
												<br /><a href="https://wpcp.io/checkout/?edd_license_key=<?php echo $license; ?>&download_id=2303" class="button-primary" style="margin-top: 10px;" target="_blank"><?php _e('Renew Your License for Updates + Support!', 'wp_cp'); ?></a>
											<?php } ?>
										</td>
									</tr>
								<?php } ?>

								<!-- Licenses Used -->
								<?php if(!empty($license_info->site_count) && !empty($license_info->license_limit)) { ?>
									<tr>
										<th><?php _e('Licenses Used', 'wp_cp'); ?></th>
										<td><?php echo $license_info->site_count . "/" . $license_info->license_limit; ?></td>
									</tr>
								<?php } ?>

							<?php } ?>

						<?php } ?>
					</tbody>
				</table>
				<?php 
				if($license === false) {
					submit_button(__('Save License', 'wp_cp'));
				}
			}
			?>
		</form>

		<!-- secondary form used to reset default settings per tab -->
		<?php if($_GET['tab'] != 'license') { ?>

			<form method="post" action="" id="wp-cp-restore" onsubmit="return confirm('<?php _e('Restore default settings?', 'wp_cp'); ?>');">
				<input type='submit' id='restore' name='restore' class='button button-secondary' value='<?php _e('Restore Defaults', 'wp_cp'); ?>'>
			</form>

			<div id="wp-cp-legend">
				<div id="wp-cp-tooltip-legend">
					<span>?</span><?php _e('Click on tooltip icons to view full documentation.', 'wp_cp'); ?>
				</div>
			</div>

		<?php } ?>
</div>
