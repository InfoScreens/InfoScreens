
function check_user_authorized (require_admin) {
    perform_action (
        "get_currrent_user_info",
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

