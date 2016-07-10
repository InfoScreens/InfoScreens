
var Errors = {
    SUCCESS: 0,
    DB_QUERY_FAILED: 1,
    EMAIL_FORMAT_INVALID: 2,
    PASSWORD_TOO_SHORT: 3,
    NAME_IS_EMPTY: 4,
    SURNAME_IS_EMPTY: 5,
    AJAX_ERROR: 6,
    UNKNOWN_ACTION: 7,
    NOT_AUTHORIZED: 8
};

var ErrorTexts = {
    0: "Success",
    1: "DB query failed",
    2: "Email format is invalid",
    3: "Password is too short",
    4: "Name is empty",
    5: "Surname is empty",
    6: "AJAX error"
};

function get_error_text (error) {

    if (error in ErrorTexts) {
        return ErrorTexts[error];
    }

    return "unknown";
}

function Response (object) {

    this.data = object.data;
    this.error = object.error;
}

Response.prototype.errored = function () {
    return this.error != Errors.SUCCESS;
};

var ajax_error_response = new Response ({data: null, error: Errors.AJAX_ERROR});

function perform_action (action, params, callback) {

    $.ajax ({
        url: "/action.php",
        data: {
            action: action,
            params: JSON.stringify (params)
        },
        type: "POST",
        success: function (data) {
            callback (new Response (data));
        },
        error: function (xhr, status, error) {
            callback (ajax_error_response);
        }
    });
}

