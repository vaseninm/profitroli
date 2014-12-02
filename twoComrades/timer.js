$(document).ready(function(){
    var timer = new Timer();

    timer.onTick = function() {
        $('#time').text(this.getTime().toLocaleTimeString());
    }

    $('#timezone').on('change', function(){
        timer.setTimeZone(parseInt($(this).val()));
        timer.onTick();
    });

    setInterval(timer.sync.bind(timer), 60000);
});

function Timer() {
    this.init();
}

Timer.prototype.serverTime = 0;
Timer.prototype.timeZone = +3;
Timer.prototype.timeUrl = 'http://api.pt.tld/time.php';

Timer.prototype.init = function() {
    this.sync();
    this.tick();
    setInterval(this.tick.bind(this), 1000);
};

Timer.prototype.sync = function() {
    var self = this;
    $.get(this.timeUrl, {}, function(result) {
        self.serverTime = result;
    });
};

Timer.prototype.setTimeZone = function(timeZone) {
    this.timeZone = timeZone;
};

Timer.prototype.getTime = function() {
    var date = new Date;

    date.setTime((this.serverTime + this.timeZone * 3600 + date.getTimezoneOffset() * 60) * 1000);

    return date;
};

Timer.prototype.tick = function() {
    this.serverTime++;
    this.onTick();
};

Timer.prototype.onTick = function() {

};




