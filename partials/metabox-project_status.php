<?php
/**
 * Meta box for project's links.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License 2.0
 * @link https://github.com/ondrejd/odwp-projects for the canonical source repository
 * @package odwp-projects
 * @since 1.0
 */
?><div class="project_status_metabox">
	<input type="hidden" name="<?php echo Odwpp_Project_Status_Metabox::NONCE; ?>" value="<?php echo $nonce; ?>">
	<p>
		<?php _e( 'Určete stav projektu:', Odwp_Projects_Plugin::SLUG ); ?>
		<select name="project_status" id="odwpp-project_status" value="<?php echo $value; ?>">
			<option value="active"<?php selected( 'active', $value ); ?>><?php _e( 'Aktivní', Odwp_Projects_Plugin::SLUG ); ?></option>
			<option value="nonactive"<?php selected( 'nonactive', $value ); ?>><?php _e( 'Neaktivní', Odwp_Projects_Plugin::SLUG ); ?></option>
			<option value="finished"<?php selected( 'finished', $value ); ?>><?php _e( 'Dokončený', Odwp_Projects_Plugin::SLUG ); ?></option>
			<option value="cancelled"<?php selected( 'cancelled', $value ); ?>><?php _e( 'Zrušený', Odwp_Projects_Plugin::SLUG ); ?></option>
		</select>
	</p>
</div>
