
var device_id;
var stopwatch;

window.Stopwatch = (function () {
	var tick_time = 1000;
	function tick (state, self) {
		self.display ();
	}
	return function () {
		var net_state = new State (device_id, "stopwatch"),
			state = {
				working: false,
				start_time: null,
				all_time: 0
			},
			data = {
				interval: null
			};
		function set_state (working, start_time, all_time) {
			state.working = working;
			state.start_time = start_time;
			state.all_time = all_time;
			net_state.set (state);
		}
		this.start = function () {
			if (!state.working) {
				set_state (true, Date.now (), state.all_time);
				data.interval = setInterval (tick, tick_time, state, this);
			}
			this.display ();
		};
		this.stop = function () {
			if (state.working) {
				set_state (false, null, state.all_time + (Date.now () - state.start_time));
				clearInterval (data.interval);
				data.interval = null;
			}
			this.display ();
		};
		this.reset = function () {
			if (!state.working) {
				set_state (state.working, null, 0);
			}
			this.display ();
		};
		this.display = function () {
			var d = state.all_time + (state.working ? (Date.now () - state.start_time) : 0);
			console.log ('stopwatch ' + (d / 1000).toFixed (2) + 's');
			$("#stopwatch_time").text ((d / 1000).toFixed (2));
		};
	};
}) ();

function Timer () {
	var net_state = new State (device_id, "timer"),
		state = {
			working: false,
			start_time: null,
			left_time: 0
		},
		data = {
			interval: null,
			tick_time: 1000
		},
		self = this;
	function tick () {
		if (state.working) {
			if (state.left_time - (Date.now () - state.start_time) < 0) {
				self.stop ();
				self.reset ();
			}
		}
		self.display ();
	}
	function set_state (working, start_time, left_time) {
		state.working = working;
		state.start_time = start_time;
		state.left_time = left_time;
		net_state.set (state);
	}
	this.start = function () {
		if (!state.working) {
			set_state (true, Date.now (), state.left_time);
			data.interval = setInterval (tick, data.tick_time, state);
			this.display ();
		}
	};
	this.stop = function () {
		if (state.working) {
			set_state (false, null, state.left_time - (Date.now () - state.start_time));
			clearInterval (data.interval);
			data.interval = null;
			this.display ();
		}
	};
	this.reset = function () {
		this.set_time (0);
	};
	this.set_time = function (time_ms) {
		if (!state.working) {
			set_state (state.working, null, time_ms);
			this.display ();
		}
	};
	this.display = function () {
		var d = state.left_time - (state.working ? (Date.now () - state.start_time) : 0);
		console.log ('timer ' + (d / 1000).toFixed (2) + 's');
		$("#timer_time_input").val ((d / 1000).toFixed (2));
	};
}

$(document).ready (function () {

	var get_parameters = URI ().search (true);
	if (get_parameters.hasOwnProperty ("device_id")) {
		device_id = get_parameters.device_id;
	}

	stopwatch = new Stopwatch ();

	$("#stopwatch_start").click (function () {
		stopwatch.start ();
	});
	$("#stopwatch_stop").click (function () {
		stopwatch.stop ();
	});
	$("#stopwatch_reset").click (function () {
		stopwatch.reset ();
	});
	stopwatch.display ();

	timer = new Timer ();

	$("#timer_start").click (function () {
		timer.start ();
	});
	$("#timer_stop").click (function () {
		timer.stop ();
	});
	$("#timer_reset").click (function () {
		timer.reset ();
	});
	$("#timer_time_input").change (function () {
		timer.set_time ((parseFloat (this.value) || 0) * 1000);
	});
	timer.display ();
});
