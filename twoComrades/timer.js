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

Timer.prototype.clientServerDiff = 0;
Timer.prototype.timeZone = +3;
Timer.prototype.timeUrl = 'http://api.pt.tld/time.php';

Timer.prototype.init = function() {
    this.sync();
    this.tick();
    setInterval(this.tick.bind(this), 100);
};

Timer.prototype.sync = function() {
    var self = this;
    $.get(this.timeUrl, {}, function(result) {
        self.clientServerDiff = (new Date).getTime() - result * 1000;
    });
};

Timer.prototype.setTimeZone = function(timeZone) {
    this.timeZone = timeZone;
};

Timer.prototype.getTime = function() {
    var date = new Date;

    date.setTime(
        ((new Date).getTime() - this.clientServerDiff + this.timeZone * 3600000 + date.getTimezoneOffset() * 60000)
    );

    return date;
};

Timer.prototype.tick = function() {
    this.serverTime++;
    this.onTick();
};

Timer.prototype.onTick = function() {

};




