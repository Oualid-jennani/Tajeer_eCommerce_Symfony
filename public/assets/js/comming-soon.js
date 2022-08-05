/*=====================
 Timer start
 ==========================*/
(function () {
    var deadlineDate = new Date('December 31, 2021 23:59:59').getTime();
    var countdownDays = document.querySelector('.countdown__days .number');
    var countdownHours = document.querySelector('.countdown__hours .number');
    var countdownMinutes = document.querySelector('.countdown__minutes .number');
    var countdownSeconds = document.querySelector('.countdown__seconds .number');
    setInterval(function () {
        var currentDate = new Date().getTime();
        var distance = deadlineDate - currentDate;
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor(distance % (1000 * 60 * 60 * 24) / (1000 * 60 * 60));
        var minutes = Math.floor(distance % (1000 * 60 * 60) / (1000 * 60));
        var seconds = Math.floor(distance % (1000 * 60) / 1000);
        countdownDays.innerHTML = days;
        countdownHours.innerHTML = hours;
        countdownMinutes.innerHTML = minutes;
        countdownSeconds.innerHTML = seconds;
    }, 1000);
})();
/*=====================
 Timer ends
 ==========================*/
