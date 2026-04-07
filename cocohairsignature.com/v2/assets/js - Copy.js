var bH = document.baseURI + "", apiUrl = bH + "/core/api.php";


var loader = {
    start: function () {
        $("body").append('<div class="loaderstartstop" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:999999;background: rgb(0 0 0 / 69%);display:flex;justify-content: center;align-items:center;">' +
            '<div style="height:95px;width:95px"><svg style="width:100%;height:100%" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" width="200" height="200" style="shape-rendering: auto; display: block; background: rgb(255, 255, 255);" xmlns:xlink="http://www.w3.org/1999/xlink"><g><rect x="19" y="19" width="20" height="20" fill="#505378">' +
            '<animate attributeName="fill" values="#ffffff;#505378;#505378" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0s" calcMode="discrete"></animate>' +
            '</rect><rect x="40" y="19" width="20" height="20" fill="#505378">' +
            '<animate attributeName="fill" values="#ffffff;#505378;#505378" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.125s" calcMode="discrete"></animate>' +
            '</rect><rect x="61" y="19" width="20" height="20" fill="#505378">' +
            '<animate attributeName="fill" values="#ffffff;#505378;#505378" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.25s" calcMode="discrete"></animate>' +
            '</rect><rect x="19" y="40" width="20" height="20" fill="#505378">' +
            '<animate attributeName="fill" values="#ffffff;#505378;#505378" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.875s" calcMode="discrete"></animate>' +
            '</rect><rect x="61" y="40" width="20" height="20" fill="#505378">' +
            '<animate attributeName="fill" values="#ffffff;#505378;#505378" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.375s" calcMode="discrete"></animate>' +
            '</rect><rect x="19" y="61" width="20" height="20" fill="#505378">' +
            '<animate attributeName="fill" values="#ffffff;#505378;#505378" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.75s" calcMode="discrete"></animate>' +
            '</rect><rect x="40" y="61" width="20" height="20" fill="#505378">' +
            '<animate attributeName="fill" values="#ffffff;#505378;#505378" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.625s" calcMode="discrete"></animate>' +
            '</rect><rect x="61" y="61" width="20" height="20" fill="#505378">' +
            '<animate attributeName="fill" values="#ffffff;#505378;#505378" keyTimes="0;0.125;1" dur="1s" repeatCount="indefinite" begin="0.5s" calcMode="discrete"></animate>' +
            '</rect><g></g></g> </svg></div>' +
            '</div>');
    },
    stop: function () {
        setTimeout(function () {
            $(".loaderstartstop").remove();
        }, 1235);
    }
}



var dateselected_fromDates = null, hairstyle = null, ttimeesele = null, dateSelecta = null;
function convertTimeTo12HourFormat(time) {
    var hour = parseInt(time.substring(0, 2));
    var minute = time.substring(2);
    var period = (hour >= 12) ? "pm" : "am";

    // Convert hour to 12-hour format
    hour = (hour > 12) ? (hour - 12) : hour;

    // Handle hour = 0 (midnight) and hour = 12 (noon)
    if (hour === 0) {
        hour = 12;
    }

    // Pad minute with leading zero if necessary
    if (minute.length === 1) {
        minute = "0" + minute;
    }

    return hour + ":" + minute + " " + period;
}

function getTimeSlot(timwssawaewses) {
    $.post(apiUrl, {
        getDate: timwssawaewses,
        action: "select_time_forDate"
    }, function (dataTimes) {
        dateSelecta = timwssawaewses;
        var timeAvailableList = "";
        if (Object.keys(dataTimes).length === 0) {
            timeAvailableList = '<li>No timeslot available</li>';
        } else {
            for (var o in dataTimes) {
                if (dataTimes.hasOwnProperty(o)) {
                    timeAvailableList += "<li><label><input type='radio' value='" + dataTimes[o] + "' name='selector_timer' class='selector_timer_checked'/><div>" + convertTimeTo12HourFormat(dataTimes[o]) + "</div></label></li>";
                }
            }
        }
        var $targetClass = $(".time_sel_xewqctorbox");
        $targetClass.css("display", "block");
        $target = $targetClass.html(`<div class="close_btnn" onclick="close_btnnax();"><b>X</b></div><div>Select A Time for<br/>${timwssawaewses.substring(0, 4) + "-" + timwssawaewses.substring(4, 6) + "-" + timwssawaewses.substring(6, 8)}</div> 
                           <ul> 
                              ${timeAvailableList} 
                           </ul>
                           </br>
                           <div class="btn btn-primary mb-3" onclick="continuetoinfo();">Proceed to Checkout!</div>
                           `)
    });
}
function close_btnnax() {
    $(".pickerdateClassModal_parent").css("display", "none");
    $(".contact_cutstomer_vbg").css("display", "none");
};

