
function check_user_authorized (require_admin) {
    perform_action (
        "get_current_user_info",
        null,
        function (response) {
            if (response.errored ()) {
                window.location = "login.php";
            } else {
                if (require_admin && !response.data.is_admin) {
                    window.location = "admin.php";
                }
            }
        }
    );
}

function display_error (error) {
    notif ({
        msg: get_error_text (error),
        type: "error",
        position: "center",
        timeout: 3000,
        multiline: true
    });
}

function action_callback_display_error (success) {
    return function (response) {
        if (response.errored ()) {
            display_error (response.error);
        } else {
            success.call (window, response);
        }
    };
}
