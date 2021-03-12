<?php

namespace WPML\TM;

use WPML\FP\Fns;
use WPML\FP\Lst;
use WPML\FP\Obj;
use function WPML\FP\pipe;

class Notify_On_Post_Update {
	public function init_hooks() {
		add_action( 'post_updated', [ $this, 'on_post_update' ], 10, 1 );
	}

	/**
	 * @param int $post_ID Post ID.
	 */
	public function on_post_update( $post_ID ) {
		// Skip if it's a REST request (or it will send the same email twice)
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		$getPostsToUpdate = pipe(
			\WPML\Element\API\PostTranslations::getIfOriginal(),
			Fns::reject( Obj::prop( 'original' ) ),
			Fns::map( Obj::prop( 'element_id' ) )
		);

		// The current post is not the original or has no translations
		if ( ! $getPostsToUpdate( $post_ID ) ) {
			return;
		}

		$post = get_post( $post_ID );

		global $sitepress;

		$element_type = 'post_' . $post->post_type;
		if ( $post->ID == $sitepress->get_original_element_id( $post->ID, $element_type ) ) {
			$trid         = $sitepress->get_element_trid( $post->ID, $element_type );
			$translations = $sitepress->get_element_translations( $trid, $element_type );

			$blog_translators = wpml_tm_load_blog_translators();
			$translators      = $blog_translators->get_blog_translators();

			foreach ( $translations as $source_language_code => $translation_data ) {
				if ( (int) $translation_data->element_id !== $post->ID ) {
					/** @var null|\WP_User[] $translator */
					$translators = array_filter( $translators, function ( $translator ) use ( $translation_data ) {
						return isset( $translator->language_pairs[ $translation_data->source_language_code ] ) && in_array( $translation_data->language_code, $translator->language_pairs[ $translation_data->source_language_code ] );
					} );
					$hasLanguage = pipe( Obj::pathOr( [], [
						'language_pairs',
						$translation_data->source_language_code
					] ), Lst::includes( $translation_data->language_code ) );
					Fns::filter( $hasLanguage, $translators );

					if ( $translators ) {
						foreach ( $translators as $translator ) {
							$recipient = get_user_by( 'ID', $translator->ID );
							if ( $recipient ) {
								$this->send_email( $post, $recipient );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * @param \WP_Post $post
	 * @param \WP_User $recipient
	 * @param string
	 */
	private function send_email( $post, $recipient ) {
		// Just a helper function to build anchor tags
		$getLink = function ( $url, $text ) {
			$link_template = '<a href="%1$s" title="%2$s">%2$s</a>';

			return sprintf( $link_template, $url, $text );
		};

		$post_link = '<strong>' . $getLink( get_post_permalink( $post->ID ), $post->post_title ) . '</strong>';

		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

		$subject = 'Translations of ' . $post->post_title . ' may need to be updated';

		$message = [
			// translators: %s is replaced with the name of a translator.
			sprintf( esc_html__( "Hello, %s", 'wpml-notify-translators' ), $recipient->display_name ),
			'',
			// translators: %s is replaced with a link to a post.
			sprintf( esc_html__( "%s has been modified and its translations may require an update.", 'wpml-notify-translators' ), $post_link ),
			'',
			$getLink( get_bloginfo( 'url' ), get_bloginfo( 'name' ) ),
		];

		$body = implode( '<br>', $message );

		/**
		 * Filters the email subject.
		 *
		 * @param string   $subject   The subject of the email.
		 * @param \WP_Post $post      The post that has been updated.
		 * @param \WP_User $recipient The recipient (as an instance of `\WP_User`) of the email.
		 */
		$subject = apply_filters( 'wpml_notify_on_post_update_send_email_subject', $subject, $post, $recipient );
		/**
		 * Filters the email subject.
		 *
		 * @param string   $body      The body of the email.
		 * @param \WP_Post $post      The post that has been updated.
		 * @param \WP_User $recipient The recipient (as an instance of `\WP_User`) of the email.
		 */
		$body = apply_filters( 'wpml_notify_on_post_update_send_email_body', $body, $post, $recipient );
		/**
		 * Filters the recipient of the email.
		 *
		 * @param string   $recipient_email The email address that will be used to send the email.
		 * @param \WP_Post $post            The post that has been updated.
		 * @param \WP_User $recipient       The recipient (as an instance of `\WP_User`) of the email.
		 */
		$recipient_email = apply_filters( 'wpml_notify_on_post_update_send_email_recipient', $recipient->user_email, $post, $recipient );

		wp_mail( $recipient_email, $subject, $body, $headers );
	}
}
