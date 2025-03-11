<?php
// @codingStandardsIgnoreStart
defined( 'ABSPATH' ) || exit;


/* GENERAL
***************************************************/
{
	/*! SET CONTENT WIDTH
	------------------------------------------------->*/
	if ( ! isset( $content_width ) ) {
		$content_width = 1170;
	}

	/*! PRELOADER
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_preloader' ) ) :
		function zoa_preloader() {
			if ( true == get_theme_mod( 'loading', false ) ) :
				?>
                <div id="page-loader"></div>
			<?php
			endif;
		}
	endif;

	/*! SET KIRKI LABEL
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_label' ) ):
		function zoa_label( $data = '' ) {
			$output = '<span style="padding: 15px 10px; background-color: #cbcbcb; color: #333; display: block; text-transform: uppercase; font-weight: 700; margin: 15px 0;">' . $data . '</span>';

			return $output;
		}
	endif;

	/*! SET DEFAULT FONT IF KIRKI NOT ACTIVE
	------------------------------------------------->*/
	add_action( 'wp_enqueue_scripts', 'zoa_typo_default' );
	function zoa_typo_default() {
		$default = get_theme_mod( 'typo_body', array( 'font-family' => 'Open Sans' ) );
		if ( ! class_exists( 'Kirki' ) ) {
			echo '<style type="text/css">input, select, textarea, button{font-family: ' . $default['font-family'] . ', sans-serif!important}</style>';
		}
	}

	/*! SET IMAGE ALT
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_img_alt' ) ):
		function zoa_img_alt( $id = null, $alt = '' ) {
			$data    = get_post_meta( $id, '_wp_attachment_image_alt', true );
			$img_alt = ! empty( $data ) ? $data : $alt;

			return $img_alt;
		}
	endif;

	/*! SET LOGO IMAGE
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_logo_image' ) ):
		function zoa_logo_image() {
			$pid         = get_queried_object_id();
			$p_lg        = function_exists( 'fw_get_db_post_option' ) ? fw_get_db_post_option( $pid, 'p_lg' ) : '';
			$menu_layout = zoa_menu_slug();

			/*general logo*/
			$logo = get_theme_mod( 'custom_logo' );

			/*logo src*/
			$logo_src_full = wp_get_attachment_image_src( $logo, 'full' );
			$logo_src      = ! empty( $logo ) ? $logo_src_full[0] : get_template_directory_uri() . '/images/logo.svg';

			// retina logo
			$retina_logo     = get_theme_mod( 'retina_logo' );
			$retina_logo_src = ! empty( $retina_logo ) ? $retina_logo : $logo_src;

			$tag       = 'figure';
			$child_tag = 'figcaption';

			if ( is_front_page() ) {
				$tag       = 'h1';
				$child_tag = 'span';
			}

			?>
            <<?php echo esc_attr( $tag ) . ' '; ?> class="theme-logo" itemscope itemtype="http://schema.org/Organization">
            <a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" itemprop="url">
                <img class="primary-logo"
                     src="<?php echo esc_url( $logo_src ); ?>"
                     alt="<?php esc_attr_e( 'Logo image', 'zoa' ); ?>"
                     itemprop="logo"
                     srcset="<?php echo esc_url( $logo_src ); ?> 1x, <?php echo esc_url( $retina_logo_src ); ?> 2x"
                >
				<?php
				if ( 'layout-4' == $menu_layout ) :
					$logo4 = get_theme_mod( 'secondary_logo' );
					$logo4_src = ! empty( $logo4 ) ? $logo4 : $logo_src;
					?>
                    <img class="secondary-logo"
                         src="<?php echo esc_url( $logo4_src ); ?>"
                         alt="<?php esc_attr_e( 'Logo image', 'zoa' ); ?>"
                         itemprop="logo">
				<?php endif; ?>
            </a>
            <<?php echo esc_attr( $child_tag ); ?> class="screen-reader-text"><?php echo esc_attr( bloginfo( 'name' ) ); ?></<?php echo esc_attr( $child_tag ); ?>>
            </<?php echo esc_attr( $tag ); ?>>
			<?php
		}
	endif;

	/*! SCHEMA MARKUP SHORTCUT
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_schema_markup' ) ):
		function zoa_schema_markup( $type ) {

			if ( empty( $type ) ) {
				return;
			}

			$attributes = '';
			$attr       = array();

			switch ( $type ) {
				case 'head':
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/WebSite';
					break;

				case 'body':
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/WebPage';
					break;

				case 'header':
					$attr['role']      = 'banner';
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/WPHeader';
					break;

				case 'nav':
					$attr['role']      = 'navigation';
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/SiteNavigationElement';
					break;

				case 'breadcrumb':
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/BreadcrumbList';
					break;

				case 'title':
					$attr['itemprop'] = 'headline';
					break;

				case 'sidebar':
					$attr['role']      = 'complementary';
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/WPSideBar';
					break;

				case 'footer':
					$attr['role']      = 'contentinfo';
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/WPFooter';
					break;

				case 'main':
					$attr['itemprop'] = 'mainContentOfPage';
					if ( is_search() ) {
						$attr['itemtype'] = 'https://schema.org/SearchResultsPage';
					}
					break;

				case 'article':
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/Article';
					break;

				case 'blog':
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/Blog';
					break;

				case 'blog_list':
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/BlogPosting';
					break;

				case 'creative_work':
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/CreativeWork';
					break;

				case 'author':
					$attr['itemprop']  = 'author';
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/Person';
					break;

				case 'person':
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/Person';
					break;

				case 'comment':
					$attr['itemprop']  = 'comment';
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/UserComments';
					break;

				case 'comment_author':
					$attr['itemprop']  = 'creator';
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/Person';
					break;

				case 'comment_author_link':
					$attr['itemprop']  = 'creator';
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/Person';
					$attr['rel']       = 'external nofollow';
					break;

				case 'comment_time':
					$attr['itemprop']  = 'commentTime';
					$attr['itemscope'] = 'itemscope';
					$attr['datetime']  = get_the_time( 'c' );
					break;

				case 'comment_text':
					$attr['itemprop'] = 'commentText';
					break;

				case 'author_box':
					$attr['itemprop']  = 'author';
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/Person';
					break;

				case 'video':
					$attr['itemprop'] = 'video';
					$attr['itemtype'] = 'https://schema.org/VideoObject';
					break;

				case 'audio':
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'https://schema.org/AudioObject';
					break;

				case 'image':
					$attr['itemscope'] = 'itemscope';
					$attr['itemprop']  = 'image';
					$attr['itemtype']  = 'http://schema.org/ImageObject';
					break;

				case 'organization':
					$attr['itemprop']  = 'organization';
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'http://schema.org/Organization';
					break;

				case 'publisher':
					$attr['itemprop']  = 'publisher';
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'http://schema.org/Organization';
					break;

				case 'logo':
					$attr['itemprop']  = 'logo';
					$attr['itemscope'] = 'itemscope';
					$attr['itemtype']  = 'http://schema.org/Organization ';
					break;

				case 'name':
					$attr['itemprop'] = 'name';
					break;

				case 'url':
					$attr['itemprop'] = 'url';
					break;

				case 'email':
					$attr['itemprop'] = 'email';
					break;

				case 'job':
					$attr['itemprop'] = 'jobTitle';
					break;

				case 'post_time':
					$attr['itemprop'] = 'datePublished';
					$attr['datetime'] = get_the_time( 'c', $args['id'] );
					break;

				case 'post_title':
					$attr['itemprop'] = 'headline';
					break;

				case 'post_content':
					$attr['itemprop'] = 'text';
					break;
			}

			foreach ( $attr as $key => $value ) {
				$attributes .= $key . '="' . $value . '" ';
			}

			echo wp_kses_post( $attributes );
		}
	endif;

	/*! SCHEMA MARKUP FOR SEO
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_seo_data' ) ):
		function zoa_seo_data() {
			?>
            <div class="screen-reader-text">
				<?php /*HEADING*/ ?>
                <h2 <?php zoa_schema_markup( 'title' ); ?>><?php echo get_the_title(); ?></h2>

				<?php /*THUMBNAIL*/ ?>
				<?php
				global $post;
				$thumbnail = get_the_post_thumbnail_url( $post->ID, 'thumbnail' );

				if ( ! empty( $thumbnail ) ) {
					$img_id  = get_post_thumbnail_id( $post->ID );
					$img_alt = zoa_img_alt( $img_id, esc_attr__( 'Blog thumbnail', 'zoa' ) );
					?>
                    <span <?php zoa_schema_markup( 'image' ); ?>>
							<img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
							<meta itemprop="url" content="<?php echo esc_url( $thumbnail ); ?>"/>
							<meta itemprop="width" content="770"/>
							<meta itemprop="height" content="450"/>
						</span>
				<?php } else { ?>
                    <span <?php zoa_schema_markup( 'image' ); ?>>
						<img src="<?php echo get_template_directory_uri() . '/images/thumbnail-default.jpg'; ?>"
                             alt="<?php esc_attr_e( 'Thumbnail', 'zoa' ); ?>">
						<meta itemprop="url"
                              content="<?php echo get_template_directory_uri() . '/images/thumbnail-default.jpg'; ?>"/>
						<meta itemprop="width" content="100"/>
						<meta itemprop="height" content="90"/>
					</span>
				<?php } ?>

				<?php /*PUBLISHER*/ ?>
                <span class="author" <?php zoa_schema_markup( 'publisher' ); ?>>
					<span itemprop="logo" itemscope="itemscope" itemtype="https://schema.org/ImageObject">
						<meta itemprop="url" content="<?php echo esc_url( home_url( '/' ) ); ?>"/>
						<meta itemprop="width" content="100"/>
						<meta itemprop="height" content="100"/>
					</span>
					<meta itemprop="name" content="<?php the_author(); ?>"/>
				</span>

				<?php /*DATE MODIFIED*/ ?>
                <span itemprop="dateModified" class="updated">
					<time datetime="<?php echo esc_attr( get_the_modified_time( 'Y-m-d' ) ); ?>">
						<?php the_modified_date(); ?>
					</time>
				</span>

				<?php /*DATE PUBLISHED*/ ?>
                <span itemprop="datePublished"
                      content="<?php echo get_the_time( 'c' ); ?>"><?php echo get_the_time( 'c' ); ?></span>

				<?php /*POST CATEGORIES*/ ?>
                <span itemprop="articleSection"><?php echo zoa_blog_categories(); ?></span>
            </div>
			<?php
		}
	endif;

	/*! THEME PAGINATION ( SINGLE POST/PAGE )
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_wp_link_pages' ) ):
		function zoa_wp_link_pages() {
			$args = array(
				'before'      => '<div class="theme-page-links">',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			);

			return wp_link_pages( $args );
		}
	endif;

	/*! REMOVE TAG-CLOUD INLINE CSS
	------------------------------------------------->*/
	add_filter( 'wp_generate_tag_cloud', 'zoa_tag_cloud', 10, 1 );
	function zoa_tag_cloud( $string ) {
		return preg_replace( '/ style=("|\')(.*?)("|\')/', '', $string );
	}

	/* THEME SEARCH FORM
	***************************************************/
	if ( ! function_exists( 'zoa_dialog_search_form' ) ) {
		function zoa_dialog_search_form() {
			?>
            <div class="zoa-search-form-container js-search-form">
                <div class="container zoa-search-form__inner">
                    <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="zoa-search-form">
                        <input
                                type="text"
                                name="s"
                                class="zoa-search-form__field js-search-field"
                                placeholder="<?php esc_attr_e( 'Search...', 'zoa' ); ?>"
                        >
                        <span class="zoa-search-form__desc"><?php esc_html_e( 'Hit Enter to search or Esc key to close',
								'zoa'
							); ?></span>
                        <a href="#" class="zoa-search-form__close js-close-search-form">
                            <span class="ion-android-close"></span>
                        </a>
                        <input type="hidden" name="post_type" value="product" />
                    </form><!-- .ajax-search-form -->
                </div><!-- .ajax-search-form__inner -->
            </div><!-- .ajax-search-form-container -->
			<?php
		}
	}

	/**
	 * Ajax search form
	 */
	if ( ! function_exists( 'zoa_ajax_search_form' ) ) {
		function zoa_ajax_search_form() {
			?>
            <div class="zoa-search-form-container js-search-form">
                <div class="container zoa-search-form__inner">
                    <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="zoa-search-form">
                        <input
                                type="text"
                                name="s"
                                id="autocomplete"
                                class="zoa-search-form__field js-search-field"
                                placeholder="<?php esc_attr_e( 'Search Products...', 'zoa' ); ?>"
                        >
                        <span class="fa fa-circle-o-notch fa-2x fa-fw zoa-search-form__loader"></span>
                        <a href="#" class="zoa-search-form__close js-close-search-form">
                            <span class="ion-android-close"></span>
                        </a>
                    </form><!-- .ajax-search-form -->

                    <div class="zoa-search-form__suggestions"></div>
                </div><!-- .ajax-search-form__inner -->
            </div><!-- .ajax-search-form-container -->
			<?php
		}
	}

	/*! DETECT IE BROWSER
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_ie' ) ):
		function zoa_ie() {
			global $is_winIE;

			if ( $is_winIE ) {
				return true;
			}

			return false;
		}
	endif;
}


/* BLOG
***************************************************/
{
	/*! DETECT BLOG PAGE
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_blog' ) ):
		function zoa_blog() {
			global $post;
			$post_type = get_post_type( $post );

			return ( 'post' == $post_type && ( is_archive() || is_author() || is_category() || is_home() || is_single() || is_tag() ) ) ? true : false;
		}
	endif;

	/*! BLOG TAGS
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_blog_tags' ) ):
		function zoa_blog_tags() {
			if ( has_tag() ):
				?>
                <span class="tagcloud" itemprop="keywords"><?php echo the_tags( '', '', '' ); ?></span>
			<?php
			endif;
		}
	endif;

	/*! BLOG PAGINATION
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_paging' ) ):
		function zoa_paging( $wp_query = null, $paged = null ) {

			if ( ! $wp_query ) {
				$wp_query = $GLOBALS['wp_query'];
			}

			/*Don't print empty markup if there's only one page.*/

			if ( $wp_query->max_num_pages < 2 ) {
				return;
			}

			if ( null == $paged ) {
				$paged = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
			}
			$pagenum_link = html_entity_decode( get_pagenum_link() );
			$query_args   = array();
			$url_parts    = explode( '?', $pagenum_link );

			if ( isset( $url_parts[1] ) ) {
				wp_parse_str( $url_parts[1], $query_args );
			}

			$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
			$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

			$format = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
			$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

			/*Set up paginated links.*/
			$links = paginate_links( array(
				'base'      => $pagenum_link,
				'format'    => $format,
				'total'     => $wp_query->max_num_pages,
				'current'   => $paged,
				'mid_size'  => 1,
				'add_args'  => array_map( 'urlencode', $query_args ),
				'prev_text' => sprintf( esc_html_x( '%s Prev', 'Pagination previous button', 'zoa' ), '<span class="fa fa-angle-double-left"></span>' ),
				'next_text' => sprintf( esc_html_x( 'Next %s', 'Pagination next button', 'zoa' ), '<span class="fa fa-angle-double-right"></span>' ),
				'type'      => 'list'
			) );

			if ( $links ):
				?>
                <nav class="ht-pagination">
                    <span class="screen-reader-text"><?php esc_html_e( 'Posts pagination', 'zoa' ); ?></span>
					<?php echo wp_kses_post( $links ); ?>
                </nav>
			<?php
			endif;
		}
	endif;

	/*! BLOG CATEGORIES
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_blog_categories' ) ):
		function zoa_blog_categories() {
			return get_the_term_list( get_the_ID(), 'category', esc_html_x( 'in ', 'In Uncategorized Category', 'zoa' ), ', ', null );
		}
	endif;

	/*! COMMENT LIST MODIFIED
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_comment_list' ) ):
		function zoa_comment_list( $comment, $args, $depth ) {
			$GLOBALS['comment'] = $comment;
			switch ( $comment->comment_type ):
				case 'pingback':
				case 'trackback':
					?>
                    <div class="comment-post-pingback">
                        <span><?php esc_html_e( 'Pingback:', 'zoa' ); ?></span><?php comment_author_link(); ?><?php edit_comment_link( esc_html__( 'Edit', 'zoa' ) ); ?>
                    </div>
					<?php
					break;
				default:
					?>
                    <div id="comment-<?php comment_ID(); ?>"
                         class="comment-item" <?php zoa_schema_markup( 'comment' ); ?>>
                        <div class="comment-avatar">
							<?php echo get_avatar( $comment, $size = 100 ); ?>
                        </div>
                        <div class="comment-content">
                            <strong class="comment-author" <?php zoa_schema_markup( 'comment_author' ); ?>>
                                <a href="#comment-<?php comment_ID(); ?>"
                                   class="comment-author-name" <?php zoa_schema_markup( 'comment_author_link' ); ?>><?php echo get_comment_author(); ?></a>
                                <time class="comment-time" <?php zoa_schema_markup( 'comment_time' ); ?>><?php echo zoa_date_format(); ?></time>
                            </strong>
                            <div class="comment-info">
								<?php
								echo get_comment_reply_link( array_merge( $args, array(
									'depth'      => $depth,
									'reply_text' => esc_html__( 'Reply', 'zoa' ),
									'max_depth'  => $args['max_depth']
								) ) );
								?>
								<?php edit_comment_link( esc_html__( 'Edit', 'zoa' ), ' ', '' ); ?>
                            </div>
                            <div class="comment-text" <?php zoa_schema_markup( 'comment_text' ); ?>>
								<?php if ( '0' == $comment->comment_approved ): ?>
                                    <em><?php esc_html_e( 'Your comment is awaiting moderation.', 'zoa' ) ?></em>
								<?php endif; ?>
								<?php comment_text(); ?>
                            </div>
                        </div>
                    </div>
					<?php
					break;
			endswitch;
		}
	endif;

	/*! GET DATE TIME
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_date_format' ) ):
		function zoa_date_format() {
			$date_format = get_the_date( get_option( 'date_format' ) );

			return $date_format;
		}
	endif;

	/*! BLOG POST INFO
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_post_info' ) ):
		function zoa_post_info() {
			global $post;
			?>
            <span class="if-item if-cat"><?php echo zoa_blog_categories(); ?></span>
            <time class="if-item if-date" itemprop="datePublished"
                  datetime="<?php echo get_the_time( 'c' ); ?>"><?php echo zoa_date_format(); ?></time>

			<?php if ( is_single() ): ?>
                <span class="if-item if-author" itemprop="author">
						<span><?php esc_html_e( 'by ', 'zoa' ); ?></span>
					<?php the_author_posts_link(); ?>
					</span>
			<?php endif; ?>
			<?php
		}
	endif;

	/*! post thumbnail
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_post_thumbnail' ) ):
		function zoa_post_thumbnail( $size = 'full' ) {
			global $post;
			$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $size );
			if ( empty( $thumbnail ) ) {
				return;
			}

			$img_id  = get_post_thumbnail_id( $post->ID );
			$img_alt = zoa_img_alt( $img_id, esc_attr__( 'Blog thumbnail', 'zoa' ) );

			if ( is_single() ) {
				?>
                <div class="cover-image" <?php zoa_schema_markup( 'image' ); ?>>
                    <img src="<?php echo esc_url( $thumbnail[0] ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
                    <meta itemprop="url" content="<?php echo esc_url( $thumbnail[0] ); ?>"/>
                    <meta itemprop="width" content="<?php echo esc_attr( $thumbnail[1] ); ?>"/>
                    <meta itemprop="height" content="<?php echo esc_attr( $thumbnail[2] ); ?>"/>
                </div>
			<?php } else { ?>
                <a href="<?php the_permalink(); ?>"
                   class="cover-image entry-image-link" <?php zoa_schema_markup( 'image' ); ?>>
                    <img src="<?php echo esc_url( $thumbnail[0] ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
                    <meta itemprop="url" content="<?php echo esc_url( $thumbnail[0] ); ?>"/>
                    <meta itemprop="width" content="<?php echo esc_attr( $thumbnail[1] ); ?>"/>
                    <meta itemprop="height" content="<?php echo esc_attr( $thumbnail[2] ); ?>"/>
					<?php if ( is_sticky() ): ?>
                        <span class="sticky-medal"><span><?php esc_html_e( 'Sticky', 'zoa' ); ?></span></span>
					<?php endif; ?>
                </a>
				<?php
			}
		}
	endif;

	/*! POST FORMAT
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_post_format' ) ):
		function zoa_post_format() {
			global $post;
			$format               = get_post_format( $post->ID );

			if ( is_single() ) {
				switch ( $format ) {
					case 'video':
						$video_type = function_exists( 'fw_get_db_post_option' ) ? fw_get_db_post_option( $post->ID, 'video_type' ) : '';
						$video_id = function_exists( 'fw_get_db_post_option' ) ? fw_get_db_post_option( $post->ID, 'video_id' ) : '';

						if ( ! empty( $video_id ) ) {

							if ( zoa_ie() ) {
								wp_enqueue_script( 'plyr-polyfill' );
							}

							wp_enqueue_script( 'plyr-script' );
							wp_enqueue_style( 'plyr-style' );

							wp_add_inline_script(
								'plyr-script',
								"document.addEventListener( 'DOMContentLoaded', function () {
									var player = document.getElementById( 'p-video' );
									if( player ){
										var playerSetup = new Plyr( player );
									}
								});",
								'after'
							);
							?>
                            <div class="single-format-content pf-video">
                                <div data-plyr-provider="<?php echo esc_attr( $video_type ); ?>"
                                     data-plyr-embed-id="<?php echo esc_attr( $video_id ); ?>" id="p-video"></div>
                            </div>

							<?php
						} else {
							zoa_post_thumbnail();
						}
						break;

					case 'gallery':
						$gallery = function_exists( 'fw_get_db_post_option' ) ? fw_get_db_post_option( $post->ID, 'd_gallery' ) : array();
						$arrows   = function_exists( 'fw_get_db_post_option' ) ? fw_get_db_post_option( $post->ID, 'arrows', true ) : true;
						$dots     = function_exists( 'fw_get_db_post_option' ) ? fw_get_db_post_option( $post->ID, 'dots', false ) : true;

						if ( ! empty( $gallery ) ) {

							wp_add_inline_script(
								'tiny-slider',
								"window.addEventListener( 'DOMContentLoaded', function(){
									var sliderContainer = document.getElementById( 'post-type-gallery-slide' ),
										opt = JSON.parse( sliderContainer.getAttribute( 'data-tiny-slider' ) );

									opt.container = sliderContainer;
									var slider = tns( opt );
								} );",
								'after'
							);
							/*tiny slider options*/
							$options = array(
								"items"      => 1,
								"mouseDrag"  => true,
								"infinite"   => false,
								"controls"   => $arrows,
								"nav"        => $dots,
								"autoHeight" => true,
								'loop'       => false,
								"arrowKeys"  => true
							);
							?>
                            <div class="single-format-content pf-gallery">
                                <div id="post-type-gallery-slide"
                                     data-tiny-slider='<?php echo json_encode( $options ); ?>'>
									<?php
									foreach ( $gallery as $key ) {
										$imgs_alt = zoa_img_alt( $key['attachment_id'], esc_attr__( 'Gallery image', 'zoa' ) );
										echo '<img src="' . esc_url( $key['url'] ) . '" alt="' . esc_attr( $imgs_alt ) . '" />';
									}
									?>
                                </div>
                            </div>
							<?php
						} else {
							zoa_post_thumbnail();
						}
						break;

					case 'audio':
						$d_audio = function_exists( 'fw_get_db_post_option' ) ? fw_get_db_post_option( $post->ID, 'd_audio' ) : '';
						if ( ! empty( $d_audio ) ) {
							?>
                            <div class="single-format-content pf-audio">
								<?php echo html_entity_decode( $d_audio ); ?>
                            </div>
							<?php
						} else {
							zoa_post_thumbnail();
						}
						break;

					default:
						zoa_post_thumbnail();
						break;
				}
			} else {
				zoa_post_thumbnail();
			}

		}
	endif;

	/*! OUTPUT SEARCH WIDGET MODIFIED
	------------------------------------------------->*/
	add_filter( 'get_search_form', 'zoa_search_form_widget', 100 );
	function zoa_search_form_widget( $form ) {
		$form = '<form role="search" method="get" class="search-form" action="' . esc_url( home_url( '/' ) ) . '" >';
		$form .= '<label class="screen-reader-text">' . esc_html__( 'Search for:', 'zoa' ) . '</label>';
		$form .= '<input type="text" class="search-field" placeholder="' . esc_attr__( 'Search...', 'zoa' ) . '" value="' . get_search_query() . '" name="s" required/>';
		$form .= '<button type="submit" class="search-submit zoa-icon-search"></button>';
		$form .= '</form>';

		return $form;
	}

	/*! CUSTOM EXERPT MORE TEXT
	------------------------------------------------->*/
	add_filter( 'excerpt_more', 'zoa_more' );
	function zoa_more( $more ) {
		return '...';
	}
}


