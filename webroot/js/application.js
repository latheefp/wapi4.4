

$(document).ready(function () {
    $(".ac_autocomplete").keyup(function (event) {
        //  alert("Key up");
        id = event.target.id;
        source = document.getElementById(id).getAttribute("source");
        $(this).autocomplete({
            source: function (request, response) {
                var request = document.getElementById(id).value;
                $.ajax({
                    url: '/ajaxes/getlist/' + source + '/' + encodeURIComponent(request),
                    dataType: 'json',
                    success: function (json) {
                        json.unshift({
                            id: 0,
                            name: ' --- None --- '
                        });
                        response($.map(json, function (item) {
                            return {
                                label: item['name'],
                                value: item['id']
                            }
                        }));


                    }
                });
            },
//                    autoFocus: true,            
//                    minLength: 0,
            select: function (event, ui) {
                $('#' + id).val(ui.item.label);
                $('#' + id + '_id').val(ui.item.value);
                return false;
            },
            change: function (event, ui) {
                if (!ui.item) {
                    $(this).val('');
                } else {
                    $('#promoteActionError').hide();
                    $(this).val(ui.item.label);
                    $(this).attr('actionId', ui.item.values);
                }
            },
            focus: function (event, ui) {
                this.value = ui.item.label;
                event.preventDefault();
            }
        });
    });
});



//js for audio play in streams.

var audio = document.getElementById("audioPlayer");
var playButton = document.getElementById("playButton");
var audioMessage = document.querySelector(".audio-message");

playButton.addEventListener("click", togglePlayback);

audio.onplay = function () {
    audioMessage.classList.add("playing");
};

audio.onpause = function () {
    audioMessage.classList.remove("playing");
};

function togglePlayback() {
    if (audio.paused) {
        audio.play();
    } else {
        audio.pause();
    }
}