var today = new Date();
var year = today.getFullYear();
var month = (today.getMonth() + 1), day = today.getDate();
var ttodayDatew = year + '' + ((month < 10) ? ('0' + month) : month) + '' + ((day < 10) ? ('0' + day) : day);

getTimeSlot(ttodayDatew);


$('.agddhahah').pignoseCalendar({
    disabledRanges: [['2020-01-12', ttodayDatew.substring(0, 4) + "-" + ttodayDatew.substring(4, 6) + "-" + (parseInt(ttodayDatew) - 1).toString().substring(6, 8)]],
    //disabledDates: [ '2024-05-07', '2024-05-06' ],   
    //disabledWeekdays: [0, 6],
    scheduleOptions: {
        colors: {
            offer: '#2fabb7',
            ad: '#5c6270'
        }
    },
    schedules: [{
        name: 'offer',
        date: '2024-05-09'
    }, {
        name: 'ad',
        date: '2024-05-10'
    }],
    select: function (date, context) {
        dateselected_fromDates = (date[0] === null ? 'null' : date[0].format('YYYYMMDD'));
        if (dateselected_fromDates !== "null") {
            getTimeSlot(dateselected_fromDates);
        } else {
            var $targetClass = $(".time_sel_xewqctorbox"); $targetClass.css("display", "block");
            $target = $targetClass.html(` <ul> <li>No date selected!</li> </ul>`)
        }
    }
});



function formatPhoneNumber(input) {
    let formattedValue = input.value.replace(/\D/g, '');
    input.value = formattedValue;
}

// pick a date after selecting from  hairstyle
document.querySelector("#btn_continue_to_pickadate").onclick = function () {
    $(".agddhahah").append('<div class="close_btnn" onclick="close_btnnax();"><b>X</b></div>');
    loader.start();

    setTimeout(function () {
        var nothingSelected = true,
            rados = document.querySelectorAll('[data-hair]');
        for (var i = 0; i < rados.length; i++) {
            if (rados[i].type === 'radio' && rados[i].checked) {

                $(".pickerdateClassModal_parent").css("display", "flex");
                hairstyle = rados[i].getAttribute("data-hair");

                loader.stop();
                nothingSelected = false;
                break;
            }
        }
        if (nothingSelected) { alert("Please Select a HairStyle From the Collection."); loader.stop(); };
    }, 1000);

}


function continuetoinfo() {
    loader.start();
    setTimeout(function () {
        var j = 0, radosq = document.querySelectorAll('input[name="selector_timer"].selector_timer_checked');
        for (var i = 0; i < radosq.length; i++) {
            if (radosq[i].type === 'radio' && radosq[i].checked) {
                ttimeesele = radosq[i].value;
                $("body").append(`<div class="contact_cutstomer_vbg">
                    <div style="position:relative;"><div class="close_btnn" onclick="close_btnnax();"><b>X</b></div></br><h2>Your contact Info</h2></br>
                        <label><p>FullName</p><input type="text" name="fullname" placeholder="Fullname"/></label>
                        <label><p>Email</p><input type="email" name="email" placeholder="Email"/></label>
                        <label><p>Phone Number</p><input type="text" name="phonenumber" placeholder="Phone Number" maxlength="15" oninput="formatPhoneNumber(this);"/></label>
                        <label><input class="btn btn-primary mb-3" type="button" name="submit" value="Proceed to payment" onclick="saveContactInfo();"/></label>
                        </br></br></div>
                </div>`);
                j = 1; break; return;
            }


        }
        if (j == 0) { alert("Please Select a time slot for this date."); }
    }, 1000);
    loader.stop();
}

function saveContactInfo() {
    loader.start();
    setTimeout(function () {
        var fnm = $('[name="fullname"]'),
            eml = $('[name="email"]'),
            pnb = $('[name="phonenumber"]'),
            err = false;

        if (fnm.val().trim().indexOf(' ') < 0) { err = "Please type your First and Last name!"; }
        if (eml.val().trim().indexOf('@') < 0) { err = "Please Input a valid Email!"; }
        if (pnb.val().trim().length > 14 || pnb.val().trim().length < 4) { err = "Please Input a valid Phone number!"; }

        if (1 == 2) {
            alert(err); loader.stop();
        } else {
            if (dateSelecta !== null && ttimeesele !== null && hairstyle !== null) {
                var contactInfo = {
                    fullname: fnm.val(),
                    email: eml.val(),
                    phone: pnb.val()
                };
                var hairstyleInfo = {
                    treeId: hairstyle,
                    time: ttimeesele,
                    date: dateSelecta
                }

                $.post(apiUrl, {
                    action: "save_contacts_64e",
                    contact_info: JSON.stringify(contactInfo),
                    order_detail: JSON.stringify(hairstyleInfo)
                }, function (dare) {
                    if (dare.code == 301) {
                        location.href = dare.link;
                    }
                }).always(function () {
                    loader.stop();
                });
            } else {
                alert("Error please reload this page."); loader.stop();
            }
        }
    }, 1000);

}