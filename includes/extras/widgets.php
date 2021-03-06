<?php

function mbt_register_widgets() {
	register_widget("MBT_Featured_Book");
}
add_action('widgets_init', 'mbt_register_widgets');

/*---------------------------------------------------------*/
/* Featured Books Widget                                   */
/*---------------------------------------------------------*/

class MBT_Featured_Book extends WP_Widget {
	function MBT_Featured_Book() {
		parent::WP_Widget('mbt_featured_book', 'MyBookTable Featured Book');
		add_action('admin_enqueue_scripts', array('MBT_Featured_Book', 'enqueue_widget_js'));
		$this->defaultargs = array('selectmode' => 'by_date', 'featured_book' => '', 'image_size' => 'medium');
	}

	function enqueue_widget_js() {
		global $pagenow;
		if($pagenow == 'widgets.php') {
			wp_enqueue_script("mbt-widgets", plugins_url('js/widgets.js', dirname(dirname(__FILE__))), 'jquery', '', true);
		}
	}

	function widget($args, $instance) {
		extract(wp_parse_args($instance, $this->defaultargs));

		echo($args['before_widget']);

		if($selectmode == 'manual_select' and !empty($featured_book)) {
			$book = get_post($featured_book);
		} else {
			$wp_query = new WP_Query(array('post_type' => 'mbt_book', 'orderby' => 'date', 'posts_per_page' => 1));
			$book = $wp_query->post;
		}

		if(!empty($book)) {
			$permalink = get_permalink($book->ID);
			?>
				<div class="mbt-featured-book-widget">
					<h1 class="mbt-book-title"><a href="<?php echo($permalink); ?>"><?php echo(get_the_title($book->ID)); ?></a></h1>
					<a href="<?php echo($permalink); ?>"><?php echo(mbt_get_book_image($book->ID, array('class' => $image_size))); ?></a>
					<div class="mbt-book-blurb"><?php echo(mbt_get_book_blurb($book->ID, true)); ?></div>
					<div class="mbt-book-buybuttons">
						<?php
							$buybuttons = mbt_get_buybuttons($book->ID, array('display' => 'featured'));
							echo(mbt_format_buybuttons($buybuttons));
						?>
						<div style="clear:both;"></div>
					</div>
				</div>
			<?php
		}

		echo('<div style="clear:both;"></div>');
		echo($args['after_widget']);
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['selectmode'] = strip_tags($new_instance['selectmode']);
		$instance['featured_book'] = strip_tags($new_instance['featured_book']);
		$instance['image_size'] = $new_instance['image_size'];
		return $instance;
	}

	function form($instance) {
		extract(wp_parse_args($instance, $this->defaultargs));
		?>

		<p>
			<label>Book image size:</label><br>
			<?php foreach(array('small', 'medium', 'large') as $size) { ?>
				<input type="radio" name="<?php echo($this->get_field_name('image_size')); ?>" value="<?php echo($size); ?>" <?php echo($image_size == $size ? ' checked' : ''); ?> ><?php echo(ucfirst($size)); ?><br>
			<?php } ?>
		</p>
		<p>
			<label for="<?php echo($this->get_field_id('selectmode')); ?>">Choose how to select the featured book:</label>
			<select class="mbt_featured_book_selectmode" name="<?php echo($this->get_field_name('selectmode')); ?>" id="<?php echo($this->get_field_id('selectmode')); ?>">
				<option value="by_date"<?php selected($selectmode, 'by_date'); ?>>Most Recent Book</option>
				<option value="manual_select"<?php selected($selectmode, 'manual_select'); ?>>Choose Manually</option>
			</select>
		</p>
		<div class="mbt_featured_book_manual_selector" <?php echo($selectmode == 'manual_select' ? '' : 'style="display:none"'); ?>>
			<p>
				<label for="<?php echo($this->get_field_id('featured_book')); ?>">Select Book:</label>
				<select name="<?php echo($this->get_field_name('featured_book')); ?>" id="<?php echo($this->get_field_id('featured_book')); ?>">
					<option value=""> -- Choose One -- </option>
					<?php
						$wp_query = new WP_Query(array('post_type' => 'mbt_book', 'orderby' => 'title', 'order' => 'ASC', 'posts_per_page' => -1));
						if(!empty($wp_query->posts)) {
							foreach($wp_query->posts as $book) {
								echo '<option value="'.$book->ID.'" '.selected($featured_book, $book->ID).' >'.substr($book->post_title, 0, 25).(strlen($book->post_title) > 25 ? '...' : '').'</option>';
							}
						}
					?>
				</select>
			</p>
		</div>

		<?php
	}
}
