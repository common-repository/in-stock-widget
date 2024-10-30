<?php
 /**
 Plugin Name: in stock product widget
 Plugin URI: http://
 Description: in stock products filter widget, ابزارک فیلتر محصولات موجود
 Author: movahedian1@gmail.com
 Version: 1.3.1
 Author URI: ...
 */

add_action('admin_menu', 'instockwidget_setup_menu');

function instockwidget_setup_menu(){
add_menu_page( 'in stock products widget Plugin page', 'in stock widget', 'manage_options', 'instockwidget-plugin', 'instockwidget_init' );
}

function instockwidget_init(){
echo "<h1>in stock products filter widget setting</h1>";
echo "<br>";

$newnonce = wp_create_nonce( 'instockwidget_nonce' );
$nonce = $_REQUEST['_wpnonce'];
if( isset($_POST['submit']) && current_user_can( 'administrator' ) && wp_verify_nonce( $nonce, 'instockwidget_nonce' )){

		if (isset($_POST['changeorder']) == true)
			update_option('instockwidget_changeorder', true);
		else
			update_option('instockwidget_changeorder', false);
		// echo "با موفقیت ذخیره شد.";
		
		if (isset($_POST['removeprice']) == true)
			update_option('instockwidget_removeprice', true);
		else
			update_option('instockwidget_removeprice', false);
		// echo "با موفقیت ذخیره شد.";
		echo "<div id='setting-error-settings_updated' class='updated settings-error notice is-dismissible'> 
	<p><strong>تنظیمات ذخیره شد.</strong></p><button type='button' class='notice-dismiss'><span class='screen-reader-text'>بستن این اعلان.</span></button></div>";
		

	
}

?>

<div class="wrap">
	<form action="" method="post">
		<label>change order</label>
		<input type="checkbox" name="changeorder" <?php checked( '1', get_option( 'instockwidget_changeorder' ) ); ?>>
		<br>
		با انتخاب این گزینه محصولات ناموجود به انتهای لیست محصولات میروند.
		<br>
		by select this option, show in stock product first in list of products
		<br>
		<label>remove price</label>
		<input type="checkbox" name="removeprice" <?php checked( '1', get_option( 'instockwidget_removeprice' ) ); ?>>
		<br>
		با انتخاب این گزینه قیمت محصولات ناموجود نمایش داده نمیشود
		<br>
		by select this option, price Removes from sold out product page

		<br>
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $newnonce ?>" />
		<input type="submit" name="submit" value="ذخیره">
	</form>
</div>

<?php
}

if(isset($_GET['instock'])){

	$instock = sanitize_text_field($_GET['instock']);
	

	if($instock == "true"){
		
		add_action( 'pre_get_posts', 'iconic_hide_out_of_stock_products' );
		function iconic_hide_out_of_stock_products( $q ) {

			if ( ! $q->is_main_query() || is_admin() ) {
				return;
			}

			if ( $outofstock_term = get_term_by( 'name', 'outofstock', 'product_visibility' ) ) {

				$tax_query = (array) $q->get('tax_query');

				$tax_query[] = array(
					'taxonomy' => 'product_visibility',
					'field' => 'term_taxonomy_id',
					'terms' => array( $outofstock_term->term_taxonomy_id ),
					'operator' => 'NOT IN'
				);

				$q->set( 'tax_query', $tax_query );
			}
			remove_action( 'pre_get_posts', 'iconic_hide_out_of_stock_products' );
		}
	}
	
}




class Instock_Widget extends WP_Widget {
 
    function __construct() {
 
        parent::__construct(
            'instockwidget',  // Base ID
            'in stock widget'   // Name
        );
 
        add_action( 'widgets_init', function() {
            register_widget( 'Instock_Widget' );
        });
 
    }
 
    public $args = array(
        'before_title'  => '<h4 class="widgettitle">',
        'after_title'   => '</h4>',
        'before_widget' => '<div class="widget-wrap">',
        'after_widget'  => '</div></div>'
    );
 
    public function widget( $args, $instance ) {
 
        echo $args['before_widget'];
 
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
 
        echo '<div class="textwidget">';
 
        // echo esc_html__( $instance['text'], 'text_domain' );
		$instock = sanitize_text_field($_GET['instock']);
		if($instock == "false" or !$instock){
			//echo "<a href='?instock=true'>نمایش کالاهای موجود</a>";
			echo "<label class='switch'>
					<a href='?instock=true'>
					<input type='checkbox'>
					<span class='slider round'></span>
				  </label></a>";
		}else{
			echo "<label class='switch'>
					<a href='?instock=false'>
					<input type='checkbox' checked>
					<span class='slider round'></span>
				  </label></a>";

		}
 
        echo '</div>';
 
        echo $args['after_widget'];
 
    }
     public function form( $instance ) {
 
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
        ?>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
 
    }
 
    public function update( $new_instance, $old_instance ) {
 
        $instance = array();
 
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
 
        return $instance;
    }

 
}
$Instock_Widget = new Instock_Widget();
		
function instockwidget_js_adds_to_the_head() {
    wp_register_script( 'add-instockwidget-js', plugin_dir_url( __FILE__ ) . 'javascript.js', array('jquery'),'',true  ); 
    wp_enqueue_script( 'add-instockwidget-js' );

}
add_action( 'wp_enqueue_scripts', 'instockwidget_js_adds_to_the_head' ); 


 
function instockwidget_load_plugin_css() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_enqueue_style( 'add-instockwidget-style', $plugin_url . 'stylesheet.css' );

}
add_action( 'wp_enqueue_scripts', 'instockwidget_load_plugin_css' );


 
add_filter( 'woocommerce_get_catalog_ordering_args', 'instockwidget_first_sort_by_stock_amount', 9999 );
 
function instockwidget_first_sort_by_stock_amount( $args ) {
	if ( get_option( "instockwidget_changeorder" ) == true){	
	   $args['orderby'] = 'meta_value';
	   $args['order'] = 'ASC';
	   $args['meta_key'] = '_stock_status';
	}
   return $args;
}


add_filter( "woocommerce_variable_sale_price_html", "instock_remove_prices", 10, 2 );
add_filter( "woocommerce_variable_price_html", "instock_remove_prices", 10, 2 );
add_filter( "woocommerce_get_price_html", "instock_remove_prices", 10, 2 );

function instock_remove_prices( $price, $product ) {
	if (get_option( 'instockwidget_removeprice' ) == true and ! $product->is_in_stock()) {
		$price = "";
	}
	return $price;
}		

add_filter( 'woocommerce_sale_flash', 'custom_hide_sales_flash' );

function custom_hide_sales_flash($html) {
	global $product;
	if (get_option( 'instockwidget_removeprice' ) == true and ! $product->is_in_stock()){
		$html = '<style>.sale-item.product-label{display: none !important;}</style>';
	}
		return $html;
}



?>