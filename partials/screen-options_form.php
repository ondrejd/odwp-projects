<?php
/**
 * Screen options form.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License 2.0
 * @link https://github.com/ondrejd/odwp-projects for the canonical source repository
 * @package odwp-projects
 * @since 1.0
 */
?><div id="screen-options-wrap" class="hidden" aria-label="<?php esc_html_e( 'Screen Options Tab', Odwp_Projects_Plugin::SLUG ); ?>">
  <form name="<?php echo $slug; ?>-form" method="post">
    <?php echo wp_nonce_field( -1, $slug . '-nonce', true, false); ?>
    <input type="hidden" name="screen_name" value="<?php echo $screen->id; ?>">
    <fieldset>
      <legend><?php esc_html_e( 'Display detail description', Odwp_Projects_Plugin::SLUG ); ?></legend>
      <label for="<?php echo $slug; ?>-checkbox1" title="<?php esc_html_e( 'Show detail description by default.', Odwp_Projects_Plugin::SLUG ); ?>">
        <input type="checkbox" name="<?php echo $slug; ?>-checkbox1" id="<?php echo $slug; ?>-checkbox1" <?php checked( $display_description ); ?>>
        <?php esc_html_e( 'Check if you want see detailed description.', Odwp_Projects_Plugin::SLUG ); ?>
      </label>
    </fieldset>
    <p class="submit">
      <input type="submit" name="<?php echo $slug; ?>-submit" value="<?php esc_html_e( 'Apply', Odwp_Projects_Plugin::SLUG ); ?>" class="button button-primary">
    </p>
  </form>
</div>
