<?php

$attachment_id       = bp_get_document_attachment_id();
$extension           = '';
$can_download_btn    = false;
$can_manage_btn      = false;
$can_view            = false;
$attachment_url      = '';
$text_attachment_url = '';
$move_id             = '';
$move_type           = '';
$folder_link         = '';
$document_id         = bp_get_document_id();
$filename            = basename( get_attached_file( $attachment_id ) );
if ( $attachment_id ) {
	$extension           = bp_document_extension( $attachment_id );
	$svg_icon            = bp_document_svg_icon( $extension );
	$download_link       = bp_document_download_link( $attachment_id, $document_id );
	$text_attachment_url = wp_get_attachment_url( $attachment_id );
	$move_class          = 'ac-document-move';
	$listing_class       = 'ac-document-list';
	$document_type       = 'document';
	$document_privacy    = bp_document_user_can_manage_document( bp_get_document_id(), bp_loggedin_user_id() );
	$can_download_btn    = ( true === (bool) $document_privacy['can_download'] ) ? true : false;
	$can_manage_btn      = ( true === (bool) $document_privacy['can_manage'] ) ? true : false;
	$can_view            = ( true === (bool) $document_privacy['can_view'] ) ? true : false;
	$group_id            = bp_get_document_group_id();
	// $document_title      = basename( get_attached_file( $attachment_id ) );
	$document_title = bp_get_document_title();
	$data_action    = 'document';

	if ( $group_id > 0 ) {
		$move_id   = $group_id;
		$move_type = 'group';
	} else {
		$move_id   = bp_get_document_user_id();
		$move_type = 'profile';
	}

	if ( in_array( $extension, bp_get_document_preview_doc_extensions(), true ) ) {
		$attachment_url = wp_get_attachment_url( bp_get_document_preview_attachment_id() );
	}
} else {
	$svg_icon         = bp_document_svg_icon( 'folder' );
	$download_link    = bp_document_folder_download_link( bp_get_document_folder_id() );
	$folder_link      = bp_get_folder_link();
	$move_class       = 'ac-folder-move';
	$listing_class    = 'ac-folder-list';
	$document_type    = 'folder';
	$folder_privacy   = bp_document_user_can_manage_folder( bp_get_document_folder_id(), bp_loggedin_user_id() );
	$can_manage_btn   = ( true === (bool) $folder_privacy['can_manage'] ) ? true : false;
	$can_view         = ( true === (bool) $folder_privacy['can_view'] ) ? true : false;
	$can_download_btn = ( true === (bool) $folder_privacy['can_download'] ) ? true : false;
	$group_id         = bp_get_document_folder_group_id();
	$document_title   = bp_get_folder_title();
	$data_action      = 'folder';
	if ( $group_id > 0 ) {
		$move_id   = $group_id;
		$move_type = 'group';
	} else {
		$move_id   = bp_get_document_user_id();
		$move_type = 'profile';
	}
}

$document_id = bp_get_document_id();

$link = ( $attachment_id ) ? $download_link : $folder_link;

$class = '';
if ( $attachment_id && bp_get_document_activity_id() ) {
	$class = 'bb-open-document-theatre';
}

?>

