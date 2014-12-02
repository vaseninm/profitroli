$(document).ready(function(){

});

function Timer() {
    this.init();
}

Timer.prototype.serverTime = 0;
Timer.prototype.timeZone = +4;
Timer.prototype.timeUrl = 'http://api.pt.tld/time.php';

this.prototype.init = function() {

};

this.prototype.sync = function() {
    var self = this;
    $.get(this.timeUrl, {}, function(result) {
        self.serverTime = result;
    });
};

this.prototype.setTimeZone = function(timeZone) {
    this.timeZone = timeZone;
};

this.prototype.getServerTime = function() {
    var time = this.serverTime + this.timeZone;

    return time;
};
