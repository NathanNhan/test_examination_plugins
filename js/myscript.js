
// Xác thực đầu vào cho form ems_form_data và submit form




//Client
//Ẩn thông báo 
jQuery("#alert_danger").hide();
jQuery("#alert_success").hide();
//Thêm emplyee
jQuery("#booking_form").validate({
    submitHandler : function() {
        var data = jQuery("#booking_form").serialize() + "&action=bookroom&param=save"
        console.log(data);
        jQuery.ajax({
            type: "POST",
            url: ajaxurl.baseURL,
            data: data,
            success: function (response) {
                var data = JSON.parse(response);
               
                if(data.status == '200'){
                    jQuery("#alert_success").show();
                    jQuery("#alert_success").html(data.message);
                    setTimeout(function () {
                        jQuery("#alert_success").hide();
                    },2000)
                }
                if(data.status == '201'){
                    jQuery("#alert_danger").show();
                    jQuery("#alert_danger").html(data.message);
                    setTimeout(function () {
                        jQuery("#alert_danger").hide();
                    },2000)
                }
             
             
            },
            error: function (response) {
                console.log(response);
            }
        })
    }
});




jQuery(document).on("change", ".status", function () {
    
        var id = jQuery(this).attr('data-id');
        var status_name = jQuery(this).val();
       
        var data = "action=statusUpdate&param=update&status=" + status_name + "&id="+id;
        
        jQuery.ajax({
            type: "POST",
            url: ajaxurl.baseURL, 
            data: data,
            success: function (response) {
                console.log(response);
            },
            error: function (error) {
                console.error("Error: ", error);
            }
        });
    
    
})