/* PLUGINS
***************************************************/
{
	/*! CUSTOM FONT-FAMILY FOR KIRKI
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_custom_font' ) ):
		function zoa_custom_font() {
			$fonts = array(
				'fonts' => array(
					'families' => array(
						'custom' => array(
							'text'     => esc_html__( 'Zoa custom font', 'zoa' ),
							'children' => array(
								array(
									'id'   => 'Eina03',
									'text' => esc_html__( 'Eina03', 'zoa' )
								),
							)
						),
					),
					'variants' => array(
						'Eina03' => array(
							'regular',
							'300',
							'600',
							'700'
						),
					),
				),
			);

			return $fonts;
		}
	endif;

	/*! REMOVE REV SLIDER METABOX
	------------------------------------------------->*/
	if ( is_admin() ) {
		add_action( 'registered_post_type', 'zoa_remove_rev_slider_meta_boxes' );
		function zoa_remove_rev_slider_meta_boxes( $post_type ) {
			add_action( 'do_meta_boxes', function () use ( $post_type ) {
				remove_meta_box( 'mymetabox_revslider_0', $post_type, 'normal' );
			} );
		}
	}

	/*! REMOVE CONTACT FORM 7 CSS
	------------------------------------------------->*/
	add_filter( 'wpcf7_load_css', '__return_false' );

	/*! DISABLE THE CONFIGURATION CTF7 VALIDATOR
	------------------------------------------------->*/
	add_filter( 'wpcf7_validate_configuration', '__return_false' );

	/*DISABLE STYLESHEETS NONEED*/
	add_action( 'wp_enqueue_scripts', 'zoa_remove_yith_wishlist_css', 100 );
	function zoa_remove_yith_wishlist_css() {
		/*YITH WISHLIST*/
		wp_dequeue_style( 'yith-wcwl-main' );
		wp_dequeue_style( 'yith-wcwl-font-awesome' );
		wp_dequeue_style( 'jquery-selectBox' );
		/*WOOCOMMERCE*/
		wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
		/*SWATCH*/
		wp_dequeue_style( 'tawcvs-frontend' );
	}
}

