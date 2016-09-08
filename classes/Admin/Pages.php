<?php

// Exit if accessed directly
namespace RCPM\Admin;

use RCPM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pages {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_pages' ), 999 );
		add_action( 'admin_init', array( __CLASS__, 'admin_redirect' ) );
	}

	public static function register_pages() {
		global $rcpm_settings_page, $rcpm_tools_page, $rcpm_addons_page;

		$rcpm_settings_page = add_submenu_page(
			'edit.php?post_type=recipe',
			__( 'Settings', 'recipe-manager' ),
			__( 'Settings', 'recipe-manager' ),
			'manage_options', 'rcpm-settings',
			array( __CLASS__, 'settings_page' )
		);

		$rcpm_tools_page = add_submenu_page(
			'edit.php?post_type=recipe',
			__( 'Tools', 'recipe-manager' ),
			__( 'Tools', 'recipe-manager' ),
			'manage_options',
			'rcpm-tools',
			array( __CLASS__, 'tools_page' )
		);

		$rcpm_addons_page = add_submenu_page(
			'edit.php?post_type=recipe',
			__( 'Addons', 'recipe-manager' ),
			__( 'Addons', 'recipe-manager' ),
			'manage_options',
			'rcpm-addons',
			array( __CLASS__, 'addons_page' )
		);

	}

	public static function settings_page() {
		$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], rcpm_get_settings_tabs() ) ? $_GET['tab'] : false;

		if ( ! $active_tab ) {
			foreach ( rcpm_get_settings_tabs() as $key => $value ) {
				if ( ! empty( $value ) ) {
					$active_tab = $key;
					break;
				}
			}
		}

		ob_start(); ?>
		<div class="wrap">
		<h2><?php esc_html_e( __( 'Recipe Manager Settings', 'recipe-manager' ) ); ?></h2>

		<h2 id="rcpm-tabs" class="nav-tab-wrapper"><?php
			foreach ( rcpm_get_settings_tabs() as $tab_id => $tab_name ) {

				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab'              => $tab_id,
				) );

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
				echo esc_html( $tab_name );
				echo '</a>';
			} ?>
		</h2>

		<form id="rcpm-settings-editor" method="post" action="options.php">
			<?php do_action( 'rcpm_form_nonce' ); ?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-1">
					<div id="post-body-content">
						<div id="tab_container">
							<table class="form-table"><?php
								settings_fields( 'rcpm_settings' );
								do_settings_fields( 'rcpm_settings_' . $active_tab, 'rcpm_settings_' . $active_tab ); ?>
							</table>
							<?php submit_button(); ?>
						</div>
						<!-- #tab_container-->
					</div>
				</div>
				<br class="clear" />
			</div>
		</form>
		</div><?php
		echo ob_get_clean();
	}

	public static function tools_page() {
		$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], rcpm_get_tools_tabs() ) ? $_GET['tab'] : 'import';
		ob_start(); ?>
		<div class="wrap">
		<h2><?php esc_html_e( __( 'Recipe Manager Tools', 'recipe-manager' ) ); ?></h2>
		<?php if ( isset( $_GET['imported'] ) ) : ?>
			<div class="updated">
				<p><?php _e( 'Successfully Imported your themes &amp; modals from ZipList.' ); ?></p>
			</div>
		<?php endif; ?>
		<h2 id="rcpm-tabs" class="nav-tab-wrapper"><?php
			foreach ( rcpm_get_tools_tabs() as $tab_id => $tab_name ) {

				$tab_url = add_query_arg( array(
					'tools-updated' => false,
					'tab'           => $tab_id,
				) );

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
				echo esc_html( $tab_name );
				echo '</a>';
			} ?>
		</h2>

		<form id="rcpm-tools-editor" method="post" action="">
			<?php do_action( 'rcpm_form_nonce' ); ?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-1">
					<div id="post-body-content">
						<div id="tab_container">
							<?php do_action( 'rcpm_tools_page_tab_' . $tab_id ); ?>
						</div>
						<!-- #tab_container-->
					</div>
				</div>
				<br class="clear" />
			</div>
		</form>
		</div><?php
		echo ob_get_clean();
	}

	public static function addons_page() {
		ob_start(); ?>
		<div class="wrap">
		<h2><?php _e( 'Recipe Manager Addons', 'recipe-manager' ) ?></h2>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content"><?php
					$addons = rcpm_available_addons(); ?>
					<ul class="addons-available">
						<?php
						$plugins           = get_plugins();
						$installed_plugins = array();
						foreach ( $plugins as $key => $plugin ) {
							$is_active                          = is_plugin_active( $key );
							$installed_plugin                   = array(
								'is_active' => $is_active,
							);
							$installerUrl                       = add_query_arg( array(
								'action' => 'activate',
								'plugin' => $key,
								'em'     => 1,
							), network_admin_url( 'plugins.php' ) //admin_url('update.php')
							);
							$installed_plugin["activation_url"] = $is_active ? "" : wp_nonce_url( $installerUrl, 'activate-plugin_' . $key );


							$installerUrl                         = add_query_arg( array(
								'action' => 'deactivate',
								'plugin' => $key,
								'em'     => 1,
							), network_admin_url( 'plugins.php' ) //admin_url('update.php')
							);
							$installed_plugin["deactivation_url"] = ! $is_active ? "" : wp_nonce_url( $installerUrl, 'deactivate-plugin_' . $key );
							$installed_plugins[ $key ]            = $installed_plugin;
						}

						$existing_addon_images = apply_filters( 'rcpm_existing_addon_images', array() );
						if ( ! empty( $addons ) ) {
							foreach ( $addons as $addon ) :?>
								<li class="available-addon-inner">
									<h3>
										<a target="_blank" href="<?php esc_attr_e( $addon['homepage'] ); ?>?utm_source=Plugin+Admin&amp;utm_medium=Addons+Page+Addon+Names&amp;utm_campaign=<?php esc_attr_e( str_replace( ' ', '+', $addon['name'] ) ); ?>">
											<?php esc_html_e( $addon['name'] ) ?>
										</a>
									</h3>
									<?php $image = in_array( $addon['slug'], $existing_addon_images ) ? RCPM::$URL . '/assets/images/addons/' . $addon['slug'] . '.png' : $addon['image']; ?>
									<img class="addon-thumbnail" src="<?php esc_attr_e( $image ) ?>">
									<!--
										<p><?php esc_html_e( $addon['excerpt'] ) ?></p>
										<hr/>
										 -->
									<?php
									/*
									if(!empty($addon->download_link) && !isset($installed_plugins[$addon->slug.'/'.$addon->slug.'.php']))
									{
										$installerUrl = add_query_arg(
											array(
												'action' => 'install-plugin',
												'plugin' => $addon->slug,
												'edd_sample_plugin' => 1
											),
											network_admin_url('update.php')
											//admin_url('update.php')
										);
										$installerUrl = wp_nonce_url($installerUrl, 'install-plugin_' . $addon->slug)?>
										<span class="action-links"><?php
										printf(
											'<a class="button install" href="%s">%s</a>',
											esc_attr($installerUrl),
											__('Install')
										);?>
										</span><?php
									}
									elseif(isset($installed_plugins[$addon->slug.'/'.$addon->slug.'.php']['is_active']))
									{?>
										<span class="action-links"><?php
											if(!$installed_plugins[$addon->slug.'/'.$addon->slug.'.php']['is_active'])
											{
												printf(
													'<a class="button install" href="%s">%s</a>',
													esc_attr($installed_plugins[$addon->slug.'/'.$addon->slug.'.php']["activation_url"]),
													__('Activate')
												);

											}
											else
											{
												printf(
													'<a class="button install" href="%s">%s</a>',
													esc_attr($installed_plugins[$addon->slug.'/'.$addon->slug.'.php']["deactivation_url"]),
													__('Deactivate')
												);
											}?>
										</span><?php
									}
									else
									{
										?><span class="action-links"><a class="button" target="_blank" href="<?php esc_attr_e($addon->homepage);?>"><?php _e('Get It Now');?></a></span><?php
									}
									*/
									?>
									<span class="action-links">
						                    <a class="button" target="_blank" href="<?php esc_attr_e( $addon['homepage'] ); ?>?utm_source=Plugin+Admin&amp;utm_medium=Addons+Page+Addon+Buttons&amp;utm_campaign=<?php esc_attr_e( str_replace( ' ', '+', $addon['name'] ) ); ?>"><?php _e( 'Learn More', 'recipe-manager' ); ?></a>
						                </span>
								</li>
							<?php endforeach;
						} ?>
					</ul>
				</div>
			</div>
			<br class="clear" />
		</div>
		</div><?php
		echo ob_get_clean();
	}

	public static function admin_redirect() {
		$redirect = get_option( 'rcpm_activation_redirect', false );
		if ( $redirect ) {
			delete_option( 'rcpm_activation_redirect' );
			wp_redirect( $redirect );
			exit;
		}
	}

}
