
function State (device_id, app_id) {
	this.set = function (state) {
		$.post ("/state.php?method=set_state&data=" + window.escape (JSON.stringify ({
			device_id: device_id,
			app_id: app_id,
			state: state
		})));
	}
}