/* ELEMENTOR
***************************************************/
{
	/* CHECK IF ELEMENTOR IS ACTIVE
	***************************************************/
	if ( ! function_exists( 'zoa_is_elementor' ) ) :
		function zoa_is_elementor() {
			return defined( 'ELEMENTOR_VERSION' );
		}
	endif;

	if ( ! function_exists( 'zoa_is_elementor_pro' ) ):
		function zoa_is_elementor_pro() {
			return defined( 'ELEMENTOR_PRO_VERSION' );
		}
	endif;

	/* CHECK IF PAGE BUILD WITH ELEMENTOR
	***************************************************/
	if ( ! function_exists( 'zoa_elementor_page' ) ):
		function zoa_elementor_page( $id ) {
			return get_post_meta( $id, '_elementor_edit_mode', true );
		}
	endif;

	/* GET PAGE OPTION
	***************************************************/
	if ( ! function_exists( 'zoa_page_opt' ) ):
		function zoa_page_opt( $_id = null, $key = '' ) {
			if ( true == zoa_is_elementor() && true == zoa_elementor_page( $_id ) ) {
				$document = \Elementor\Plugin::$instance->documents->get( $_id );

				if ( $document ) {
					return $document->get_settings( $key );
				}

				return false;
			}
		}
	endif;

	/* TOURS WIDGET: GET NARROW DATA SOURCE
	***************************************************/
	if ( ! function_exists( 'zoa_get_narrow_data' ) ) :
	function zoa_get_narrow_data( $type = 'post', $terms = 'category' ) {
		/* $type  = `post` || `term`
		*  $terms = post_type || taxonomy | ex: post, category, product, product_cat, custom_post_type...
		*/

		$output = array();
		switch ( $type ) :
			case 'post':
				$tour_args = array(
					'post_type'           => $terms,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => - 1,
				);
				$qr        = new WP_Query( $tour_args );
				$output_org    = wp_list_pluck( $qr->posts, 'post_title', 'ID' );
				
				$output = array();
				if ( ! empty( $output_org ) ) {
					foreach ( $output_org as $product_id => $value_p ) {
						$serie_cat = get_the_terms( $product_id, 'series' );
						
						// Check if $serie_cat is valid and contains terms
						if ( $serie_cat && ! is_wp_error( $serie_cat ) ) {
							$serie_cat_name = isset( $serie_cat[0] ) ? $serie_cat[0]->name : ''; // Avoid null or undefined term name
							
							if ( ! isset( $_REQUEST['elementor_updater'] ) && ! isset( $_REQUEST['elementor_pro_updater'] ) ) {
								$output[ $product_id ] = $serie_cat_name . ' - ' . $value_p;
							}
						}
					}
				}
				break;

			case 'term':
				$terms  = get_terms( $terms );
				$output = wp_list_pluck( $terms, 'name', 'term_id' );
				break;
		endswitch;

		return $output;
	}
endif;


	// Add custom markup for Elementor Canvas header.
	add_action( 'elementor/page_templates/canvas/before_content', 'zoa_preloader' );

	add_action( 'elementor/page_templates/canvas/after_content', 'zoa_elementor_canvas_footer' );
	/**
	 * Add custom markup for Elementor Canvas footer
	 */
	function zoa_elementor_canvas_footer() {
		// quick view markup.
		if ( class_exists( 'woocommerce' ) ) {
			zoa_product_action();
		}

		if ( get_theme_mod( 'ajax_search', false ) ) {
			zoa_ajax_search_form();
		} else {
			zoa_dialog_search_form();
		}

		?>
        <a href="#" class="scroll-to-top js-to-top">
            <i class="ion-chevron-up"></i>
        </a>
		<?php
		if ( true === get_theme_mod( 'loading', false ) ) {
			echo '<span class="is-loading-effect"></span>';
		}
	}
}


