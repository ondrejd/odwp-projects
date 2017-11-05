<?php
/**
 * Meta box for project's slug.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License 2.0
 * @link https://github.com/ondrejd/odwp-projects for the canonical source repository
 * @package odwp-projects
 * @since 0.1.0
 */
?><div class="project_slug_metabox">
	<input type="hidden" name="<?=Odwpp_Project_Status_Metabox::NONCE?>" value="<?=$nonce?>">
	<label for="odwpp-project_slug" class="screen-reader-text"><?php _e( 'Systémový  název projektu:', ODWPP_SLUG )?></label>
	<input type="text" name="project_slug" id="odwpp-project_slug" value="<?=$value?>">
    <p class="description"><?php _e( 'Měl by odpovídat trvalému odkazu a zároveň i názvu repozitáře (např. <strong>odwp-projects</strong>).', ODWPP_SLUG )?></p>
</div>
