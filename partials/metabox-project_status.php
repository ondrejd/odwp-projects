<?php
/**
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License 2.0
 * @link https://github.com/ondrejd/odwp-projects for the canonical source repository
 * @package odwp-projects
 * @since 0.1.0
 */

?><div class="project_status_metabox">
	<input type="hidden" name="<?=Odwpp_Project_Status_Metabox::NONCE?>" value="<?=$nonce; ?>">
	<label for="odwpp-project_status" class="screen-reader-text"><?php _e( 'Stav projektu:', ODWPP_SLUG )?></label>
	<select name="project_status" id="odwpp-project_status" value="<?=$value; ?>">
		<option value="active"<?php selected( 'active', $value )?>><?php _e( 'Aktivní', ODWPP_SLUG )?></option>
		<option value="nonactive"<?php selected( 'nonactive', $value )?>><?php _e( 'Neaktivní', ODWPP_SLUG )?></option>
		<option value="finished"<?php selected( 'finished', $value )?>><?php _e( 'Dokončený', ODWPP_SLUG )?></option>
		<option value="cancelled"<?php selected( 'cancelled', $value )?>><?php _e( 'Zrušený', ODWPP_SLUG )?></option>
	</select>
</div>