/* PARTIAL REFRESH
***************************************************/
{
	/*! MENU LAYOUT SLUG
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_menu_slug' ) ):
		function zoa_menu_slug() {
			/*CUSTOMIZE*/
			$layout = get_theme_mod( 'menu_layout', 'layout-2' );

			/* BLOG SINGLE
			***************************************************/
			$pid              = get_queried_object_id();
			$single_blog_menu = function_exists( 'fw_get_db_post_option' ) ? fw_get_db_post_option( $pid, 'single_blog_menu' ) : 'default';

			if ( function_exists( 'FW' ) && 'default' != $single_blog_menu && is_singular( 'post' ) ) :
				$layout = $single_blog_menu;
			endif;

			/*PAGE*/
			$id               = get_queried_object_id();
			$page_menu_layout = true == zoa_elementor_page( $id ) ? zoa_page_opt( $id, 'page_menu_layout' ) : null;
			if ( isset( $page_menu_layout ) && 'default' != $page_menu_layout ) {
				$layout = $page_menu_layout;
			}

			return $layout;
		}
	endif;

	/*! STICKY HEADER MENU
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_sticky_header' ) ) {
		function zoa_sticky_header() {
			?>
            <div class="menu-layout menu-layout-2 menu-layout--classic menu-layout--sticky js-sticky-header">
                <header class="header-box">
                    <div class="container" <?php zoa_schema_markup( 'navigation' ); ?>>
                        <div class="header-container">
                            <div id="hd2-logo" class="header-logo">
								<?php zoa_logo_image(); ?>
                            </div>

                            <div class="theme-menu-box header-menu">
                                <span class="screen-reader-text"><?php esc_html_e( 'Primary Menu', 'zoa' ); ?></span>
								<?php
								if ( has_nav_menu( 'primary' ) ):
									wp_nav_menu( array(
										'theme_location' => 'primary',
										'menu_class'     => 'theme-primary-menu',
										'container'      => '',
									) );
								else:
									?>
                                    <a class="add-menu"
                                       href="<?php echo esc_url( get_admin_url() . 'nav-menus.php' ); ?>"><?php esc_html_e( 'Add Menu', 'zoa' ); ?></a>
								<?php endif; ?>
                            </div><!-- .theme-menu-box -->

                            <div class="header-action">
                                <button id="theme-search-btn" class="zoa-icon-search js-search-button"></button>
								<?php
								if ( class_exists( 'woocommerce' ) ) {
									zoa_wc_header_action();
								}
								?>
                            </div><!-- .header-action -->

                        </div><!-- .header-container -->
                    </div><!-- .container -->
                </header><!-- .header-box -->
            </div><!-- .menu-layout-2 -->
			<?php
		}
	}

	/*! MENU LAYOUT
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_menu_layout' ) ):
		function zoa_menu_layout() {
			/*CUSTOMIZE*/
			$menu_layout = zoa_menu_slug();

			switch ( $menu_layout ):
				case 'layout-1':
					if ( true === get_theme_mod( 'header_1_topbar', false ) ) {
						zoa_topbar();
					}
					?>
                    <div class="menu-layout menu-layout-1">
                        <div class="container" <?php zoa_schema_markup( 'navigation' ); ?>>
                            <span class="screen-reader-text"><?php esc_html_e( 'Primary Menu', 'zoa' ); ?></span>
                            <div id="menu-box">
                                <div class="m-col m1-col">
                                    <button id="menu-toggle-btn"><span></span></button>
                                </div>

								<?php zoa_logo_image(); ?>

                                <div class="m-col m2-col">
                                    <button id="theme-search-btn" class="zoa-icon-search js-search-button"></button>

									<?php
									if ( class_exists( 'woocommerce' ) ) {
										zoa_wc_header_action();
									}
									?>
                                </div>
                            </div>
                        </div>
                        <span id="menu-overlay"></span>
                    </div>
					<?php
					break;
				case 'layout-2':
					if ( true === get_theme_mod( 'header_2_topbar', false ) ) {
						zoa_topbar();
					}
					?>
                    <div class="menu-layout menu-layout-2 menu-layout--classic">
                        <header class="header-box">
                            <div class="container" <?php zoa_schema_markup( 'navigation' ); ?>>
                                <div class="header-container">
                                    <button id="menu-toggle-btn"><span></span></button>
                                    <div id="hd2-logo" class="header-logo">
										<?php zoa_logo_image(); ?>
                                    </div>

                                    <div class="theme-menu-box header-menu">
                                        <span class="screen-reader-text"><?php esc_html_e( 'Primary Menu', 'zoa' ); ?></span>
										<?php
										if ( has_nav_menu( 'primary' ) ):
											wp_nav_menu( array(
												'theme_location' => 'primary',
												'menu_class'     => 'theme-primary-menu',
												'container'      => '',
											) );
										else:
											?>
                                            <a class="add-menu"
                                               href="<?php echo esc_url( get_admin_url() . 'nav-menus.php' ); ?>"><?php esc_html_e( 'Add Menu', 'zoa' ); ?></a>
										<?php endif; ?>
                                    </div><!-- .theme-menu-box -->

                                    <div class="header-action">
                                        <button id="theme-search-btn" class="zoa-icon-search js-search-button"></button>
										<?php
										if ( class_exists( 'woocommerce' ) ) {
											zoa_wc_header_action();
										}
										?>
                                    </div><!-- .header-action -->

                                </div><!-- .header-container -->
                            </div><!-- .container -->
                        </header><!-- .header-box -->
                        <span id="menu-overlay"></span>
                    </div><!-- .menu-layout-2 -->
					<?php
					break;
				case 'layout-3':
					$menu_layout_classes = 'menu-layout menu-layout-3 menu-layout--classic menu-layout--transparent';
					if ( true === get_theme_mod( 'header_3_topbar', false ) ) {
						zoa_topbar();
						$menu_layout_classes .= ' menu-layout--offset-top';
					}
					?>
                    <div class="<?php echo esc_attr( $menu_layout_classes ); ?>">
                        <header class="header-box">
                            <div class="container" <?php zoa_schema_markup( 'navigation' ); ?>>
                                <div class="header-container">
                                    <button id="menu-toggle-btn"><span></span></button>

                                    <div class="header-container__inner">
                                        <div class="header-logo">
											<?php zoa_logo_image(); ?>
                                        </div>

                                        <div class="theme-menu-box header-menu">
                                            <span class="screen-reader-text"><?php esc_html_e( 'Primary Menu', 'zoa' ); ?></span>
											<?php
											if ( has_nav_menu( 'primary' ) ):
												wp_nav_menu( array(
													'theme_location' => 'primary',
													'menu_class'     => 'theme-primary-menu',
													'container'      => '',
												) );
											else:
												?>
                                                <a class="add-menu"
                                                   href="<?php echo esc_url( get_admin_url() . 'nav-menus.php' ); ?>"><?php esc_html_e( 'Add Menu', 'zoa' ); ?></a>
											<?php endif; ?>
                                        </div><!-- .theme-menu-box -->
                                    </div>

                                    <div class="header-action">
                                        <button id="theme-search-btn" class="zoa-icon-search js-search-button"></button>
										<?php
										if ( class_exists( 'woocommerce' ) ) {
											zoa_wc_header_action();
										}
										?>
                                    </div><!-- .header-action -->

                                </div><!-- .header-container -->
                            </div><!-- .container -->
                        </header><!-- .header-box -->
                        <span id="menu-overlay"></span>
                    </div><!-- .menu-layout-3 -->
					<?php
					break;
				case 'layout-4':
					$menu_layout_classes = 'menu-layout menu-layout-4 menu-layout--centered menu-layout--transparent';
					if ( true === get_theme_mod( 'header_4_topbar', false ) ) {
						zoa_topbar();
						$menu_layout_classes .= ' menu-layout--offset-top';
					}
					?>
                    <div class="<?php echo esc_attr( $menu_layout_classes ); ?>">
                        <header class="header-box">
                            <div class="container" <?php zoa_schema_markup( 'navigation' ); ?>>
                                <div class="header-container">
                                    <div class="menu-toggle">
                                        <button id="theme-search-btn" class="zoa-icon-search js-search-button"></button>
                                        <button id="menu-toggle-btn"><span></span></button>
                                    </div>

                                    <div class="header-content">
                                        <div class="theme-menu-box header-menu header-menu--left">
                                            <span class="screen-reader-text"><?php echo esc_html__( 'Secondary Menu', 'zoa' ); ?></span>
											<?php
											if ( has_nav_menu( 'secondary' ) ) :
												wp_nav_menu( array(
													'theme_location' => 'secondary',
													'menu_class'     => 'theme-primary-menu',
													'container'      => '',
												) );
											else:
												?>
                                                <a href="<?php echo esc_url( get_admin_url() . 'nav-menus.php' ); ?>"
                                                   class="add-menu"></a>
											<?php endif; ?>
                                        </div><!-- .header-menu--left -->

                                        <div class="header-logo">
											<?php zoa_logo_image(); ?>
                                        </div><!-- .header-logo -->

                                        <div class="theme-menu-box header-menu header-menu--right">
                                            <span class="screen-reader-text"><?php echo esc_html__( 'Tertiary Menu', 'zoa' ); ?></span>
											<?php
											if ( has_nav_menu( 'tertiary' ) ) :
												wp_nav_menu( array(
													'theme_location' => 'tertiary',
													'menu_class'     => 'theme-primary-menu',
													'container'      => '',
												) );
											else:
												?>
                                                <a href="<?php echo esc_url( get_admin_url() . 'nav-menus.php' ); ?>"
                                                   class="add-menu"></a>
											<?php endif; ?>
                                        </div><!-- .header-menu--right -->
                                    </div><!-- .header-content -->

                                    <div class="header-action">
										<?php
										if ( class_exists( 'woocommerce' ) ) {
											zoa_wc_header_action();
										}
										?>
                                    </div><!-- .header-action -->
                                </div><!-- .header-container -->
                            </div><!-- .container -->
                            <span id="menu-overlay"></span>
                        </header><!-- .header-box -->
                    </div><!-- .menu-layout-4 -->
					<?php
					break;
				case 'layout-5':
					if ( true === get_theme_mod( 'header_5_topbar', false ) ) {
						zoa_topbar();
					}
					?>
                    <div class="menu-layout menu-layout-5 menu-layout--vertical">
                        <header class="header-box">
                            <div class="container" <?php zoa_schema_markup( 'navigation' ); ?>>
                                <div class="header-container">
                                    <button id="menu-toggle-btn"><span></span></button>

                                    <div class="header-logo">
										<?php zoa_logo_image(); ?>
                                    </div><!-- .header-logo -->
                                </div><!-- .header-container -->
                            </div><!-- .container -->
                            <span id="menu-overlay"></span>
                        </header><!-- .header-box -->
                    </div><!-- .menu-layout-4 -->
					<?php
					break;
				case 'layout-6':
					if ( true == get_theme_mod( 'header_6_topbar', false ) ) {
						zoa_topbar();
					}
					?>
					<div class="menu-layout menu-layout-6">
						<header class="header-box">
							<div class="container" <?php zoa_schema_markup( 'navigation' ); ?>>
								<div class="header-container">
									<div class="m-col m1-col">
										<?php get_search_form(); ?>
									</div>

									<div class="content-center">
										<?php
											// Left primary menu.
											if ( has_nav_menu( 'secondary' ) ) {
												wp_nav_menu( array(
													'theme_location' => 'secondary',
													'menu_class'     => 'theme-primary-menu',
													'container'      => '',
												) );
											} elseif ( is_user_logged_in() ) {
											?>
												<a href="<?php echo esc_url( get_admin_url() . 'nav-menus.php' ); ?>" class="add-menu"></a>
										<?php } ?>

										<div class="header-logo">
											<?php zoa_logo_image(); ?>
										</div>

										<?php
											// Right primary menu.
											if ( has_nav_menu( 'tertiary' ) ) {
												wp_nav_menu( array(
													'theme_location' => 'tertiary',
													'menu_class'     => 'theme-primary-menu',
													'container'      => '',
												) );
											} elseif ( is_user_logged_in() ) {
											?>
												<a href="<?php echo esc_url( get_admin_url() . 'nav-menus.php' ); ?>" class="add-menu"></a>
										<?php } ?>
									</div>

									<div class="m-col m2-col">
										<div class="header-action">
											<?php
											if ( class_exists( 'woocommerce' ) ) {
												zoa_wc_header_action();
											}
											?>
										</div>
										<button id="menu-toggle-btn"><span></span></button>
									</div>
								</div>
							</div>

							<span id="menu-overlay"></span>
						</header>
					</div>
					<?php
					break;
				case 'layout-7':
					if ( true == get_theme_mod( 'header_7_topbar', false ) ) {
						zoa_topbar();
					}
					?>
					<div class="menu-layout menu-layout-7">
						<header class="header-box">
							<div class="container" <?php zoa_schema_markup( 'navigation' ); ?>>
								<div class="header-container">
									<div class="m-col m1-col">
										<button id="menu-toggle-btn"><span></span></button>
										<?php get_search_form(); ?>
									</div>

									<div class="header-logo">
										<?php zoa_logo_image(); ?>
									</div>

									<div class="m-col m2-col">
										<div class="header-action">
											<?php
											if ( class_exists( 'woocommerce' ) ) {
												zoa_wc_header_action();
											}
											?>
										</div>
									</div>
								</div>

								<div class="menu-7-content">
									<?php
										if ( has_nav_menu( 'primary' ) ) {
											wp_nav_menu( array(
												'theme_location' => 'primary',
												'menu_class'     => 'theme-primary-menu',
												'container'      => '',
											) );
										} elseif ( is_user_logged_in() ) {
										?>
											<a href="<?php echo esc_url( get_admin_url() . 'nav-menus.php' ); ?>" class="add-menu"></a>
									<?php } ?>
								</div>
							</div>

							<span id="menu-overlay"></span>
						</header>
					</div>
					<?php
					break;
			endswitch;
		}
	endif;

	/*! PAGE HEADER SLUG: RETURN PAGE HEADER LAYOUT VALUE
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_page_header_slug' ) ):
		function zoa_page_header_slug() {

			$pid = get_queried_object_id();

			/* CUSTOMIZER
			***************************************************/
			$c_header = get_theme_mod( 'c_header_layout', 'default' );

			/* WOOCOMMERCE PAGE
			***************************************************/
			if ( class_exists( 'woocommerce' ) && is_woocommerce() ) {
				$c_header = get_theme_mod( 'shop_header_layout', 'layout-1' );
			}

			/* PAGE
			***************************************************/
			$page_header = true == zoa_elementor_page( $pid ) ? zoa_page_opt( $pid, 'p_header_layout' ) : null;
			if ( isset( $page_header ) && 'default' != $page_header ) {
				$c_header = $page_header;
			}

			return $c_header;
		}
	endif;

	/*! PAGE HEADER
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_page_header' ) ):
		function zoa_page_header() {
			if ( is_404() ) {
				return;
			}

			$c_header = zoa_page_header_slug();

			if ( 'disable' == $c_header ) {
				return;
			}
			?>
            <div class="page-header ph-<?php echo esc_attr( $c_header ); ?>">
                <div class="container">
					<?php
					/*PAGE TITLE*/
					if ( 'layout-1' == $c_header ):
						?>
                        <div id="theme-page-title">
							<?php zoa_page_title(); ?>
                        </div>
					<?php endif; ?>

					<?php /*BREADCRUMBS*/ ?>
                    <div id="theme-bread">
						<?php
						if ( function_exists( 'fw_ext_breadcrumbs' ) ) {
							fw_ext_breadcrumbs();
						}
						?>
                    </div>
					<?php
					if ( class_exists( 'woocommerce' ) &&
					     is_singular( 'product' ) &&
					     true == get_theme_mod( 'shop_single_nav', true )
					) {
						$prev = get_previous_post();
						$next = get_next_post();
						?>
                        <div class="shop-single-nav">
							<?php
							if ( ! empty( $prev ) ):
								?>
                                <a class="ssv-prev" href="<?php echo get_permalink( $prev->ID ); ?>"></a>
							<?php
							endif;

							if ( ! empty( $next ) ):
								?>
                                <a class="ssv-next" href="<?php echo get_permalink( $next->ID ); ?>"></a>
							<?php
							endif;
							?>
                        </div>
						<?php
					}
					?>
                </div>
            </div>
			<?php
		}
	endif;

	/*! PAGE TITLE
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_page_title' ) ):
		function zoa_page_title() {

			/*PAGE TITLE*/
			$title = get_the_title();

			/*BLOG TITLE*/
			$blog_title = get_theme_mod( 'blog_title', 'Blog' );

			/*SHOP TITLE*/
			$shop_title = get_theme_mod( 'shop_title', 'Shop' );

			?>
            <h1 class="page-title entry-title">
				<?php
				if ( is_day() ):
					printf( esc_html__( 'Daily Archives: %s', 'zoa' ), get_the_date() );
                elseif ( is_month() ):
					printf( esc_html__( 'Monthly Archives: %s', 'zoa' ), get_the_date( esc_html_x( 'F Y', 'monthly archives date format', 'zoa' ) ) );
                elseif ( is_home() ):
					echo esc_html( $blog_title );
                elseif ( is_author() ):
					$author = ( get_query_var( 'author_name' ) ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
					echo esc_html( $author->display_name );
                elseif ( is_year() ):
					printf( esc_html__( 'Yearly Archives: %s', 'zoa' ), get_the_date( esc_html_x( 'Y', 'yearly archives date format', 'zoa' ) ) );
                elseif ( class_exists( 'woocommerce' ) && is_shop() ):
					echo esc_html( $shop_title );
                elseif ( class_exists( 'woocommerce' ) && ( is_product_tag() || is_tag() ) ):
					esc_html_e( 'Tags: ', 'zoa' );
					single_tag_title();
                elseif ( is_page() || is_single() ):
					echo ! empty( $title ) ? esc_html( $title ) : esc_html__( 'This post has no title', 'zoa' );
                elseif ( is_tax() ):
					global $wp_query;
					$term      = $wp_query->get_queried_object();
					$tex_title = $term->name;
					echo esc_html( $tex_title );
                elseif ( is_search() ):
					esc_html_e( 'Search results', 'zoa' );
				else:
					esc_html_e( 'Archives', 'zoa' );
				endif;
				?>
            </h1>
			<?php
		}
	endif;

	/*! FOOTER SLUG
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_footer_display' ) ):
		function zoa_footer_display() {
			$pid = get_queried_object_id();

			/*CUSTOMIZE*/
			$show_footer = get_theme_mod( 'show_footer', true );

			/* BLOG SINGLE
			***************************************************/
			$single_blog_footer = function_exists( 'fw_get_db_post_option' ) ? fw_get_db_post_option( $pid, 'p_footer' ) : 'default';

			if ( function_exists( 'FW' ) && 'default' != $single_blog_footer && is_singular( 'post' ) ):
				$show_footer = 'enable' == $single_blog_footer ? true : false;
			endif;

			/*PAGE*/
			$id                 = get_queried_object_id();
			$page_footer_layout = true == zoa_elementor_page( $id ) ? zoa_page_opt( $id, 'p_footer_layout' ) : null;
			if ( isset( $page_footer_layout ) && 'default' != $page_footer_layout ) {
				$show_footer = 'enable' == $page_footer_layout ? true : false;
			}

			return $show_footer;
		}
	endif;

	/*! FOOTER
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_footer' ) ):
		function zoa_footer() {
			$show_footer = zoa_footer_display();
			if ( false == $show_footer ) {
				return;
			}

			$column          = get_theme_mod( 'ft_column', 4 );
			$copyright       = get_theme_mod( 'ft_copyright', '' );
			$copyright       = ! empty( $copyright ) ? $copyright : '&copy; ' . date( 'Y' ) . ' <strong>Zoa.</strong> &nbsp; • &nbsp; Privacy Policy &nbsp; • &nbsp; Terms of Use';
			$right_bot_right = get_theme_mod( 'ft_bot_right', '' );

			/*WIDGET*/
			if ( is_active_sidebar( 'footer-widget' ) ):
				?>
                <div class="footer-top">
                    <div class="container">
                        <div class="row widget-box footer-col-<?php echo esc_attr( $column ); ?>">
							<?php dynamic_sidebar( 'footer-widget' ); ?>
                        </div>
                    </div>
                </div>
			<?php endif; ?>

			<?php /*BASE*/ ?>
            <div class="footer-bot">
                <div class="container">
                    <div class="footer-logo"><?php zoa_footer_logo(); ?></div>
                    <div class="footer-copyright"><?php echo wp_kses_post( $copyright ); ?></div>
                    <div class="footer-bot-right"><?php echo wp_kses_post( $right_bot_right ); ?></div>
                </div>
            </div>
			<?php
		}
	endif;

	/*! FOOTER LOGO
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_footer_logo' ) ):
		function zoa_footer_logo() {
			$footer_logo = get_theme_mod( 'ft_logo' );
			if ( empty( $footer_logo ) ) {
				return;
			}
			?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <img src="<?php echo esc_url( get_theme_mod( 'ft_logo' ) ); ?>"
                     alt="<?php esc_attr_e( 'Footer logo', 'zoa' ); ?>">
            </a>
			<?php
		}
	endif;

	/*! FOOTER LOGO
	------------------------------------------------->*/
	if ( ! function_exists( 'zoa_topbar' ) ) {
		function zoa_topbar() {
			$topbar_left_content   = get_theme_mod( 'topbar_left', '<span class="topbar__tel">Hotline: <a href="tel:+01234567890">+01 234 567 890</a></span><ul class="menu-social topbar__social">
<li><a href="//facebook.com/zoa"></a></li>
<li><a href="//twitter.com/zoa"></a></li>
<li><a href="//instagram.com/zoa"></a></li>
</ul>' );
			$topbar_center_content = get_theme_mod( 'topbar_center', 'Summer sale discount 50&#37; off.' );
			$topbar_right_content  = get_theme_mod( 'topbar_right', '<div class="dropdown"><span class="dropdown__current">USD</span><div class="dropdown__content"><a href="#">USD</a><a href="#">EUR</a></div></div><div class="dropdown"><span class="dropdown__current">English</span><div class="dropdown__content"><a href="#">English</a><a href="#">French</a></div></div>' );
			?>
            <div class="topbar">
                <div class="container">
                    <div class="topbar__container">
                        <div class="topbar__left">
							<?php
							if ( ! empty( $topbar_left_content ) ) {
								echo wp_kses_decode_entities( $topbar_left_content );
							}
							?>
                        </div><!-- .topbar__left -->

                        <div class="topbar__center">
							<?php
							if ( ! empty( $topbar_center_content ) ) {
								echo wp_kses_decode_entities( $topbar_center_content );
							}
							?>
                        </div><!-- .topbar__center -->

                        <div class="topbar__right">
							<?php
							if ( ! empty( $topbar_right_content ) ) {
								echo wp_kses_decode_entities( $topbar_right_content );
							}
							?>
                        </div><!-- .topbar__right -->
                    </div><!-- .topbar__container -->
                </div><!-- .container -->
            </div><!-- .topbar -->
			<?php
		}
	}
}

