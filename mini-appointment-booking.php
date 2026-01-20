<?php
/*
Plugin Name: Mini Appointment Booking
Description: Simple appointment booking plugin with admin dashboard entries.
Version: 1.0
Author: Shailja
*/

if (!defined('ABSPATH')) exit;

/* ---------- CREATE TABLE ON ACTIVATE ---------- */
register_activation_hook(__FILE__, 'mab_create_table');
function mab_create_table(){
    global $wpdb;
    $table = $wpdb->prefix.'appointments';

    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(100),
        email VARCHAR(100),
        date DATE,
        time VARCHAR(20),
        PRIMARY KEY (id)
    ) $charset;";

    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/* ---------- SHORTCODE FORM ---------- */
add_shortcode('appointment_form','mab_form');
function mab_form(){

    if(isset($_POST['mab_submit'])){
        global $wpdb;
        $table = $wpdb->prefix.'appointments';

        $wpdb->insert($table,[
            'name'  => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'date'  => $_POST['date'],
            'time'  => $_POST['time']
        ]);

        echo "<p style='color:green'>Appointment Booked Successfully!</p>";
    }

    ob_start(); ?>

    <form method="post" style="max-width:400px">
        <input type="text" name="name" placeholder="Your Name" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="date" name="date" required><br><br>
        <input type="time" name="time" required><br><br>
        <button name="mab_submit">Book Appointment</button>
    </form>

    <?php return ob_get_clean();
}

/* ---------- ADMIN MENU ---------- */
add_action('admin_menu','mab_admin_menu');
function mab_admin_menu(){
    add_menu_page(
        'Appointments',
        'Appointments',
        'manage_options',
        'mab-appointments',
        'mab_admin_page'
    );
}

/* ---------- ADMIN PAGE ---------- */
function mab_admin_page(){
    global $wpdb;
    $table = $wpdb->prefix.'appointments';
    $data = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
    ?>

    <div class="wrap">
        <h2>Appointments</h2>
        <table class="widefat striped">
            <tr>
                <th>Name</th><th>Email</th><th>Date</th><th>Time</th>
            </tr>

            <?php foreach($data as $d){ ?>
            <tr>
                <td><?php echo esc_html($d->name); ?></td>
                <td><?php echo esc_html($d->email); ?></td>
                <td><?php echo esc_html($d->date); ?></td>
                <td><?php echo esc_html($d->time); ?></td>
            </tr>
            <?php } ?>

        </table>
    </div>
<?php }
