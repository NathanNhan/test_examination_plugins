<?php 

/*
 * Plugin Name:       Book hotel
 * Plugin URI:        https://trongnhandev.com/plugins/the-basics/
 * Description:       Handle the basics with this plugin.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Trong Nhan
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       book-hotel
 * Domain Path:       /languages
 */

defined("ABSPATH") or die("You can not access directly");
define("PLUGIN_PATH", plugin_dir_path( __FILE__ ));
define("PLUGIN_URI", plugin_dir_url( __FILE__ ));
//admin_menu hook
// Design patterns -> SingleTon
if(!class_exists('BookHotel')) {
  class BookHotel {
  public function __construct() {
   add_action('admin_enqueue_scripts', array($this, 'load_assets'));
   add_action('wp_enqueue_scripts', array($this, 'load_assets'));
   add_action( 'admin_menu', array($this, 'custom_admin_menu') );
   add_shortcode('booking_form', array($this, 'book_room_form'));

  //Lưu thông tin đặt phòng bằng ajax 
   add_action("wp_ajax_bookroom",array($this, "booking_ajax_handler"));
   add_action('wp_ajax_nopriv_bookroom', array($this, "booking_ajax_handler"));
   //Cập nhật trạng thái phòng
   add_action("wp_ajax_statusUpdate",array($this, "update_status_room"));
  }

  function custom_admin_menu() {
    add_menu_page( 'Booking', 'Booking', 'manage_options', 'all-booking', array($this, 'render_booking'), '', 10 );
  }
  //List Employees
  function render_booking() {
    include_once(PLUGIN_PATH."/pages/list_booking.php");

  }
  // shortcode book room
  function book_room_form() {
     $html= "";
     $html .= "
     <div class='alert alert-danger' role='alert' id='alert_danger'></div>
     <div class='alert alert-success' role='alert' id='alert_success'>
     </div>
     <form id='booking_form' action='javascript:void(0)' method='POST'>
                <div class='form-group'>
                  <label for='exampleFormControlSelect1'>Email</label>
                  <input type='email' name='email' class='form-control' id='exampleInputEmail1' aria-describedby='emailHelp' placeholder='Enter email'>
                </div>
                <div class='form-group'>
                  <label for='exampleFormControlSelect1'>Name</label>
                  <input type='text' name='name' class='form-control' id='exampleInputEmail1' aria-describedby='emailHelp' placeholder='Enter name'>
                </div>
                <div class='form-group'>
                <label for='exampleFormControlSelect1'>Phone</label>
                  <input type='text' name='phone' class='form-control' id='exampleInputEmail1' aria-describedby='emailHelp' placeholder='Enter phone'>
                </div>
                
                 <div class='form-group'>
                  <label for='exampleFormControlSelect1'>Room select</label>
                  <select class='form-control' id='exampleFormControlSelect1' name='room'>
                    <option>20</option>
                    <option>25</option>
                    <option>10</option>
                    <option>6</option>
                    <option>7</option>
                  </select>
                 </div>

                <div class='form-group'>
                <label for='exampleFormControlSelect1'>Start Date</label>
                  <input type='date' name='startdate' class='form-control' id='exampleInputEmail1' aria-describedby='emailHelp' placeholder='Enter phone'>
                </div>

                 <div class='form-group'>
                  <label for='exampleFormControlSelect1'>End Date</label>
                  <input type='date' name='enddate' class='form-control' id='exampleInputEmail1' aria-describedby='emailHelp' placeholder='Enter phone'>
                </div>
                 <div class='form-group text-center mt-3'>
                 <button type='submit' class='btn btn-primary text-center'>Book Now</button>
                   
                 </div>
              </form>";
      return $html;
  }


  //Tạo table khi kích hoạt plugin 
    function create_table_ems() {
      global $wpdb; 
      $table_name = $wpdb->prefix . 'book_hotel';  

      $charset_collate = $wpdb->get_charset_collate(); // Lấy charset và collation

      $sql = "CREATE TABLE $table_name (
        id int NOT NULL AUTO_INCREMENT,
        email varchar(255) NOT NULL,
        name varchar(255) NOT NULL,
        phone varchar(20) NOT NULL,
        room_no int NOT NULL,
        status varchar(255) NOT NULL DEFAULT 'booked',
        start_date date NOT NULL,
        end_date date NOT NULL, 
        created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
      ) $charset_collate;";  // Sử dụng biến $charset_collate để đảm bảo charset phù hợp
      
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
  }


  // Xóa table khi hủy kích hoạt plugin 
  function drop_table_ems() {
    global $wpdb;
    $table_prefix = $wpdb->prefix; // wp_
    $sql = "DROP TABLE IF EXISTS {$table_prefix}book_hotel";
    $wpdb->query($sql);

   
  }

  //Nhúng thư viện js và css vào trong plugin 
  function load_assets() {
    //Nhúng thư viện css
    wp_enqueue_style( 'bootstrap_min_css', PLUGIN_URI."css/bootstrap.min.css", array(), '1.0.0', 'all' );
    wp_enqueue_style('dataTables_min_css', PLUGIN_URI."css/dataTables.min.css", array(), '1.0.0', 'all');
    wp_enqueue_style('mystyle_css', PLUGIN_URI."css/mystyle.css", array(), '1.0.0', 'all');
    //Nhúng thư viện js
    wp_enqueue_script( 'bootstrap_min_js', PLUGIN_URI."js/bootstrap.min.js", array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'dataTable_min_js', PLUGIN_URI."js/dataTables.min.js", array('jquery'), '1.0.0', true );
    wp_enqueue_script('jquery_validate', PLUGIN_URI."js/jquery.validate.min.js", array('jquery'), '1.0.0', true);
    wp_enqueue_script('myscript', PLUGIN_URI."js/myscript.js", array('jquery'), '1.0.0', true);
    
    //Nhúng đường dẫn admin_ajax vào trong myscript để sử dụng
   
    wp_localize_script('myscript', 'ajaxurl', array(
        'baseURL' => admin_url("admin-ajax.php"),
     
    ));

  }


  //call ajax phía backend
  function booking_ajax_handler() {
  global $wpdb;
  //Đọc danh sách các phòng với trạng thái đã được đặt
  //Nếu phòng từ client gửi xuống mà == với phòng có trạng thái được đặt từ database
  //Thì trả về thông báo là phòng đã được đặt. Vui lòng chọn phòng khác
   $results = $wpdb->get_results("SELECT * from wp_book_hotel",ARRAY_A);
   foreach($results as $result) {
    if($result["room_no"] == $_REQUEST["room"] && $result['status'] == 'booked') {
      print_r(json_encode(array("status" => "201", "message" => "Your Room Unavailable. Please choose another room")));
      wp_die();
    } 
   }
   //Ngược lại , thì lưu thông tin phòng đã đặt xuống cơ sở dữ liệu
   if(isset($_REQUEST['param']) && $_REQUEST['param']=="save") {
       $wpdb->insert("{$wpdb->prefix}book_hotel",array(
        "email" => $_REQUEST["email"],
        "name" => $_REQUEST["name"],
        "phone" => $_REQUEST["phone"],
        "room_no" => $_REQUEST["room"],
        "start_date" => $_REQUEST["startdate"],
        "end_date" => $_REQUEST["enddate"],
        "status" => "booked"
       ));
       print_r(json_encode(array("status" => "200", "message" => "Đặt phòng thành công")));
       wp_die();
    }

  wp_die();
  }




  //Update status room
    function update_status_room() {
      if (isset($_REQUEST['param']) && $_REQUEST['param'] == "update") {
      global $wpdb;
      
      $status = isset($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : '';
      $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '';
      
      // Kiểm tra id có hợp lệ ko 
      if($id > 0 ){
        $update = $wpdb->update(
          $wpdb->prefix.'book_hotel',
          array('status' => $status),
          array('id' => $id)
        );
        if($update !== false){
          wp_send_json_success(array(
            "status" => '200',
            "message" => "Cập nhật thành công")
          );
          wp_die();
        }else{
          wp_send_json_error(array(
            "status" => '201',
            "message" => "Cập nhật thất bại!"
          ));
          wp_die();
        }
      } else {
        wp_send_json_error(array(
          "status" => '400',
          "message" => "ID không hợp lệ"
        ));
      }
    }

  }
}
  $plugins = new BookHotel();



  //Kích hoạt plugin + Tạo table wp_ems_form_data
  register_activation_hook( __FILE__, array($plugins, 'create_table_ems'));

  // Hủy kích hoạt plugin => Xóa table wp_ems_form_data
  register_deactivation_hook( __FILE__, array($plugins, 'drop_table_ems'));
}