<div class="bp-search-ajax-item bboss_ajax_search_document search-document-list">
	<a href="">
		<div class="item">
			<div class="media-folder_items <?php echo esc_attr( $listing_class ); ?>" data-activity-id="<?php bp_document_activity_id(); ?>" data-id="<?php bp_document_id(); ?>" data-parent-id="<?php bp_document_parent_id(); ?>" id="div-listing-<?php bp_document_id(); ?>">
				<div class="media-folder_icon">
					<a href="<?php echo esc_url( $link ); ?>"> <i class="<?php echo esc_attr( $svg_icon ); ?>"></i> </a>
				</div>
				<div class="media-folder_details">
					<a class="media-folder_name <?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $link ); ?>" data-id="<?php bp_document_id(); ?>" data-attachment-full="" data-privacy="<?php bp_db_document_privacy(); ?>" data-extension="<?php echo $extension ? esc_attr( $extension ) : ''; ?>" data-parent-activity-id="<?php bp_document_parent_activity_id(); ?>" data-activity-id="<?php bp_document_activity_id(); ?>" data-preview="<?php echo $attachment_url ? esc_url( $attachment_url ) : ''; ?>" data-text-preview="<?php echo $text_attachment_url ? esc_url( $text_attachment_url ) : ''; ?>" data-album-id="<?php bp_document_folder_id(); ?>" data-group-id="<?php bp_document_group_id(); ?>" data-document-title="<?php echo esc_html( $filename ); ?>" data-icon-class="<?php echo esc_attr( $svg_icon ); ?>">
						<!--			<span>--><?php // echo esc_html( $document_title ); ?><!--</span>-->
						<span><?php echo esc_html( $document_title ); ?></span><?php echo $extension ? '.' . esc_html( $extension ) : ''; ?>
						<i class="media-document-id" data-item-id="<?php echo esc_attr( bp_get_document_id() ); ?>" style="display: none;"></i>
						<i class="media-document-attachment-id" data-item-id="<?php echo esc_attr( bp_get_document_attachment_id() ); ?>" style="display: none;"></i>
						<i class="media-document-type" data-item-id="<?php echo esc_attr( $document_type ); ?>" style="display: none;"></i>
					</a>
				</div>
				<div class="media-folder_modified">
					<div class="media-folder_details__bottom">
						<span class="media-folder_date"><?php bp_document_date(); ?></span>
						<?php
						if ( ! bp_is_user() ) {
							?>
							<span class="media-folder_author"><?php esc_html_e( 'by ', 'buddyboss' ); ?><a href="<?php echo trailingslashit( bp_core_get_user_domain( bp_get_document_user_id() ) . bp_get_document_slug() ); ?>"><?php bp_document_author(); ?></a></span>
							<?php
						}
						?>
					</div>
				</div>
				<?php
				if ( bp_is_document_directory() && bp_is_active( 'groups' ) ) {
					?>
					<div class="media-folder_group">
						<div class="media-folder_details__bottom">
							<?php
							$group_id = bp_get_document_group_id();
							if ( $group_id > 0 ) {
								// Get the group from the database.
								$group = groups_get_group( $group_id );

								$group_name   = isset( $group->name ) ? bp_get_group_name( $group ) : '';
								$group_link   = sprintf( '<a href="%s" class="bp-group-home-link %s-home-link">%s</a>', esc_url( trailingslashit( bp_get_group_permalink( $group ) . bp_get_document_slug() ) ), esc_attr( bp_get_group_slug( $group ) ), esc_html( bp_get_group_name( $group ) ) );
								$group_status = bp_get_group_status( $group );
								?>
								<span class="media-folder_group"><?php echo wp_kses_post( $group_link ); ?></span>
								<span class="media-folder_status"><?php echo ucfirst( $group_status ); ?></span>
								<?php
							} else {
								?>
								<span class="media-folder_group"><?php esc_html_e( '-', 'buddyboss' ); ?></span>
								<?php
							}
							?>
						</div>
					</div>
					<?php
				}
				?>
				<div class="media-folder_visibility">
					<div class="media-folder_details__bottom">

						<?php
						if ( bp_is_active( 'groups' ) ) {
							$group_id = bp_get_document_group_id();
							if ( $group_id > 0 ) {
								?>
								<span class="bp-tooltip" data-bp-tooltip-pos="down" data-bp-tooltip="<?php esc_attr_e( 'Based on group privacy', 'buddyboss' ); ?>">
								<?php
								bp_document_privacy();
								?>
						</span>
								<?php
							} else {
								?>
								<span id="privacy-<?php echo esc_attr( bp_get_document_id() ); ?>">
								<?php
								bp_document_privacy();
								?>
						</span>
								<?php
							}
						} else {
							?>
							<span>
							<?php
							bp_document_privacy();
							?>
					</span>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</a>
</div>
