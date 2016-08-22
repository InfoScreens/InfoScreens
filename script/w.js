
function remove_item (item_id, cb) {
	var data = new FormData ();
	data.append ("itemId", item_id);
	console.log (data);
	$.ajax ({
		url: '../action.php?removeItem',
		method: 'POST',
		data: data,
		cache: false,
		dataType: 'text',
		processData: false,
		contentType: false,
		success: function (respond, textStatus, jqXHR) {
			console.log ("Success: "+respond+", "+textStatus+", "+jqXHR);
			cb.call (window);
		},
		error: function (jqXHR, textStatus, errorThrown) {
			console.log ("Fail: "+jqXHR+", "+textStatus+", "+errorThrown);
		}
	});
}

var day_s = 60 * 60 * 24,
	now_s = Math.floor (Date.now () / 1000);
var type_i = {stopwatch: 7, timer: 8};

function save_item (element_type, x1, y1, x2, y2, cb) {
	var item = {
		mon: device_id,
		date: new Date ().toISOString ().substr (0, 10),
		start: now_s,
		end: now_s - (now_s % day_s) + day_s,
		element_type: type_i[element_type],
		x1: x1,
		y1: y1,
		x2: x2,
		y2: y2,
		id: 0
	};

	item = JSON.stringify (item);
	var data = new FormData ();
	data.append ("data", item);
	$.ajax ({
		url: '../action.php?saveItem',
		method: 'POST',
		data: data,
		cache: false,
		dataType: 'text',
		processData: false,
		contentType: false,
		success: function (respond, textStatus, jqXHR) {
			console.log ("saveItem success: "+respond+", "+textStatus+", "+jqXHR);
			get_item_by_type (element_type, cb);
		},
		error: function (jqXHR, textStatus, errorThrown) {
			console.log ("saveItem fail: "+jqXHR+", "+textStatus+", "+errorThrown);
		}
	});
}

function get_item_by_type (type_s, cb) {
	var item = {
		mon: device_id,
		date: new Date ().toISOString().substr(0, 10),
		start: now_s,
		end: now_s - (now_s % day_s) + day_s,
		element_type: type_i[type_s],
		x1: x1,
		y1: y1,
		x2: x2,
		y2: y2,
		id: 0
	};
	var data = new FormData ();
	data.append ("mon", device_id);
	data.append ("date", new Date ().toISOString ().substr (0, 10));
	$.ajax ({
		url: '../action.php?openSchedule',
		method: 'POST',
		data: data,
		cache: false,
		dataType: 'text',
		processData: false,
		contentType: false,
		success: function (respond, textStatus, jqXHR) {
			var response = JSON.parse (respond),
				found = null;
			for (var i = 0; i < response.length; i ++){
				var item = response[i];
				if (item.element_type == type_i[type_s]) {
					found = item.itemId;
					break;
				}
			}
			cb.call (window, found);
		},
		error: function (jqXHR, textStatus, errorThrown) {
			console.log ("get item fail: "+jqXHR+", "+textStatus+", "+errorThrown);
		}
	});
}

var u_checkbox = null;

function init_checkbox (checkbox, type) {
	checkbox.data_loaded = false;
	checkbox.data_item_id = null;
	checkbox.data_element_type = type;
	checkbox.disabled = true;
	get_item_by_type (type, function (item_id) {
		checkbox.checked = item_id != null;
		checkbox.data_item_id = item_id;
		checkbox.data_loaded = true;
		checkbox.disabled = false;
	});
	$(checkbox).change (function () {
		var self = this;
		if (this.data_loaded) {
			if (this.checked) {
				$('#showModal').modal ('show');
				this.checked = false;
				this.disabled = true;
				u_checkbox = this;
			} else {
				this.disabled = true;
				remove_item (this.data_item_id, function () {
					self.data_item_id = null;
					self.disabled = false;
				});
			}
		}
	});
}

$(document).ready (function () {

	if (!device_id) {
		var get_parameters = URI ().search (true);
		if (get_parameters.hasOwnProperty ("device_id")) {
			device_id = get_parameters.device_id;
		}
	}
	$(".b_z_save").click (function () {
		save_item (
			u_checkbox.data_element_type,
			$("#x1").val (), $("#y1").val (),
			$("#x2").val (), $("#y2").val (),
			function (item_id) {
				u_checkbox.data_item_id = item_id;
				$('#showModal').modal ('hide');
				u_checkbox.checked = true;
				u_checkbox.disabled = false;
			}
		);
	});

	init_checkbox ($("#stopwatch_z")[0], "stopwatch");
	init_checkbox ($("#timer_z")[0], "timer");
});