/* AJAX
***************************************************/
{
	/*! CREATE AJAX URL
	------------------------------------------------->*/
	{
		add_action( 'wp_enqueue_scripts', 'zoa_ajax_url', 999 );
		function zoa_ajax_url() {
			wp_localize_script(
				'zoa-custom',
				'zoa_ajax',
				array(
					'url'   => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'zoa_product_nonce' )
				)
			);
		}
	}
}

/*ICON HEADER EMENU*/
if ( ! function_exists( 'zoa_wc_sidebar_action' ) ) :
	function zoa_wc_sidebar_action() {
		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		global $woocommerce;
		$page_account = get_option( 'woocommerce_myaccount_page_id' );
		$page_logout  = wp_logout_url( get_permalink( $page_account ) );

		if ( 'yes' == get_option( 'woocommerce_force_ssl_checkout' ) ) {
			$logout_url = str_replace( 'http:', 'https:', $logout_url );
		}

		$count = $woocommerce->cart->cart_contents_count;
		?>
        <ul class="sidebar-actions custom-sidebar-actions">
            <li class="sidebar-action custom-sidebar-login">
				<?php if ( ! is_user_logged_in() ) : ?>
                    <a href="<?php echo get_permalink( $page_account ); ?>" class="sidebar-action-link">
                        <span class="zoa-icon-user sidebar-action-icon"></span>
                        <span class="sidebar-action-text"><?php esc_html_e( 'Login', 'zoa' ); ?></span>
                    </a>
				<?php else : ?>
                    <a href="<?php echo get_permalink( $page_account ); ?>" class="sidebar-action-link">
                        <span class="zoa-icon-user sidebar-action-icon"></span>
                        <span class="sidebar-action-text"><?php esc_html_e( 'Dashboard', 'zoa' ); ?></span>
                    </a>
				<?php endif; ?>
            </li>
            <li class="sidebar-action custom-sidebar-cart">
                <a href="<?php echo wc_get_cart_url(); ?>" id="shopping-cart-btn"
                   class="sidebar-action-link js-cart-button">
                    <span class="zoa-icon-cart sidebar-action-icon"></span>
                    <span class="sidebar-action-text"><?php esc_html_e( 'Shopping cart', 'zoa' ); ?></span>
                    <span class="sidebar-action-cart shop-cart-count"><?php echo esc_html( $count ); ?></span>
                </a>
            </li>
            <?php if ( is_user_logged_in() ) : ?>
                <li class="sidebar-action custom-sidebar-cart">
                    <a href="<?php echo esc_url( $page_logout ); ?>" class="sidebar-action-link">
                        <span class="zoa-icon-log-out sidebar-action-icon"></span>
                        <span class="sidebar-action-text"><?php esc_html_e( 'Logout', 'zoa' ); ?></span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
		<?php
	}
endif;

/* INLINE CSS
***************************************************/
add_action( 'wp_enqueue_scripts', 'zoa_inline_style', 98 );
function zoa_inline_style() {

	$css = '';
	$id  = get_queried_object_id();
	$pid = '.page-id-' . $id;

	wp_add_inline_style( 'zoa-theme-style', $css );
}
