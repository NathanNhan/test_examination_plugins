
new DataTable('#list_employees');
// Xác thực đầu vào cho form ems_form_data và submit form




//Client
//Ẩn thông báo 
jQuery("#alert_danger").hide();
//Thêm emplyee
jQuery("#booking_form").validate({
    submitHandler : function() {
        var data = "action=bookroom&param=save&" + jQuery("#booking_form").serialize();
        // console.log(data);
        // Gửi AJAX xuống server để thêm thông tin đặt phòng của khách vào database
    }
});




jQuery(document).on("click", ".status", function () {
    
        var id = jQuery(this).attr('data-id');
        var status_name = jQuery(this).val();
        var data = "action=statusUpdate&param=update&status=" + status_name + "&id="+id;


        // Gửi AJAX xuống server để cập nhật trạng thái của phòng 
    
    
})


