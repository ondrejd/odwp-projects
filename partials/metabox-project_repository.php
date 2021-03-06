<?php
/**
 * Meta box for project's repository.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License 2.0
 * @link https://github.com/ondrejd/odwp-projects for the canonical source repository
 * @package odwp-projects
 * @since 0.1.0
 */
?><div class="project_repository_metabox">
	<input type="hidden" name="<?=Odwpp_Project_Status_Metabox::NONCE?>" value="<?=$nonce?>">
	<label for="odwpp-project_repository" class="screen-reader-text"><?php _e( 'URL repozitáře:', ODWPP_SLUG )?></label>
	<input type="url" name="project_repository" id="odwpp-project_repository" value="<?=$value?>">
    <p class="description"><?php printf( __( 'Vložte URL repozitáře (např. <a href="%1$s" target="_blank">%1$s</a>).', ODWPP_SLUG ), 'https://github.com/ondrejd/odwp-projects' )?></p>
</div>
