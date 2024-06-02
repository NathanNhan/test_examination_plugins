<?php 

 global $wpdb;
 $hotels = $wpdb->get_results("SELECT * from wp_book_hotel", ARRAY_A);


?>
    <table id="list_employees" class="display" style="width:100%">
        <thead>
            <tr>
                <th>STT</th>
                <th>Email</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Room</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($hotels as $hotel ) : ?>
            <tr>
                <td><?php echo $hotel["id"] ?></td>
                <td><?php echo $hotel["email"] ?></td>
                <td><?php echo $hotel["name"] ?></td>
                <td><?php echo $hotel["phone"] ?></td>
                <td>
                  <?php echo $hotel["room_no"] ?>
                </td>
                <td><?php echo $hotel["start_date"] ?></td>
                <td><?php echo $hotel["end_date"] ?></td>
                <td>
                <select class="form-select status" data-id="<?php echo $hotel['id']; ?>" aria-label="Default select example">
                    <?php
                    $statuses = ['Open status', 'booked', 'returned', 'closed'];
                    
                    foreach ($statuses as $status) {
                        // Kiểm tra xem trạng thái hiện tại có trùng với trạng thái của khách sạn không
                        $selected = ($hotel['status'] == $status) ? ' selected' : '';
                        echo "<option value='$status'$selected>$status</option>";
                    }
                    ?>
                </select>

                </td>
            <?php endforeach; ?>

            </tr>   
        </tbody>
        <tfoot>
            
        </tfoot>
    </table>

