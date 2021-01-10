

var emptyPlaylist = false;

function isEmpty(value){
	return (value.length < 1);
}

function validEmail(v) {
    var r = new RegExp("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?");
    return (v.match(r) == null) ? false : true;
}


function isExists(elem){
	if ($(elem).length > 0) {
		return true;
	}
	return false;
}


function ucFirst(str) {
    str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
        return letter.toUpperCase();
    });
    str = str.replace('_', ' ');
    return str;
}

function getTime(t) {
    var m=~~(t/60), s=~~(t % 60);
    return (m<10?"0"+m:m)+':'+(s<10?"0"+s:s);
}



function renderAjaxImage(elem, data){
    var imageElement = $(elem).closest('form').find('.uploaded-image');

    $(imageElement).attr('src', data.image_link);
}


function uploadImageAjax($this, file){
    var action = $this.data('action'),
        id = $this.closest('form').find('[name="id"]').val(),
        form_data = new FormData();

    if(imageValidation(file)){
        form_data.append('image_name', file);
        form_data.append('id', id);
        form_data.append('action', action);

        $.ajax({
            url: currentAPI,
            method: 'POST',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,

            beforeSend: function(){
                $('.ajax-bar').addClass('active').css('width', '0px');
            },

            error: function(err) {
                if(err.status == 404) renderTableMessage($this, invalidApiMessage, true);
                else renderTableMessage($this.closest('form'), somethingWentWrongMessage, true);
            },

            success: function(response) {


                var uploadedObj = JSON.parse(JSON.stringify(response));
                if (uploadedObj.status_code == 200) {

                    renderTableMessage($this.closest('form'), uploadedObj.message, false);

                    renderAjaxImage($this, uploadedObj.data);

                } else renderTableMessage($this.closest('form'), uploadedObj.message, true);
            },
            xhr: function(){
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {

                            percent = Math.ceil(position / total * 100);
                            $('.ajax-bar').css('width', percent +  '%');
                        }
                    }, true);
                }
                return xhr;
            },
            mimeType:"multipart/form-data"
        });
    }
}


function imageValidation(file){
    var validFormat = ['image/png', 'image/jpg', 'image/jpeg'],
        fileType = file.type,
        fileSize = file.size/(1024 * 1024),
        fileName = file.name;

    if($.inArray(fileType, validFormat) < 0) alert(fileName + ' ' + imageInvalidMessage);
    else if(maxUploadedFile < fileSize) alert(imageMaxSizeMessage);
    else return true;
}



function renderAudioPlayer(audio){
    var duration = 0,
        btnWrapper = $('<div>', { class: 'player-btn-wrapper' }),
        shuffled = false,
        unShuffledTracks = [],
        repeatTrack = false;

    var prevBtn = $('<button>', { class: 'prev-btn' });
    $('<i>', { class: 'ion-ios-skipbackward' }).appendTo(prevBtn);
    $(prevBtn).appendTo(btnWrapper);


    var playPauseBtn = $('<button>', { class: 'paused play-pause-btn' }).appendTo(btnWrapper);
    var loaderWrapper = $('<span>', { class: 'btn-loader full-h' })
    $('<span>', { class: 'ajax-loader active' }).appendTo(loaderWrapper);
    $(loaderWrapper).appendTo(playPauseBtn);


    var nextBtn = $('<button>', { class: 'next-btn' });
    $('<i>', { class: 'ion-ios-skipforward' }).appendTo(nextBtn);
    $(nextBtn).appendTo(btnWrapper);


    $(prevBtn).on('click', function(e){
        e.stopPropagation();
        e.preventDefault();

        trackDetails.currentPlayingTime = 0;
        trackDetails.isPlaying = true;

        var index = containsObject(currentPlaying, trackList);

        $('.audio-playing').removeClass('audio-playing').addClass('audio-paused');

        $('[data-song]').find('.play-pause-btn').removeClass('playing').addClass('paused');

        if(index <= 0) var prevItem = trackList[trackList.length - 1];
        else var prevItem = trackList[index - 1];

        currentPlaying = prevItem;
        
        var currentId = currentPlaying.id;

        $('[data-song="song' + currentId + '"]').find('.play-pause-btn').removeClass('paused').addClass('playing');

        $('[data-song="song' + currentId + '"]').closest('.user-player-sm').addClass('audio-playing').removeClass('audio-paused');

        songPlaying(true, currentId, loggedInUserID);

        setTrack(currentPlaying, trackDetails.isPlaying, trackDetails.currentPlayingTime);
    });


    var repeatCurrentSong = function(){
        trackDetails.currentPlayingTime = 0;
        trackDetails.isPlaying = true;

        setTrack(currentPlaying, trackDetails.isPlaying, trackDetails.currentPlayingTime);
    };

    /*var settingNextSong = function(){
        trackDetails.currentPlayingTime = 0;
        trackDetails.isPlaying = true;

        $('.audio-playing').removeClass('audio-playing').addClass('audio-paused');

        var index = containsObject(currentPlaying, trackList);

        $('[data-song]').find('.play-pause-btn').removeClass('playing').addClass('paused');

        if(index > trackList.length - 2) nextItem = trackList[0];
        else nextItem = trackList[index + 1];

        currentPlaying = nextItem;


        var currentId = currentPlaying.id;
        $('[data-song="song' + currentId + '"]').find('.play-pause-btn').removeClass('paused').addClass('playing');

        $('[data-song="song' + currentId + '"]').closest('.user-player-sm').addClass('audio-playing').removeClass('audio-paused');

        setTrack(currentPlaying, trackDetails.isPlaying, trackDetails.currentPlayingTime);
    };*/


    var settingNextSong = function(){
        trackDetails.currentPlayingTime = 0;
        trackDetails.isPlaying = true;

        $('.audio-playing').removeClass('audio-playing').addClass('audio-paused');


        var index = containsObject(currentPlaying, trackList);


        $('[data-song]').find('.play-pause-btn').removeClass('playing').addClass('paused');

        if(index > trackList.length - 2) nextItem = trackList[0];
        else nextItem = trackList[index + 1];

        currentPlaying = nextItem;


        var currentId = currentPlaying.id;
        $('[data-song="song' + currentId + '"]').find('.play-pause-btn').removeClass('paused').addClass('playing');

        $('[data-song="song' + currentId + '"]').closest('.user-player-sm').addClass('audio-playing').removeClass('audio-paused');

        songPlaying(true, currentId, loggedInUserID);
        setTrack(currentPlaying, trackDetails.isPlaying, trackDetails.currentPlayingTime);
    };

    $(nextBtn).on('click', function(e){
        e.stopPropagation();
        e.preventDefault();

        settingNextSong();
    });


    $(btnWrapper).insertAfter(audio);

    var audioProgressSection = $('<div>', { class: 'audio-progress-section' }).insertAfter(audio);
    var audioProgressBarWrapper = $('<a>', { class: 'audio-progress-bar-wrapper' }).appendTo(audioProgressSection);
    var audioProgressBar = $('<div>', { class: 'audio-progress-bar' }).appendTo(audioProgressBarWrapper);

    var audioCurrentTime = $('<h6>', { class: 'audio-current-time', text: '00:00' }).appendTo(audioProgressSection);
    var audioDuration = $('<h6>', { class: 'audio-duration', text: '00:00' }).appendTo(audioProgressSection);

    var soundSection = $('<div>', { class: 'audio-sound-section' }).insertAfter(audio);

    var shuffleBtn = $('<a>', { class: 'audio-shuffle-btn' }).appendTo(soundSection);
    $('<i>', { class: 'ion-shuffle'}).appendTo(shuffleBtn);
    var shuffleSettingText = $('<span>', { text: 'Shuffle is off', class: 'audio-btn-setting'}).appendTo(shuffleBtn);


    var repeatBtn = $('<a>', { class: 'audio-repeat-btn' }).appendTo(soundSection);
    $('<i>', { class: 'ion-ios-infinite'}).appendTo(repeatBtn);
    var repeatSettingText = $('<span>', { text: 'Repeat is off', class: 'audio-btn-setting' }).appendTo(repeatBtn);


    var shuffle = function(arr) {
        var currentIndex = arr.length, temporaryValue, randomIndex;
        while (0 !== currentIndex) {
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex -= 1;
            temporaryValue = arr[currentIndex];
            arr[currentIndex] = arr[randomIndex];
            arr[randomIndex] = temporaryValue;
        }
        return arr;
    };

    $(shuffleBtn).on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        if(!shuffled) {
            shuffled = true;
            $(shuffleBtn).addClass('active');
            $(shuffleSettingText).text(shuffleOnMessage);

            unShuffledTracks = $.merge([], trackList);
            trackList = shuffle(trackList);

        } else {
            shuffled = false;
            $(shuffleBtn).removeClass('active');
            $(shuffleSettingText).text(shuffleOffMessage);

            trackList = $.merge([], unShuffledTracks);
        }
    });


    $(repeatBtn).on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        if(!repeatTrack) {
            repeatTrack = true;
            $(repeatBtn).addClass('active');
            $(repeatSettingText).text(repeatOnMessage);

        } else {
            repeatTrack = false;
            $(repeatBtn).removeClass('active');
            $(repeatSettingText).text(repeatOffMessage);
        }
    });


    var soundBtn = $('<a>', { class: 'audio-sound-btn' }).appendTo(soundSection);
    $('<i>', { class: 'ion-android-volume-up'}).appendTo(soundBtn);

    var soundProgressSection = $('<div>', { class: 'sound-progress-section' }).appendTo(soundBtn);
    var soundProgressBarWrapper = $('<a>', { class: 'sound-progress-bar-wrapper' }).appendTo(soundProgressSection);
    var soundProgressBar = $('<div>', { class: 'sound-progress-bar' }).appendTo(soundProgressBarWrapper);

    var currentVol = audio[0].volume;

    $(soundBtn).on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        if (audio[0].volume == 0) {
            audio[0].volume = currentVol;
        } else {
            currentVol = audio[0].volume;
            audio[0].volume = 0;
        }

        $(soundProgressBar).css('height', audio[0].volume * 100 + '%');
        $(this).find('i').toggleClass('ion-android-volume-off').toggleClass('ion-android-volume-up');

    });

    var defaultSound = .75;
    audio[0].volume = defaultSound;
    $(soundProgressBar).css('height', defaultSound * 100 + '%');

    var soundDragable = false;
    $(soundProgressBarWrapper).on('mousedown', function(e){

        soundDragable = true;

    }).on('mouseup', function(e){

        soundDragable = false;
    });


    $(soundProgressBarWrapper).on('click mousemove', function(e){
        e.preventDefault();
        e.stopPropagation();

        var updateMouse = false;
        if(e.type == 'click') {
            soundDragable = false;
            updateMouse = true;

        }else{
            if(soundDragable){
                updateMouse = true;
            }
        }


        if(updateMouse){
            var offset = $(this).offset(),
                barHeight = $(this).height(),
                clickedPercent = (1 - (( e.pageY - offset.top) / barHeight)).toFixed(4);

            if(clickedPercent > 1) clickedPercent = 1;
            else if(clickedPercent < 0) clickedPercent = 0;

            audio[0].volume = clickedPercent;
            $(soundProgressBar).css('height', clickedPercent * 100 + '%');

            if(audio[0].volume > 0) $(soundBtn).find('i').removeClass('ion-android-volume-off').addClass('ion-android-volume-up');
            else $(soundBtn).find('i').addClass('ion-android-volume-off').removeClass('ion-android-volume-up');
        }
    });


    $(playPauseBtn).on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        var trackId = $(audio).data('song').split('song')[1];

        var songElement = $('[data-song="' + $(audio).data('song') + '"]');

        if(audio[0].paused) {

            songPlaying(true, trackId, loggedInUserID);
            trackDetails.isPlaying = true;

            $(songElement).find('.play-pause-btn').addClass('playing').removeClass('paused');
            audio[0].play();
            $(playPauseBtn).addClass('playing').removeClass('paused');

            $(songElement).closest('.user-player-sm').addClass('audio-playing').removeClass('audio-paused');

        }else{

            songPlaying(false, trackId, loggedInUserID);
            trackDetails.isPlaying = false;

            $(songElement).find('.play-pause-btn').addClass('paused').removeClass('playing');
            audio[0].pause();
            $(playPauseBtn).addClass('paused').removeClass('playing');

            $(songElement).closest('.user-player-sm').removeClass('audio-playing').addClass('audio-paused');
        }
    });

    audio.on('loadedmetadata', function(e){

        audioLoaderDisable();

        duration = Math.round(this.duration);
        audioDuration.text(getTime(duration));
    });

    audio[0].load();


    var counter = 0;

    $(audio).on('timeupdate', function(){
        if(counter % 4 == 0) {
            var currentTime = Math.round(this.currentTime);

            var percentTime = ((currentTime / duration) * 100).toFixed(2);

            $(audioProgressBar).css('width', percentTime + '%');
            audioCurrentTime.text(getTime(currentTime));
            trackDetails.currentPlayingTime  = currentTime;
        }
        counter ++;
    });


    $(audio).on('ended', function() {
        if(!repeatTrack) settingNextSong();
        else repeatCurrentSong();
    });


    var audioDragable = false;
    $(audioProgressBarWrapper).on('mousedown', function(e){
        audioDragable = true;
    }).on('mouseup', function(e){
        audioDragable = false;
    });


    $(audioProgressBarWrapper).on('click mousemove', function(e){
        e.preventDefault();
        e.stopPropagation();

        var updateMouse = false;
        if(e.type == 'click') {
            audioDragable = false;
            updateMouse = true;

        }else{
            if(audioDragable){
                updateMouse = true;
            }
        }

        if(updateMouse){
            var offset = $(this).offset(),
                barWidth = $(this).width(),
                clickedPercent = (((e.pageX - offset.left) / barWidth) * 100).toFixed(2),
                currentTime = Math.round((duration * clickedPercent) / 100);

            $(audioProgressBar).css('width', clickedPercent + '%');
            audioCurrentTime.text(getTime(currentTime));

            audio[0].currentTime = currentTime;

            if(!audio[0].paused){
                audio[0].play();
                $(playPauseBtn).addClass('playing').removeClass('paused');
            }
        }
    });
}


function resetPlayer(audio){
    audio[0].currentTime = 0;
    audio[0].pause();

    var playerWrapper = (audio).closest('.player-wrapper');

    var playPauseBtn  = $(playerWrapper).find('.play-pause-btn');
    $(playPauseBtn).addClass('paused').removeClass('playing');

    var audioCurrentTime = $(playerWrapper).find('.audio-current-time');
    $(audioCurrentTime).text('00:00');
    
    var audioProgressBar = $(playerWrapper).find('.audio-progress-bar');
    $(audioProgressBar).css('width', 0 + '%');
}


function containsObject(obj, list) {
    var i;
    for (i = 0; i < list.length; i++) {

        if (list[i].id === obj.id) {

            return i;
        }
    }

    return -1;
}


function addToQueue(newTrackList){
    $.each(newTrackList, function(key, value){

        var existingIndex = containsObject(value, trackList);
        if(existingIndex > -1) {
            trackList.splice(existingIndex, 1);
        }
        trackList.push(value);
    });
}


function audioLoaderEnable(){
    $('.user-audio-player').find('.play-pause-btn').attr('disabled', true).addClass('about-to-play');
    $('.user-audio-player').find('.play-pause-btn').find('.btn-loader').addClass('active');
}

function audioLoaderDisable(){
    $('.user-audio-player').find('.play-pause-btn').attr('disabled', false).removeClass('about-to-play');
    $('.user-audio-player').find('.play-pause-btn').find('.btn-loader').removeClass('active');
}

function setTrack(track, isPlaying, currentlyPlayingTIme){

    audioLoaderEnable();

    var audioWrapper = $('#fixed-bottom-player'),
        audioDetail = audioWrapper.find('.player-detail'),
        audioElem = audioWrapper.find('audio'),
        playPauseBtn = audioWrapper.find('.play-pause-btn');

    if(track.thumb_link != undefined && $.trim(track.thumb_link) != '' && track.thumb_link != null){
        $(audioDetail).find('img').attr('src', track.thumb_link);
    }else $(audioDetail).find('img').attr('src', deafultPlaylistImage);

    var minTextLength = 30;
    if($(window).width() < 400) minTextLength = 25;
    if($(window).width() < 300) minTextLength = 15;
    if($(window).width() < 200) minTextLength = 10;

    var titleOfTheTrack = (track.title.length > minTextLength) ? track.title.substr(0, minTextLength) + '...' :  track.title;
    var artistOfTheTrack = (track.artists.length > minTextLength) ? track.artists.substr(0, minTextLength) + '...' :  track.artists;

    $(audioDetail).find('.title').text(titleOfTheTrack);
    $(audioDetail).find('.artist').text(artistOfTheTrack);

    $(audioElem).data('song', 'song' + track.id);


    //$(audioElem)[0].src = 'play-track.php?track=' + track.encrypted;
    $(audioElem)[0].src = track.audio_link;

    audioElem[0].currentTime = currentlyPlayingTIme;

    audioElem[0].pause();
    $(playPauseBtn).removeClass('playing').addClass('paused');
    
    if(isPlaying){
        audioElem[0].play();
        $(playPauseBtn).removeClass('paused').addClass('playing');
    }

    $(audioDetail).on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        $('#fixed-bottom-player').addClass('small-player');

    });
}


function bigLoaderDisable(container){
    $(container).find('.loader-big.btn-loader').removeClass('active');
    $(container).find('.loader-big .ajax-loader').removeClass('active');
}


function bigLoaderEnable(container){
    (container).find('.loader-big.btn-loader').addClass('active');
    $(container).find('.loader-big .ajax-loader').addClass('active');
}


var clickedIds = [];


function renderTableMessage(form, message, isError){
    if(isError) $(form).find('.ajax-message').addClass('active').addClass('error').removeClass('success').text(message);
    else $(form).find('.ajax-message').addClass('active').addClass('success').removeClass('error').text(message);
}


function renderFormMessage(form, message, isError){
    if(isError) $(form).find('.ajax-message').addClass('active').addClass('error').removeClass('success').text(message);
    else $(form).find('.ajax-message').addClass('active').addClass('success').removeClass('error').text(message);
}


function validateForm(form){
    $('.has-error').removeClass('has-error');
    $('.err-msg').remove();

    var validInput = true;

    $.each($(form).find('[data-ajax-field]'), function(){
        var $this = $(this),
            ajaxField = $this.data('ajax-field');

        if(ajaxField == 'email') {
            if(!validEmail($this.val())){

                validInput = false;
                $this.addClass('has-error');
                $this.after('<h6 class="err-msg">'+ invalidEmailMEessage + '</h6>');
            }
        }else if(ajaxField == 'dropdown') {
            if ($this.prop('selectedIndex') < 0) {
                validInput = false;
                $this.addClass('has-error');
                $this.after('<h6 class="err-msg">' + requiredMessage + '</h6>');
            }
        }else if(ajaxField == true){

            if(isEmpty($this.val())){

                validInput = false;
                $this.addClass('has-error');
                $this.after('<h6 class="err-msg">'+ requiredMessage + '</h6>');
            }

        }else if(ajaxField == 'numeric'){
            if(isEmpty($this.val())){
                validInput = false;
                $this.addClass('has-error');
                $this.after('<h6 class="err-msg">' + requiredMessage + '</h6>');

            }else if(!$.isNumeric($this.val()) || $this.val() == 0){
                validInput = false;
                $this.addClass('has-error');
                $this.after('<h6 class="err-msg">' + ucFirst($this.attr('name')) + ' ' + numericError + '</h6>');
            }
        }else if(ajaxField == 'wysiwyg'){

            if(isEmpty($this.val())){

                validInput = false;
                $this.closest('.trumbowyg-box').addClass('has-error');
                $this.closest('.trumbowyg-box').after('<h6 class="err-msg">' + requiredMessage + '</h6>');

            }
        }else if(ajaxField == 'readonly-input'){

            if(isEmpty($this.find('input').val()) || $this.find('input').val() == 0 || $this.find('input').val() == ','){
                validInput = false;
                $this.addClass('has-error');
                $this.after('<h6 class="err-msg">'+ requiredMessage + '</h6>');
            }
        }else if(ajaxField == 'radio'){

            var radioBtn = $this.find('input[type="radio"]');
           if(!$(radioBtn).is(':checked')){
               validInput = false;
               $this.addClass('has-error');
               $this.after('<h6 class="err-msg">'+ radionRequiredError + '</h6>');

           }
        }else if(ajaxField == 'password'){

            if(isEmpty($this.val())){
                validInput = false;
                $this.addClass('has-error');
                $this.after('<h6 class="err-msg">'+ requiredMessage + '</h6>');

            }else if($this.val().length < minPassLength){
                validInput = false;
                $this.addClass('has-error');
                $this.after('<h6 class="err-msg">'+ minPassMessage + '</h6>');

            }
        }
    });

    return validInput;
}


function renderForm(data) {
    var objKey = Object.keys(data)[0],
        mainObj = data[objKey];

    var currencyKeys = ['price', 'cleaning_fee', 'service_fee'];

    var searchDropdown = mainObj['search_dropdown'],
        text = mainObj['text'],
        image = mainObj['image'],
        audio = mainObj['audio'],
        multipleImages = mainObj['images'],
        radio = mainObj['radio'],
        dropdown = mainObj['dropdown'],
        wshywyg = mainObj['wshywyg'],
        switchBtn = mainObj['switch'],
        multipleTracks = mainObj['multiple_tracks'],
        imageDropdown = mainObj['image_dropdown'];


    if(multipleTracks != undefined && multipleTracks != null){
        renderMultipleTracks(mainObj, objKey, multipleTracks);
    }

    if(dropdown != undefined && dropdown != null){
        for (var key in dropdown) {
            if(key == 'currency') getCurrencySelectOption(key, dropdown[key]);
            else mappingDropDown(key, dropdown[key]);
        }
    }


    if(searchDropdown != undefined && searchDropdown != null){
        for (var key in searchDropdown) {

            var searchDropdownValues = searchDropdown[key];
            var tagsReadonlyWrapper = $('#' + key).find('.readonly-input');
            var tagsInput = $(tagsReadonlyWrapper).find('input');

            if($(searchDropdownValues).length > 0){
                $.each(searchDropdownValues, function(index, value){

                    $(tagsReadonlyWrapper).find('[data-id="' + index + '"]').addClass('selected');
                    addTag(tagsReadonlyWrapper, tagsInput, index, value);

                });
            }
        }
    }


    if(imageDropdown != undefined && imageDropdown != null){
        for (var key in imageDropdown) {

            var imageDropdownValues = imageDropdown[key],
                readonlyWrapper = $('#' + key).find('.readonly-input'),
                valueHiddenInput = $(readonlyWrapper).find('input');

            if($(imageDropdownValues).length > 0){

                $(readonlyWrapper).find('.no-selected').removeClass('active');
                $(valueHiddenInput).val(imageDropdownValues['id']);
                var dropdownValue = $(readonlyWrapper).find('.selected-item');

                $('<img>', { class: 'dropdown-value-image', src: uploadedLink + imageDropdownValues['image_name'] }).appendTo(dropdownValue);
                $('<p>', { class: 'dropdown-value-title main-tag', text: decodeEntities(imageDropdownValues['title'])  }).appendTo(dropdownValue);

                $(dropdownValue).appendTo(readonlyWrapper);
            }
        }
    }


    if(wshywyg != undefined && wshywyg != null){

        

        for (var key in wshywyg) {
            var currentElement = $('[name="' + key + '"]');
            $(currentElement).val(wshywyg[key]);

            $(currentElement).trumbowyg({
                btns: [
                    ['viewHTML'],
                    ['undo', 'redo'], // Only supported in Blink browsers
                    ['formatting'],
                    ['strong', 'em', 'del'],
                    ['superscript', 'subscript'],
                    ['link'],
                    ['insertImage'],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ['unorderedList', 'orderedList'],
                    ['horizontalRule'],
                    ['removeformat']
                ],
                autogrow: true
            });
        }
    }


    if(text != undefined && text != null){
        for (var key in text) {

            var currentValue = text[key];
            $('[name="' + key + '"]').val(currentValue);
        }
    }

    if(multipleImages != undefined && multipleImages != null){
        renderMultipleImages(multipleImages, objKey);
    }


    if(audio != undefined && audio != null && audio != ''){
        for (var key in audio) {
            var audioElem = $('.' + objKey + '.' + key);

            $(audioElem).find('source').attr('src', uploadedAudioLink + '/' + audio[key]);
            audioElem[0].load();
        }
    }


    if(image != undefined && image != null && image != ''){
        for (var key in image) {
            if(image[key] != undefined && image[key] != null && image[key] != '')
                $('.' + objKey + '.' + key).attr('src', uploadedLink + '/' + image[key]);
            else $('.' + objKey + '.' + key).attr('src', uploadedLink + '/' + defaultImage);
        }
    }



    if(switchBtn != undefined && switchBtn != null) {
        for (var key in switchBtn) {

            if(switchBtn[key] == 1) $('[name="' + key + '"]').attr('checked', true);
        }
    }

    if(radio != undefined && radio != null) {
        for (var key in radio) {

            $($('[name="' + key + '"]')).each(function(){
                if($(this).val() == radio[key]) $(this).attr('checked', true);
                else $(this).attr('checked', false);
            });
        }
    }
}


function ajaxFormRequest(form, method){
    var ajaxBar = $(form).find($('.ajax-bar'));

    $.ajax({
        url: currentAPI,
        type: method,
        data: $(form).serialize(),
        dataType : 'json',

        beforeSend: function(e){
            $(ajaxBar).css('width', 0 +  '%').addClass('active');
            bigLoaderEnable($(form).closest('.loader-wrapper'));
        },
        error: function(err) {

            bigLoaderDisable($(form).closest('.loader-wrapper'));
            if(err.status == 404) renderTableMessage(form, 'Invalid Api.', true);
            else renderTableMessage(form, somethingWentWrongMessage, true);
        },
        success: function(response) {


            jQuery('html,body').animate({scrollTop:0},0);

            $(form).find('.item-content').addClass('active');
            bigLoaderDisable($(form).closest('.loader-wrapper'))

            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {

                if(method == "POST") renderFormMessage(form, uploadedObj.message, false);

                if(uploadedObj.data.redirect != undefined) {
                    var currentUrl = window.location.href,
                        currentBaseUrl = currentUrl.split('#'),
                        currentTabID = (currentBaseUrl[1] != undefined) ? '#' + currentBaseUrl[1] : '';

                    if(uploadedObj.data.redirect != '') window.location.href = currentBaseUrl[0] + '?' + uploadedObj.data.redirect + currentTabID;
                    else window.location.href = currentBaseUrl[0];

                } else renderForm(uploadedObj.data);

            } else renderFormMessage(form, uploadedObj.message, true);

        },
        xhr: function(){
            var xhr = $.ajaxSettings.xhr();
            if (xhr.upload) {
                xhr.upload.addEventListener('progress', function(event) {
                    var percent = 0;
                    var position = event.loaded || event.position;
                    var total = event.total;
                    if (event.lengthComputable) {

                        percent = Math.ceil(position / total * 100);
                        $(ajaxBar).css('width', percent +  '%');
                    }
                }, true);
            }
            return xhr;
        },
        mimeType:"multipart/form-data"
    });
}


function getFormContent(form){
    ajaxFormRequest(form, 'GET');
}


function updateFormContent(form){
    ajaxFormRequest(form, 'POST');
}


function ajaxSidebarInit(currentHash, siteConfig){
    var selectedId = currentHash.trim();
    if(selectedId == '' || selectedId == null) selectedId = '#' + siteConfig;

    $('[href="' + selectedId +'"]').addClass('active');
    $(selectedId).addClass('active');

    var selectedForm = $(selectedId),
        formGetUrl = $(selectedForm).data('url');

    clickedIds.push(selectedId);

    getFormContent(formGetUrl, selectedForm);
}


function loadProfile(){
    $.ajax({
        url: currentAPI,
        type: 'POST',
        data: { action: currentAction, user_id: loggedInUserID },
        dataType : 'json',
        beforeSend: function(e){ },
        error: function(err) { },
        success: function(response) {


            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {


                var user = uploadedObj.data['user'],
                    tracks = uploadedObj.data['tracks'];

                if(user != null && user != '' && user != undefined){

                    $('[name="id"]').val(user.id);
                    $('[name="username"]').val(user.username);
                    $('[name="email"]').val(user.email);

                    $('.uploaded-image').attr('src', user.image_link);

                    $('input[value="' + user.gender + '"]').attr('checked', true);
                }

                settingTracks(tracks);
                
                loadGenreTag(uploadedObj);
                
            }else alert(uploadedObj.message);
        },
    });
}



function settingTracks(tracks){
    if(currentPlaying == null) currentPlaying = tracks[0];
    if(!trackDetails.isPlaying) {
        trackDetails.currentPlayingTime = 0;
        setTrack(currentPlaying, trackDetails.isPlaying, trackDetails.currentPlayingTime);
    }
    if(trackList.length < 1) addToQueue(tracks);
}


function updateTracks(tracks){
    currentPlaying = tracks[0];
    if(!trackDetails.isPlaying){
        trackDetails.currentPlayingTime = 0;
        setTrack(currentPlaying, trackDetails.isPlaying, trackDetails.currentPlayingTime);
    }
    addToQueue(tracks);
}


function popupToastEnabled(title, desc){
    $('.popup-toast').find('.title').text(title);
    $('.popup-toast').find('.desc').text(desc);

    $('.popup-toast').addClass('active');

    setTimeout(function(){

        $('.popup-toast').removeClass('active');

    }, 2000);
}

function addSinglePlaylist(songId, value, playlistBodyWrapper, playlistBodyInputWrapper, loader){

    var playlistSingle = $('<div>', { class: 'single-playlist' });
    var playlist = $('<a>', { href: '#', text: decodeEntities(value.title) }).appendTo(playlistSingle);

    var operationBtnWrapper = $('<div>', { class: 'playlist-btn-wrapper' });

    var playEdit = $('<a>', { href: '#', });
    $('<i>', { class: 'ion-compose' }).appendTo(playEdit);
    $(playEdit).appendTo(operationBtnWrapper);

    var playDelete = $('<a>', { href: '#', });
    $('<i>', { class: 'ion-android-delete' }).appendTo(playDelete);
    $(playDelete).appendTo(operationBtnWrapper);


    /*ADD TO PLAYLIST*/

    $(playlist).on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        $.ajax({
            url: currentAPI,
            type: 'POST',
            data: { playlist_id: value.id, track_id: songId, action: addToPlaylistAction, user_id: loggedInUserID },
            dataType : 'json',
            beforeSend: function(e){ $(loader).addClass('active'); },
            error: function(err) { },
            success: function(response) {

                $(loader).removeClass('active');
                var uploadedObj = JSON.parse(JSON.stringify(response));
                if (uploadedObj.status_code == 200) {

                    popupToastEnabled('Success', uploadedObj.message);
                    undoBodyFixed();
                    $('.playlist-popup').removeClass('active');

                }else alert(uploadedObj.message);
            }
        });
    });



    /*PLAYLIST EDIT*/

    $(playEdit).on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        $(playlistBodyWrapper).removeClass('active');
        $(playlistBodyInputWrapper).addClass('active');
        $(playlistBodyInputWrapper).closest('form').attr('data-playlist-index', playlistSingle.index());
        $(playlistBodyInputWrapper).find('input[name="title"]').val($(playlist).text());
        $(playlistBodyInputWrapper).find('input[name="id"]').val(value.id);
    });


    /*PLAYLIST DELETE*/

    $(playDelete).on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        $.ajax({
            url: currentAPI,
            type: 'POST',
            data: { playlist_id: value.id, action: deletePlaylistAction, user_id: loggedInUserID },
            dataType : 'json',
            beforeSend: function(e){ $(loader).addClass('active'); },
            error: function(err) { },
            success: function(response) {

                $(loader).removeClass('active');
                var uploadedObj = JSON.parse(JSON.stringify(response));
                if (uploadedObj.status_code == 200) {

                    $(playlistSingle).remove();

                    if(playlistBodyWrapper.html().trim() == ""){
                        emptyPlaylist = true;
                        $('<p>', { text: noPlaylistMessage }).appendTo(playlistBodyWrapper);
                    }

                }else alert(uploadedObj.message);
            }
        });
    });

    $(operationBtnWrapper).appendTo(playlistSingle);

    $(playlistSingle).appendTo(playlistBodyWrapper);
};


var songPlayed = [];

function songPlaying(playingStatus, trackId, userId){
    var playingInterval = setInterval(function(){

        if(playingStatus){


            if(currentPlaying.id == trackId) {
                if($.inArray(trackId, songPlayed) == -1){

                    increaseListeningCount(trackId, userId);
                    songPlayed.push(trackId);

                    clearInterval(playingInterval);

                }else clearInterval(playingInterval);
            }else clearInterval(playingInterval);
        }else clearInterval(playingInterval);
    }, 5000);
}


function increaseListeningCount(trackId, userId) {

    $.ajax({
        url: currentAPI,
        type: 'POST',
        data: { track_id: trackId, action: increaseListeningCountAction, user_id: userId },
        dataType : 'json',
        beforeSend: function(e){  },
        error: function(err) { },
        success: function(response) {
            
            

        }
    });

}

function playPauseBtnClickEvent(payPauseBtn, playPauseBtnLink, value){
    $(playPauseBtnLink).on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        $('.audio-playing').removeClass('audio-playing').addClass('audio-paused');

        if(payPauseBtn.hasClass('paused')){

            songPlaying(true, value.id, loggedInUserID);

            //NEW SONG

            if(currentPlaying.id != value.id) trackDetails.currentPlayingTime = 0;

            currentPlaying = value;
            trackDetails.isPlaying = true;

            $('.user-player-sm').find('.play-pause-btn').addClass('paused').removeClass('playing');
            $(payPauseBtn).addClass('playing').removeClass('paused');

            $(payPauseBtn).closest('.user-player-sm').addClass('audio-playing').removeClass('audio-paused');

        }else{

            songPlaying(false, value.id, loggedInUserID);
            trackDetails.isPlaying = false;
            $(payPauseBtn).addClass('paused').removeClass('playing');
        }
        setTrack(currentPlaying, trackDetails.isPlaying, trackDetails.currentPlayingTime);
    });
}


function visibleTrackDetails(head, body){
    var popup = $('#track-detail-popup');
    popup.addClass('active');

    bodyFixed();

    $(popup).find('.popup-header').html(head);

    if(body != null ) $(popup).find('.popup-body').html(decodeEntities(body));
    else $(popup).find('.popup-body').text('No ' + head + ' have been Added.');
}


var trackDetail = {};
var lyricsText = 'Lyrics';
var descriptionText = 'Description';

function getTrackDetail(trackId, type){

    $.ajax({
        url: currentAPI,
        type: 'POST',
        data: { track_id: trackId, action: trackDetailAction, user_id: loggedInUserID },
        dataType : 'json',
        beforeSend: function(e){

            bigLoaderEnable($('body'));

           if(trackDetail[trackId] != null){

               if(type == lyricsText) visibleTrackDetails(lyricsText, trackDetail[trackId].lyrics);
               else if(type == descriptionText) visibleTrackDetails(descriptionText, trackDetail[trackId].description);

               bigLoaderDisable($('body'));

               return false;
           }
        },
        error: function(err) {
            bigLoaderDisable($('body'));
        },
        success: function(response) {
            bigLoaderDisable($('body'));

            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {

                trackDetail[trackId] = uploadedObj.data;

                if(type == lyricsText) visibleTrackDetails(lyricsText, trackDetail[trackId].lyrics);
                else if(type == descriptionText) visibleTrackDetails(descriptionText, trackDetail[trackId].lyrics);

            }else alert(uploadedObj.message);
        }
    });
}



function renderCardTrack(sliderTracks, sliderTracksElem){
    var bootstrapRow = $('<div>', { class: 'row' });

    $.each(sliderTracks, function(key, value){

        var userPlayerSm = $('<div>', { class: 'user-player-sm audio-paused' });
        var userPlayerTop = $('<div>', { class: 'player-top' });

        var imageWrapperLink = $('<a>', { 'data-title': value.title,'data-page': 'tracks',
            href: 'track.php?id=' + value.id, class: 'not-load image-wrapper'  });
        if(value.image_link) var imageElem = $('<img>', { class: 'response-img', src: value.image_link}).appendTo(imageWrapperLink);
        else var imageElem = $('<img>', { class: 'response-img', src: defaultImage }).appendTo(imageWrapperLink);

        $(imageWrapperLink).appendTo(userPlayerTop);

        var playPauseBtnLink = $('<a>', { href: '#', class: 'play-pause-wrapper', 'data-song': 'song' + value.id });
        var payPauseBtn = $('<span>', { class: 'play-pause-btn paused' });
        $(payPauseBtn).appendTo(playPauseBtnLink);
        $(playPauseBtnLink).appendTo(userPlayerTop);
        
        playPauseBtnClickEvent(payPauseBtn, playPauseBtnLink, value);

        /*AUDIO FAVOURITE*/

        var audioFavourite = $('<a>', { class: 'audio-favourite', href: '#' }),
            favouritedLogo = 'ion-ios-heart',
            unFavouritedLogo = 'ion-ios-heart-outline';

        if(value.favourited == 1) $('<i>', { class: favouritedLogo,  }).appendTo(audioFavourite);
        else  $('<i>', { class: unFavouritedLogo, }).appendTo(audioFavourite);

        $(audioFavourite).appendTo(userPlayerTop);

        favouriteEvent(audioFavourite, favouritedLogo, unFavouritedLogo, value);


        /*AUDIO OPERATION*/

        var threeDotsWrapper = $('<div>', { class: 'audio-operation' });

        $('<i>',{ class: 'ion-android-more-vertical' }).appendTo(threeDotsWrapper);

        var moreList = $('<ul>');

        var addToPlaylist = $('<a>', { href : '#', text: 'Add to Playlist' }).appendTo($('<li>').appendTo(moreList));

        var viewLyrics = $('<a>', { href : '#', text: 'Lyrics' }).appendTo($('<li>').appendTo(moreList));
        var viewDescription = $('<a>', { href : '#', text: 'Description' }).appendTo($('<li>').appendTo(moreList));
        var shareSong = $('<a>', { href : '#', text: 'Share' }).appendTo($('<li>').appendTo(moreList));
        var downloadSong = $('<a>', { href : '#', text: 'Download' }).appendTo($('<li>').appendTo(moreList));
        
        $(moreList).appendTo(threeDotsWrapper);
        $(threeDotsWrapper).appendTo(userPlayerTop);

        $(userPlayerTop).appendTo(userPlayerSm);


        $(downloadSong).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            if(isLoggedIn) downloadTrack(value.id);
            else showLoginForm();
        });


        $(shareSong).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            shareTrack(value.id);
        });


        $(viewLyrics).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            getTrackDetail(value.id, lyricsText);
        });


        $(viewDescription).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            getTrackDetail(value.id, descriptionText);
        });

        var audioDetails =  $('<div>', { class: 'audio-details', });
        var audioDetailsArtist = $('<div>', { class: 'audio-details-artist' });
        
        /*AUDIO DURATION*/

        var audioDuration = $('<p>', { class: 'audio-duration' });
        $('<img>', { class: '', src: 'images/music-equalizer-gif.gif' }).appendTo(audioDuration);
        $('<span>', { class: 'active', text: decodeEntities(getTime(value.audio_duration)) }).appendTo(audioDuration);

        $(audioDuration).appendTo(audioDetails);


        var trackTitle = $('<a>', { 'data-page': 'tracks', href: 'track.php?id=' + value.id, class: 'not-load image-wrapper'  });
        $('<b>', { text: decodeEntities(value.title) }).appendTo($('<p>', { class: 'title' })).appendTo(trackTitle);

        $(trackTitle).appendTo(audioDetails);


        var subTitle = $('<div>', { class: 'sub-title' });

        $.each(value.artist_array, function(key, artistValue){

            $('<a>', { 'data-page': 'artist', href: 'artist.php?id=' + artistValue.id, class: 'not-load', text: decodeEntities(artistValue.name) }).appendTo(subTitle);
        });

        $(subTitle).appendTo(audioDetailsArtist);


        $(audioDetails).appendTo(userPlayerSm);


        $(audioDetailsArtist).appendTo(audioDetails);
        $(audioDetails).appendTo(userPlayerSm);


        userPlayerSm.addClass('track-card-item');
        var bootstrapCol = $('<div>', { class: 'col-lg-2 col-md-3 col-sm-4 col-6' });
        $(userPlayerSm).appendTo(bootstrapCol);
        $(bootstrapCol).appendTo(bootstrapRow);
        $(bootstrapRow).appendTo(sliderTracksElem);


        var colWidth = bootstrapCol.width();
        $(imageElem).attr('height', colWidth);
        $(imageElem).attr('width', colWidth);

        $(addToPlaylist).click(function(e){
            e.preventDefault();
            e.stopPropagation();

            if(isLoggedIn) addToPlaylistOperation(value.id);
            else showLoginForm();
        });

    });
}



function downloadTrack(track_id){
    var downadContainer = $('#track-download-popup');

    $(downadContainer).find('.popup-header').text('Download');

    $.ajax({
        url: currentAPI,
        type: 'POST',
        data: { id: track_id, action: downloadAction, user_id: loggedInUserID },
        dataType: 'json',
        beforeSend: function (e) {
            bigLoaderEnable($('body'));
        },
        error: function (err) {
            bigLoaderDisable($('body'));
        },
        success: function (response) {

            bodyFixed();
            bigLoaderDisable($('body'));
            $(downadContainer).addClass('active');

            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {

                var dplayTble = $('<div>', { class: 'dplay-tbl center-text' }),
                    dplayTbleCell = $('<div>', { class: 'dplay-tbl-cell' });

                $('<p>', { text: downloadActiveTimeMessage  }).appendTo(dplayTbleCell);
                var downloadBtn = $('<a>', { text: 'Download', 'target' : '_blank', href: 'download.php?d=' + uploadedObj.data, class : 'btn c-btn' }).appendTo(dplayTbleCell);


                $(downloadBtn).on('click', function (e) {
                    undoBodyFixed();
                    $(downadContainer).removeClass('active');
                });

                $(dplayTbleCell).appendTo(dplayTble);
                $(dplayTble).appendTo($(downadContainer).find('.popup-body'));

            }else {
                $('<p>', { text: decodeEntities(uploadedObj.message) }).appendTo(downadContainer);
            }
        }
    });
}



function renderListTrack(sliderTracks, sliderTracksElem, playlistId){

    playlistId = playlistId || null;

    $.each(sliderTracks, function(key, value){

        var userPlayerSm = $('<div>', { class: 'user-player-sm audio-paused' });
        var userPlayerLeft = $('<a>', { href: '#', class: 'audio-left', 'data-song': 'song' + value.id });

        var payPauseBtn = $('<span>', { class: 'play-pause-btn paused' });
        $(payPauseBtn).appendTo(userPlayerLeft);

        playPauseBtnClickEvent(payPauseBtn, userPlayerLeft, value);

        var audioDetails = $('<div>', { class: 'audio-details' });

        //if(value.title.length > 30) value.title = value.title.substr(0, 30) + '...';

        $('<b>', { text: decodeEntities(value.title) }).appendTo($('<p>', { class: 'title' })).appendTo(audioDetails);
        $('<p>', { text: decodeEntities(value.artists), class: 'artist' }).appendTo(audioDetails);


        $(audioDetails).appendTo(userPlayerLeft);
        $(userPlayerLeft).appendTo(userPlayerSm);


        var audioRightBtns = $('<div>', { class: 'audio-right-btns' });


        /*AUDIO FAVOURITE*/

        var audioFavourite = $('<a>', { class: 'audio-favourite', href: '#' }),
            favouritedLogo = 'ion-ios-heart',
            unFavouritedLogo = 'ion-ios-heart-outline';

        if(value.favourited == 1) $('<i>', { class: favouritedLogo,  }).appendTo(audioFavourite);
        else  $('<i>', { class: unFavouritedLogo, }).appendTo(audioFavourite);

        $(audioFavourite).appendTo(audioRightBtns);


        favouriteEvent(audioFavourite, favouritedLogo, unFavouritedLogo, value);


        /*AUDIO DURATION*/

        var audioDuration = $('<p>', { class: 'audio-duration' });
        $('<img>', { class: '', src: 'images/music-equalizer-gif.gif' }).appendTo(audioDuration);
        $('<span>', { class: 'active', text: decodeEntities(getTime(value.audio_duration)) }).appendTo(audioDuration);

        $(audioDuration).appendTo(audioRightBtns);


        /*AUDIO OPERATION*/

        var userPlayerRight = $('<div>', { class: 'audio-operation' });

        $('<i>',{ class: 'ion-android-more-vertical' }).appendTo(userPlayerRight);

        var moreList = $('<ul>');


        if(value.added_to_playlist == 1) var addToPlaylist = $('<a>', { href : '#', text: 'Remove From Playlist' }).appendTo($('<li>').appendTo(moreList));
        else var addToPlaylist = $('<a>', { href : '#', text: 'Add to Playlist' }).appendTo($('<li>').appendTo(moreList));

        var viewLyrics = $('<a>', { href : '#', text: 'Lyrics' }).appendTo($('<li>').appendTo(moreList));
        var viewDescription = $('<a>', { href : '#', text: 'Description' }).appendTo($('<li>').appendTo(moreList));
        var shareSong = $('<a>', { href : '#', text: 'Share' }).appendTo($('<li>').appendTo(moreList));
        var downloadSong = $('<a>', { href : '#', text: 'Download' }).appendTo($('<li>').appendTo(moreList));


        $(addToPlaylist).click(function(e){
            e.preventDefault();
            e.stopPropagation();

            if(isLoggedIn) addToPlaylistOperation(value.id);
            else showLoginForm();
        });


        $(shareSong).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            shareTrack(value.id);
        });
        
        $(viewLyrics).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            getTrackDetail(value.id, lyricsText);
        });


        $(viewDescription).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            getTrackDetail(value.id, descriptionText);
        });

        $(downloadSong).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            if(isLoggedIn) downloadTrack(value.id);
            else showLoginForm();
        });

        $(moreList).appendTo(userPlayerRight);
        $(userPlayerRight).appendTo(audioRightBtns);

        $(audioRightBtns).appendTo(userPlayerSm);

        $(userPlayerSm).appendTo(sliderTracksElem);

    });
}

function favouriteEvent(audioFavourite, favouritedLogo, unFavouritedLogo, value){
    $(audioFavourite).on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        if(isLoggedIn) {

            if(value.favourited == 1) var favoriteAction = removeFavouriteAction;
            else var favoriteAction = addToFavouriteAction;

            $.ajax({
                url: currentAPI,
                type: 'POST',
                data: { track_id: value.id, action: favoriteAction, user_id: loggedInUserID},
                dataType: 'json',
                beforeSend: function (e) {
                },
                error: function (err) {
                },
                success: function (response) {


                    var uploadedObj = JSON.parse(JSON.stringify(response));
                    if (uploadedObj.status_code == 200) {

                        popupToastEnabled('Success', uploadedObj.message);

                        if(value.favourited == 1) {
                            value.favourited = 2;
                            $(audioFavourite).find('i').addClass(unFavouritedLogo).removeClass(favouritedLogo);
                        }else {
                            value.favourited = 1;
                            $(audioFavourite).find('i').addClass(favouritedLogo).removeClass(unFavouritedLogo);
                        }
                    }
                }
            });
        }else showLoginForm();
    });
}



function addToPlaylistOperation(songId){

        $.ajax({
            url: currentAPI,
            type: 'POST',
            data: { page: null, action: playlistByUserAction, user_id: loggedInUserID },
            dataType : 'json',
            beforeSend: function(e){
                bigLoaderEnable($('body'));
            },
            error: function(err) {
                bigLoaderDisable($('body'));
                undoBodyFixed();
            },
            success: function(response) {

                bodyFixed();
                bigLoaderDisable($('body'));


                var uploadedObj = JSON.parse(JSON.stringify(response));
                if (uploadedObj.status_code == 200) {

                    var playlistContainer = $('#my-playlist');
                    playlistContainer.html('');
                    playlistContainer.addClass('active');

                    var playlistInnerContainer = $('<form>', { class: 'playlist-popup-inner' });

                    var playlistHeader = $('<h5>', { class: 'popup-header', text: 'Playlists' }),
                        playlistBody = $('<div>', { class: 'popup-body' }),
                        playlistFooter = $('<div>', { class: 'popup-footer' });


                    var playlistLoaderWrapper = $('<div>', { class: 'loader-big btn-loader' });
                    $('<div>', { class: 'ajax-loader active' }).appendTo(playlistLoaderWrapper);
                    $(playlistLoaderWrapper).appendTo(playlistBody);


                    var playlistBodyWrapper = $('<div>', { class: 'playlist-body-inner active' });


                    /*playlist input*/

                    var playlistBodyInputWrapper = $('<div>', { class: 'playlist-body-inner' }),
                        playlistInput = $('<input>', { type: 'text', placeholder: 'Title of the playlist', name: 'title' }).appendTo(playlistBodyInputWrapper);
                    playlistInput = $('<input>', { type: 'hidden', name: 'id' }).appendTo(playlistBodyInputWrapper);

                    $(playlistBodyInputWrapper).appendTo(playlistBodyInputWrapper);

                    $(playlistBodyInputWrapper).appendTo(playlistBody);


                    var playlistList = uploadedObj.data;
                    if(playlistList.length > 0){

                        $.each(playlistList, function(key, value){

                            addSinglePlaylist(songId, value, playlistBodyWrapper, playlistBodyInputWrapper, playlistLoaderWrapper);
                        });

                    }else {
                        emptyPlaylist = true;
                        $('<p>', { text: "You don't have any playlist" }).appendTo(playlistBodyWrapper);
                    }

                    //PLAYLIST INPUT

                    $(playlistBodyWrapper).appendTo(playlistBody);


                    //BTNS

                    var playlistCancel = $('<a>', { href: '#', text: 'Cancel'}).appendTo(playlistFooter);
                    var playlistNew = $('<a>', { href: '#', text: 'Create New'}).appendTo(playlistFooter);

                    $(playlistCancel).on('click', function(e){
                        e.preventDefault();
                        e.stopPropagation();

                        undoBodyFixed();
                        playlistContainer.removeClass('active');
                    });


                    /*CREATE NEW PLAYSLIST*/

                    $(playlistNew).on('click', function(e){
                        e.preventDefault();
                        e.stopPropagation();

                        $('.has-error').removeClass('has-error');
                        $('.err-msg').remove();

                        var $this = $(this),
                            playlistForm = $this.closest('form'),
                            playlistId = playlistForm.find('input[name="id"]'),
                            playlistIdVal = playlistId.val(),
                            playlistTitle = playlistForm.find('input[name="title"]'),
                            playlistTitleVal = playlistTitle.val();

                        if(playlistIdVal == undefined || playlistIdVal == '' || playlistIdVal == null) {
                            var formData = { title: playlistTitleVal, action: createPlaylistAction, user_id: loggedInUserID };
                        }else{
                            var formData = { title: playlistTitleVal, id: playlistIdVal,
                                action: createPlaylistAction, user_id: loggedInUserID };
                        }

                        if(playlistBodyInputWrapper.hasClass('active')){

                            if(!isEmpty(playlistTitleVal)){

                                $.ajax({
                                    url: currentAPI,
                                    type: 'POST',
                                    data: formData,
                                    dataType: 'json',
                                    beforeSend: function (e) {
                                        $(playlistLoaderWrapper).addClass('active');
                                    },
                                    error: function (err) {
                                    },
                                    success: function (response) {

                                        var uploadedObj = JSON.parse(JSON.stringify(response));
                                        if (uploadedObj.status_code == 200) {

                                            $(playlistLoaderWrapper).removeClass('active');

                                            $(playlistBodyWrapper).addClass('active');
                                            $(playlistBodyInputWrapper).removeClass('active');


                                            if(playlistIdVal == undefined || playlistIdVal == '' || playlistIdVal == null) {
                                                playlistTitle.val('');

                                                if(emptyPlaylist) playlistBodyWrapper.html('');
                                                emptyPlaylist = false;
                                                addSinglePlaylist(songId, response.data, playlistBodyWrapper, playlistBodyInputWrapper, playlistLoaderWrapper);
                                            }else{

                                                var editedELem = $('.single-playlist').eq($(playlistForm).attr('data-playlist-index'));
                                                $(editedELem).children('a').text(uploadedObj.data.title);

                                            }


                                        }else alert(uploadedObj.message);
                                    }
                                });

                            }else {
                                $(playlistInput).addClass('has-error');
                                $(playlistInput).after('<h6 class="err-msg">'+ cantBeEmpty + '</h6>');
                            }
                        }else{
                            $(playlistBodyWrapper).removeClass('active');
                            $(playlistBodyInputWrapper).addClass('active');
                        }
                    });

                    $(playlistHeader).appendTo(playlistInnerContainer);
                    $(playlistBody).appendTo(playlistInnerContainer);
                    $(playlistFooter).appendTo(playlistInnerContainer);
                    $(playlistInnerContainer).appendTo(playlistContainer);

                }else alert(uploadedObj.message);
            }
        });
}

function renderTagDetail(item, itemElem) {

    $('<h4>', { text: decodeEntities(item.title) }).appendTo(itemElem);

    if(item.count > 1) $('<h5>', { text: decodeEntities(item.count) + ' tracks'}).appendTo(itemElem);
    else $('<h5>', { text: decodeEntities(item.count) + ' track'}).appendTo(itemElem);


}

function renderTag(parentElement, tags){

    $.each(tags, function(key, value){

        var tag = $('<a>', { text: decodeEntities(value.title), 'data-page': 'tag', 'data-title': value.title , class: 'not-load album mb-30', href: 'tag.php?id=' + value.id});

        $(tag).appendTo(parentElement);
    });

}

function loadTags(page, artistId){

    $.ajax({
        url: currentAPI,
        type: 'POST',
        data: { page: page, action: currentAction, user_id: loggedInUserID, tag_id: tagId },
        dataType : 'json',
        async: false,
        beforeSend: function(e){ },
        error: function(err) { },
        success: function(response) {

            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {

                if(pagination == 1){
                    /*COMMON TRACKS*/
                    settingTracks(uploadedObj.data['tracks']);

                    loadGenreTag(uploadedObj);
                }

                if(artistId == null){
                    var tags = uploadedObj.data['tags'];
                    if(tags.length > 0) renderTag($('#tags'), tags);
                    else reachAtTheEnd = true;
                }else{

                    if(pagination == 1){
                        var tagInfo = uploadedObj.data['tag_detail'];
                        renderTagDetail(tagInfo, $('#tag-detail'));
                    }

                    var tagTracks = uploadedObj.data['tag_tracks'];
                    
                    if(tagTracks.length > 0){
                        renderListTrack(tagTracks, '#tag-tracks');
                        updateTracks(tagTracks);
                    } else reachAtTheEnd = true;
                }
            }else alert(uploadedObj.message);
        },
    });
}


function loadTagLinks(tags, parentElem){
    if(parentElem.html() == ''){
        $.each(tags, function(key, value){

            $('<a>', { 'data-page': 'tag', 'data-title': value.title , class: 'not-load', text: decodeEntities(value.title), href: 'tag.php?id=' + value.id }).appendTo(parentElem);

        });
    }
}


function renderTrackDetail(item, itemElem) {
    var leftArea = $('<div>', { class: 'left-area' });
    $('<img>', { src: item.image_link }).appendTo(leftArea);

    var rightArea = $('<div>', { class: 'right-area' });
    $('<h4>', { text: item.title }).appendTo(rightArea);


    $.each(item.artist_array, function (key, value) {

        $('<a>', { 'data-page': 'artist', 'data-title': value.name , class: 'not-load link',
            text: decodeEntities(value.name), href: 'artist.php?id=' + value.id }).appendTo(rightArea);
    });
    
    $(leftArea).appendTo(itemElem);
    $(rightArea).appendTo(itemElem);
}


function loadTracks(pagination, search, trackId){
    $.ajax({
        url: currentAPI,
        type: 'POST',
        data: { action: currentAction, page: pagination, track_id: trackId, user_id: loggedInUserID, searched: search },
        async: false,
        dataType : 'json',
        beforeSend: function(e){ },
        error: function(err) {  },
        success: function(response) {


            undoBodyFixed();

            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {

                if(pagination == 1){

                    /*COMMON TRACKS*/
                    settingTracks(uploadedObj.data['tracks']);
                    loadGenreTag(uploadedObj);
                }

                var trackList = uploadedObj.data['track_list'];

                if(trackList.length > 0){

                    if(trackList.length == 1){
                        var trackDetailElam = $('#track-detail');
                        $(trackDetailElam).closest('.inactive').removeClass('inactive');
                        renderTrackDetail(trackList[0], trackDetailElam);
                    }

                    renderListTrack(trackList, paginationElem);
                    updateTracks(trackList);

                }else {
                    if(pagination == 1) $('<h4>', { text: nothingFound, class: '' }).appendTo(paginationElem);
                    reachAtTheEnd = true;
                }

            }else alert(uploadedObj.message);
        },
    });
}


function renderGenreDetail(item, itemElem) {
    $('<h4>', { text: decodeEntities(item.title) }).appendTo(itemElem);

    if(item.count > 1) $('<h5>', { text: item.count + ' tracks'}).appendTo(itemElem);
    else $('<h5>', { text: item.count + ' track' }).appendTo(itemElem);
}


function renderGenre(parentElement, genres){
    $.each(genres, function(key, value){

        var genre = $('<a>', { 'data-page': 'genre', 'data-title': value.title ,
            text: decodeEntities(value.title), class: 'not-load album mb-30', href: 'genre.php?id=' + value.id});

        $(genre).appendTo(parentElement);
    });
}


function loadGenres(page, genreId){
    $.ajax({
        url: currentAPI,
        type: 'POST',
        data: { page: page, action: currentAction, user_id: loggedInUserID, genre_id: genreId },
        async: false,
        dataType : 'json',
        beforeSend: function(e){ },
        error: function(err) { },
        success: function(response) {


            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {

                if(pagination == 1){

                    /*COMMON TRACKS*/
                    settingTracks(uploadedObj.data['tracks']);

                    loadGenreTag(uploadedObj);
                }

                if(genreId == null){
                    var genres = uploadedObj.data['genres'];
                    if(genres.length > 0) renderGenre($('#genres'), genres);
                    else reachAtTheEnd = true;
                }else{

                    if(pagination == 1){
                        var genreInfo = uploadedObj.data['genre_detail'];
                        renderGenreDetail(genreInfo, $('#genre-detail'));
                    }

                    var genreTracks = uploadedObj.data['genre_tracks'];

                    if(genreTracks.length > 0){
                        renderListTrack(genreTracks, '#genre-tracks');
                        updateTracks(genreTracks);
                    }else reachAtTheEnd = true;
                }
            }else alert(uploadedObj.message);
        },
    });
}


function loadGenreLinks(tags, parentElem){
    if(parentElem.html() == ''){
        $.each(tags, function(key, value){

            $('<a>', { 'data-page': 'genre', 'data-title': value.title , class: 'not-load',text: decodeEntities(value.title), href: 'genre.php?id=' + value.id }).appendTo(parentElem);
        });
    }
}



function loadMyMusic(userId){
    $('.page-loader').addClass('active');

    //MY MUSIC
    $.ajax({
        url: currentAPI,
        type: 'POST',
        data: { action: currentAction, user_id: userId },
        dataType : 'json',
        beforeSend: function(e){  },
        error: function(err) { },
        success: function(response) {
            
            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {
                
                loadGenreTag(uploadedObj);

                //MY PLAYLIST

                var myPlaylist = uploadedObj.data['my_playlist'];

                if(myPlaylist.length > 0) {
                    var myPlaylistElem = $('#my-playlist');
                    $(myPlaylistElem).closest('.inactive').removeClass('inactive');
                    renderPlaylist(myPlaylistElem, myPlaylist);
                }


                //SAVED PLAYLIST

                var savedPlaylist = uploadedObj.data['saved_playlist'];

                if(savedPlaylist.length > 0){
                    var savedPlaylistElem = $('#saved-playlist');
                    $(savedPlaylistElem).closest('.inactive').removeClass('inactive');
                    renderPlaylist(savedPlaylistElem, savedPlaylist);
                }


                //FAVOURITE TRACKS

                var tracks =  uploadedObj.data['my_favourite'];

                if(tracks.length > 0){
                    var favouriteTracks = $('#favourite-tracks');
                    $(favouriteTracks).closest('.inactive').removeClass('inactive');
                    renderListTrack(tracks, favouriteTracks);
                    updateTracks(tracks);

                }

            }else alert(uploadedObj.message);

            $('.page-loader').removeClass('active');

        },
    });
}


function loadFeaturedAlbum(albums, parentElement){

    $.each(albums, function(key, value){
        var swiperAlbumWrapper = $('<div>', { class: 'swiper-slide' }),
            albumWrapper = $('<div>', { class: '' }),
            albumTitleLink = $('<a>', { 'data-page': 'album', 'data-title': value.title,
                class: 'not-load', href: 'album.php?id=' + value.id }),
            albumImageLink = $('<a>', { 'data-page': 'album', 'data-title': value.title,
                class: 'not-load', href: 'album.php?id=' + value.id });

        
        $('<img>', { src: value.image_link }).appendTo(albumImageLink);


        $(albumImageLink).appendTo(albumWrapper);

        var artistWrapper = $('<p>', {  class: 'sub-title mt-10 mb-5' });

        $.each(value.artist_array, function(key, artistDetail){
            $('<a>', { 'data-page': 'artist', 'data-title': artistDetail.name , class: 'not-load',
                href: 'artist.php?id=' + artistDetail.id, text: decodeEntities(artistDetail.name) }).appendTo(artistWrapper);
        });

        $(artistWrapper).appendTo(albumWrapper);

        $('<h4>', { text: decodeEntities(value.title) }).appendTo(albumTitleLink);
        $(albumTitleLink).appendTo(albumWrapper);

        $(albumWrapper).appendTo(swiperAlbumWrapper);

        $(swiperAlbumWrapper).appendTo(parentElement);

    });

}


function loadHomePage(pagination){

    if(pagination == 1) $('.page-loader').addClass('active');

    //HOME PAGE

    $.ajax({
        url: currentAPI,
        type: 'POST',
        data: { page: pagination, action: currentAction, user_id: loggedInUserID, featured_page: featuredPage },
        dataType : 'json',
        async: false,
        beforeSend: function(e){

        },
        error: function(err) { },
        success: function(response) {

            console.log(response);

            $('.page-loader').removeClass('active');

            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {

                featuredPage = uploadedObj.data['featured_page'];

                if(pagination == 1){

                    loadGenreTag(uploadedObj);

                    //FEATURED ALBUMS

                    var featuredAlbums = uploadedObj.data['featured_album'];

                    if(featuredAlbums.length > 0) {
                        var featuredAlbumElem = $('#featured-album').find('.swiper-wrapper');
                        $(featuredAlbumElem).closest('.inactive').removeClass('inactive');
                        loadFeaturedAlbum(featuredAlbums, featuredAlbumElem);
                    }

                    
                    //SLIDER PLAYLIST
                    var sliderTracks = uploadedObj.data['tracks'];

                    if(sliderTracks.length > 0) {
                        settingTracks(sliderTracks);

                        var sliderTracksElem = $('#slider-tracks')
                        $(sliderTracksElem).closest('.inactive').removeClass('inactive');
                        renderCardTrack(sliderTracks, sliderTracksElem, true);
                    }

                    //RECENT ALBUMS

                    var recentAlbums = uploadedObj.data['recent_album'];

                    if(recentAlbums.length > 0) {
                        var recentAlbumsElem = $('#recent-albums');
                        $(recentAlbumsElem).closest('.inactive').removeClass('inactive');
                        renderAlbum(recentAlbumsElem, recentAlbums);
                    }

                    //POPULAR ARTISTS

                    var popularArtists = uploadedObj.data['popular_artist'];

                    if(popularArtists.length > 0) {
                        var popularArtistsElem = $('#popular-artists');
                        $(popularArtistsElem).closest('.inactive').removeClass('inactive');
                        renderArtist(popularArtistsElem, popularArtists);
                    }
                }

                //POPULAR PLAYLIST
                var popularPlaylist = uploadedObj.data['popular_playlist'];
                var featuredPlaylist = uploadedObj.data['featured_playlist'];

                if(featuredPlaylist !=  undefined && featuredPlaylist.length > 0){

                    var featuredPlaylistElem = $('#featured-playlists');
                    $(featuredPlaylistElem).closest('.inactive').removeClass('inactive');
                    renderPlaylist(featuredPlaylistElem, featuredPlaylist);

                }else if(popularPlaylist!= undefined && popularPlaylist.length > 0){

                    var popularPlaylistElem = $('#popular-playlists');
                    $(popularPlaylistElem).closest('.inactive').removeClass('inactive');
                    renderPlaylist(popularPlaylistElem, popularPlaylist);

                }else reachAtTheEnd = true;

            }else alert(uploadedObj.message);
            
            enableSwiperSlider();

        },
    });
}

function decodeEntities(encodedString) {
    var translate_re = /&(nbsp|amp|quot|lt|gt);/g;
    var translate = {
        "nbsp":" ",
        "amp" : "&",
        "quot": "\"",
        "lt"  : "<",
        "gt"  : ">"
    };

    return encodedString.toString().replace(translate_re, function(match, entity) {
        return translate[entity];
    }).replace(/&#(\d+);/gi, function(match, numStr) {
        var num = parseInt(numStr, 10);
        return String.fromCharCode(num);
    });
}


function renderPlaylistDetail(item, itemElem) {
    var leftArea = $('<div>', { class: 'left-area' });
    $('<img>', { src: item.image_link }).appendTo(leftArea);

    var rightArea = $('<div>', { class: 'right-area' });
    $('<h4>', { text: decodeEntities(item.title) }).appendTo(rightArea);

    if(item.count > 1) $('<h5>', { text: decodeEntities(item.count) + ' tracks'}).appendTo(rightArea);
    else $('<h5>', { text: decodeEntities(item.count) + ' track'}).appendTo(rightArea);

    $(leftArea).appendTo(itemElem);
    $(rightArea).appendTo(itemElem);
}


function renderPlaylist(parentElement, playlists){
    $.each(playlists, function(key, value){

        var bootstrapCol = $('<div>', { class: 'col-lg-2 col-md-3 col-sm-4 col-6' }),
            wrapperCard = $('<div>', { class: 'wrapper-card' });

        var albumImgLink = $('<a>', { 'data-page': 'playlist',
            'data-title': value.title, class: 'not-load album', href: 'playlist.php?id=' + value.id });

        if(value.image_link  != null) var imageElem = $('<img>', { class: 'reponse-img', src: value.image_link  }).appendTo(albumImgLink);
        else var imageElem = $('<img>', { class: 'reponse-img', src: deafultPlaylistImage  }).appendTo(albumImgLink);

        $(albumImgLink).appendTo(wrapperCard);


        var albumTitle = $('<div>', { class: 'playlist-title-wrapper card-title' });

        var albumTitleLink = $('<a>', { 'data-page': 'playlist',
            'data-title': value.title, class: 'not-load album', href: 'playlist.php?id=' + value.id });

        $('<b>', {  text: decodeEntities(value.title) }).appendTo(albumTitleLink);

        $(albumTitleLink).appendTo(albumTitle);

        if(value.saved == 1) var savingIconClass = "ion-android-bookmark saved";
        else var savingIconClass = "ion-android-bookmark";


        var albumSavingLink = $('<a>', { class: 'save-btn', href: '#' });
        var saveIcon = $('<i>', {  class: savingIconClass }).appendTo(albumSavingLink);
        $(albumSavingLink).appendTo(albumTitle);


            $(albumSavingLink).on('click', function(e){
                e.preventDefault();
                e.stopPropagation();

                if(value.saved == 1) var requestData = { action: unSavePlaylistAction, user_id: loggedInUserID, playlist_id: value.id };
                else var requestData = { action: savePlaylistAction, user_id: loggedInUserID, playlist_id: value.id };

                if(loggedInUserID > 0){

                    $.ajax({
                        url: currentAPI,
                        type: 'POST',
                        data: requestData,
                        dataType: 'json',
                        beforeSend: function (e) {
                        },
                        error: function (err) {
                        },
                        success: function (response) {
                            
                            var uploadedObj = JSON.parse(JSON.stringify(response));
                            if (uploadedObj.status_code == 200) {

                                if(value.saved == 1){
                                    value.saved = 2;
                                    saveIcon.removeClass('saved');
                                } else {
                                    value.saved = 1;
                                    saveIcon.addClass().addClass('saved');
                                }
                            }
                        }
                    });

                }else showLoginForm();
            });

        $(albumTitle).appendTo(wrapperCard);

        $(wrapperCard).appendTo(bootstrapCol);
        $(bootstrapCol).appendTo(parentElement);

        setImageResolution(bootstrapCol.width(), imageElem, value.resolution);
    });
}


function setImageResolution(colWidth, elem, resolution){

    if(resolution == undefined) var colHeight = colWidth;
    else{
        var imgResolutionArr = resolution.split(':');
        var colHeight = imgResolutionArr[0] * colWidth / imgResolutionArr[1];
    }

    $(elem).css({
        'width' : colWidth + 'px',
        'height' :  colHeight + 'px'
    }).addClass('before-load-bg');

}

function loadPlaylists(page, playlistId){
    $.ajax({
        url: currentAPI,
        type: 'POST',
        data: { page: page, action: currentAction, user_id: loggedInUserID, playlist_id: playlistId },
        async: false,
        dataType : 'json',
        beforeSend: function(e){ },
        error: function(err) { },
        success: function(response) {


            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {

                if(pagination == 1){
                    /*COMMON TRACKS*/
                    settingTracks(uploadedObj.data['tracks']);

                    loadGenreTag(uploadedObj);
                }

                if(playlistId == null){
                    var playlists = uploadedObj.data['playlists'];

                    if(playlists.length > 0) renderPlaylist($('#playlists'), playlists);
                    else reachAtTheEnd = true;

                }else{

                    if(pagination == 1){
                        var playlistInfo = uploadedObj.data['playlist_detail'];
                        renderPlaylistDetail(playlistInfo, $('#playlist-detail'));
                    }

                    var playlistTracks = uploadedObj.data['playlist_tracks'];

                    if(playlistTracks.length > 0){
                        renderListTrack(playlistTracks, '#playlist-tracks', playlistId);
                        updateTracks(playlistTracks);
                    }else reachAtTheEnd = true;
                }
            }else alert(uploadedObj.message);
        },
    });
}



function renderAlbumDetail(item, itemElem) {
    var leftArea = $('<div>', { class: 'left-area' });
    $('<img>', { src: item.image_link }).appendTo(leftArea);

    var rightArea = $('<div>', { class: 'right-area' });
    $('<h4>', { text: decodeEntities(item.title) }).appendTo(rightArea);

    if(item.count > 1) $('<h5>', { text: decodeEntities(item.count) + ' tracks'}).appendTo(rightArea);
    else $('<h5>', { text: decodeEntities(item.count) + ' track'}).appendTo(rightArea);

    $(leftArea).appendTo(itemElem);
    $(rightArea).appendTo(itemElem);
}


function renderAlbum(parentElement, albums){
    $.each(albums, function(key, value){

        var bootstrapCol = $('<div>', { class: 'col-lg-2 col-md-3 col-sm-4 col-6' }),
            wrapperCard = $('<div>', { class: 'wrapper-card' });

        var album = $('<a>', { 'data-page': 'album', 'data-title': value.title , class: 'not-load album', href: 'album.php?id=' + value.id});

        var imageElem = $('<img>', { class: 'reponse-img', src: value.image_link  }).appendTo(album);

        var cardTitle = $('<div>', { class: 'card-title' });
        $('<b>', {  text: decodeEntities(value.title) }).appendTo($('<p>', { class: '' }).appendTo(cardTitle));

        var artistWrapper = $('<p>', {  class: 'sub-title' });

        $.each(value.artist_array, function(key, artistDetail){
            $('<a>', { 'data-page': 'artist', 'data-title': value.title , class: 'not-load',
                href: 'artist.php?id=' + artistDetail.id, text: decodeEntities(artistDetail.name) }).appendTo(artistWrapper);
        });

        $(artistWrapper).appendTo(cardTitle);
        $(cardTitle).appendTo(album);

        $(album).appendTo(wrapperCard);
        $(wrapperCard).appendTo(bootstrapCol);
        $(bootstrapCol).appendTo(parentElement);

        setImageResolution(bootstrapCol.width(), imageElem, value.resolution);

    });
}

function loadGenreTag(uploadedObj) {

    //GENRES

    var genre = uploadedObj.data['genres'];
    if(genre.length > 0) {
        var genreElem = $('#genres');
        $(genreElem).closest('.inactive').removeClass('inactive');
        loadGenreLinks(genre, genreElem);
    }

    //TAGS

    var tags = uploadedObj.data['tags'];
    if(tags.length > 0) {
        var tagElem = $('#tags');
        $(tagElem).closest('.inactive').removeClass('inactive');
        loadTagLinks(tags, tagElem);
    }

}
function loadAlbums(page, albumId){
    $.ajax({
        url: currentAPI,
        type: 'POST',
        data: { page: page, action: currentAction, user_id: loggedInUserID, album_id: albumId },
        dataType : 'json',
        async: false,
        beforeSend: function(e){ },
        error: function(err) { },
        success: function(response) {
            
            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {

                if(pagination == 1){
                    var tracks = uploadedObj.data['tracks'];
                    settingTracks(tracks);

                    loadGenreTag(uploadedObj);
                }


                if(albumId == null){

                    /*ALBUM LIST*/
                    var albums = uploadedObj.data['albums'];

                    if(albums.length > 0) renderAlbum($('#albums'), albums);
                    else reachAtTheEnd = true;

                }else{

                    /*ALBUM TRACK LIST*/
                    if(pagination == 1){
                        var albumInfo = uploadedObj.data['album_detail'];
                        renderAlbumDetail(albumInfo, $('#album-detail'));
                    }

                    var albumTracks = uploadedObj.data['album_tracks'];
                    if(albumTracks.length > 0){
                        renderListTrack(albumTracks, '#album-tracks');
                        updateTracks(albumTracks);
                    }else reachAtTheEnd = true;
                }
            }else alert(uploadedObj.message);
        },
    });
}


function renderArtistDetail(item, itemElem) {

    var leftArea = $('<div>', { class: 'left-area' });
    $('<img>', { src: item.image_link }).appendTo(leftArea);

    var rightArea = $('<div>', { class: 'right-area' });
    $('<h4>', { text: decodeEntities(item.name) }).appendTo(rightArea);

    if(item.count > 1) $('<h5>', { text: decodeEntities(item.count) + ' tracks'}).appendTo(rightArea);
    else $('<h5>', { text: decodeEntities(item.count) + ' track'}).appendTo(rightArea);

    $(leftArea).appendTo(itemElem);
    $(rightArea).appendTo(itemElem);
}


function renderArtist(parentElement, artists){
    $.each(artists, function(key, value){

        var bootstrapCol = $('<div>', { class: 'col-lg-2 col-md-3 col-sm-4 col-6' }),
            wrapperCard = $('<div>', { class: 'wrapper-card' });

        var album = $('<a>', { 'data-page': 'artist', 'data-title': value.name , class: 'not-load album', href: 'artist.php?id=' + value.id});
        var imageElem = $('<img>', { class: 'reponse-img', src: value.image_link  }).appendTo(album);
        $('<b>', {  text: decodeEntities(value.name) }).appendTo($('<p>', { class: 'card-title' }).appendTo(album));

        $(album).appendTo(wrapperCard);
        $(wrapperCard).appendTo(bootstrapCol);
        $(bootstrapCol).appendTo(parentElement);

        setImageResolution(bootstrapCol.width(), imageElem, value.resolution);
    });
}


function loadArtists(page, artistId){

    $.ajax({
        url: currentAPI,
        type: 'POST',
        data: { page: page, action: currentAction, user_id: loggedInUserID, artist_id: artistId },
        async: false,
        dataType : 'json',
        beforeSend: function(e){ },
        error: function(err) { },
        success: function(response) {

            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {


                if(pagination == 1){
                    /*SETTING TRACK*/
                    settingTracks(uploadedObj.data['tracks']);

                    loadGenreTag(uploadedObj);
                }

                if(artistId == null){

                    /*ALBUM LIST*/
                    var artists = uploadedObj.data['artists'];

                    if(artists.length > 0) renderArtist($('#popular-artists'), artists);
                    else reachAtTheEnd = true;

                }else{

                    /*ALBUM TRACK LIST*/

                    if(pagination == 1){
                        var artistInfo = uploadedObj.data['artist_detail'];
                        renderArtistDetail(artistInfo, $('#artist-detail'));
                    }

                    var artistTracks = uploadedObj.data['artist_tracks'];

                    if(artistTracks.length > 0){
                        renderListTrack(artistTracks, '#artist-tracks');
                        updateTracks(artistTracks);
                    }else reachAtTheEnd = true;

                }
            }else alert(uploadedObj.message);
        },
    });
}


function pageLoaderEnable(){
    $('.page-loader').addClass('active');
}

function pageLoaderDisable(){
    $('.page-loader').removeClass('active');
}


function load(url, page){
    pagination = 1;

    if(url == undefined) url = 'index.php';

    $.ajax({
        url: url,
        beforeSend: function(e){ pageLoaderEnable(); },
        success: function (response) {

            pageLoaderDisable();

            window.scrollTo(0, 0);

            var currentHtml = $($.parseHTML(response, true)).filter("main");
            $('main').replaceWith(currentHtml);

            if(page == undefined || page == 'home') {

                document.title = homePageTitle;

                loadHomePage(pagination);

                $(window).unbind('scroll');
                $(window).on('scroll', function(e){

                    if(!reachAtTheEnd){
                        if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                            pagination++;

                            loadHomePage(pagination);
                        }
                    }
                });

            }else if(page == 'my-music'){

                loadMyMusic(loggedInUserID);

            } else if(page == 'tracks'){

                $('#search-area').removeClass('active');

                loadTracks(pagination, search, trackId)

                $(window).unbind('scroll');

                if(trackId == null){
                    $(window).on('scroll', function(e){

                        if(!reachAtTheEnd){
                            if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                                pagination++;

                                loadTracks(pagination, search, trackId)
                            }
                        }
                    });
                }


            }else if(page == 'genre'){

                loadGenres(pagination, genreId);

                $(window).unbind('scroll');
                $(window).on('scroll', function(e){

                    if(!reachAtTheEnd){
                        if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                            pagination++;

                            loadGenres(pagination, genreId);
                        }
                    }
                });

            } else if(page == 'tag'){

                loadTags(pagination, tagId);
                $(window).unbind('scroll');
                $(window).on('scroll', function(e){

                    if(!reachAtTheEnd){
                        if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                            pagination++;

                            loadTags(pagination, tagId);
                        }
                    }
                });


            } else if(page == 'playlist'){

                loadPlaylists(pagination, playlistId);

                $(window).unbind('scroll');
                $(window).on('scroll', function(e){

                    if(!reachAtTheEnd){
                        if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                            pagination++;

                            loadPlaylists(pagination, playlistId);
                        }
                    }
                });

            }else if(page == 'album'){

                loadAlbums(pagination, albumId);

                $(window).unbind('scroll');
                $(window).on('scroll', function(e){
                    
                    if(!reachAtTheEnd){
                        if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                            pagination++;

                            loadAlbums(pagination, albumId);
                        }
                    }
                });

            }else if(page == 'artist'){

                loadArtists(pagination, artistId);

                $(window).unbind('scroll');
                $(window).on('scroll', function(e){

                    if(!reachAtTheEnd){
                        if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                            pagination++;

                            loadArtists(pagination, artistId);
                        }
                    }
                });

            } else if(page == 'profile'){
                loadProfile();
            }
        }
    });
}

function undoBodyFixed(){
    $('html').css({ height: '100%', overflow: 'auto' });
}


function bodyFixed(){
    $('html').css({ height: '100%', overflow: 'hidden' });
}


function showLoginForm(){
    $('.login-register').addClass('active');
    $('.tab').removeClass('active');
    $('#login-tab').addClass('active');
    bodyFixed();
}


function enableSwiperSlider() {

    swiperSlider = new Swiper('#featured-album', {
        slidesPerView: '3',
        spaceBetween: 0,
        centeredSlides: true,
        loop: true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        speed: 300,
        navigation: {
            nextEl: '#swiper-button-next',
            prevEl: '#swiper-button-prev',
        },

        breakpoints: {
            1200: {   slidesPerView: 3, },
            992: {   slidesPerView: 2, },
            576: { slidesPerView: 1, },
            100: { slidesPerView: 1, },
        }

    });


}

function shareTrack(trackId){
    var url = serverUrl + "/track.php";
    var totalurl=encodeURIComponent(url + '?id=' + trackId);

    window.open ('http://www.facebook.com/sharer.php?u=' + totalurl,'','width=500, height=500, scrollbars=yes, resizable=no');
}


(function ($) {
    "use strict";

    enableSwiperSlider();

    var History = window.History;

    History.Adapter.bind(window,'statechange',function(e) {
        var State = History.getState();

        load(State.data.url, State.data.page);
    });


   $(window).on('load', function (e) {
       pageLoaderDisable();
    });


    $(document).on('click', '.not-load', function(e){
        e.preventDefault();
        e.stopPropagation();


        $('.dropdown-list').removeClass('active');

        var $this = $(this),
            title = $this.data('title'),
            url = $this.attr('href'),
            page = $this.data('page');

        $('#main-menu').removeClass('active');

        if(page == 'my-music' && !isLoggedIn) showLoginForm();
        else History.pushState({ title: title, url: url, page: page }, title, url);
    });


    $(document).on('click', '#player-close-btn', function(e){
        e.preventDefault();
        e.stopPropagation();

        $('#fixed-bottom-player').removeClass('small-player');
    });

    
    $('#hamburger-menu').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        $('#main-menu').toggleClass('active');
    });


    $(document).on('click', function(e) {
        $('#main-menu').removeClass('active');
    });


    $('[data-close]').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            closeElement = $this.data('close');

        bigLoaderDisable($('body'));
        undoBodyFixed();
        $(closeElement).removeClass('active');
    });


    $('#search-area').find('form').on('submit', function(e){
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            searchValue = $this.find('input[name="search"]').val(),
            title = 'Tracks',
            page = 'tracks',
            url = 'track.php?search=' + searchValue;

        if(searchValue != '' && searchValue != null) History.pushState({ title: title, url: url, page: page }, title, url);
    });


    $(document).on('click', '[data-confirm]', function(e){
        if (!confirm($(this).data('confirm'))) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
    

    var haveSearchedItems = false;

    $('#search-btn').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            searchArea = $this.attr('href');

        $(searchArea).addClass('active');

        bodyFixed();

        $('#search-area').find('input').val('');


        if(!haveSearchedItems){
            $.ajax({
                url: currentAPI,
                type: 'POST',
                data: { action: searchDataAction },
                dataType: 'json',
                beforeSend: function (e) {  },
                error: function (err) {},
                success: function (response) {

                    var uploadedObj = JSON.parse(JSON.stringify(response));
                    if (uploadedObj.status_code == 200) {

                        haveSearchedItems = true;

                        var popular_searched_terms = uploadedObj.data['popular'],
                            recent_searched_terms = uploadedObj.data['recent'],
                            popularItemWrapper = $('#popular_search'),
                            recentItemWrapper = $('#recent_search');

                        $(popularItemWrapper).html('');

                        if(popular_searched_terms.length > 0){
                            $('<h5>', { class: 'title', text: 'Popular Search' }).appendTo(popularItemWrapper);
                            var itemWrapper = $('<div>');
                            $.each(popular_searched_terms, function(key, value){
                                $('<a>', { text: decodeEntities(value.term), 'data-page': 'tracks', 'data-title': value.term ,
                                    class: 'not-load', href: 'track.php?search=' + value.term  }).appendTo(itemWrapper);
                            });

                            $(itemWrapper).appendTo(popularItemWrapper);
                        }

                        $(recentItemWrapper).html('');

                        if(recent_searched_terms.length > 0){
                            $('<h5>', { class: 'title', text: 'Recent Search' }).appendTo(recentItemWrapper);
                            var itemWrapper = $('<div>');
                            $.each(recent_searched_terms, function(key, value){
                                $('<a>', { text: decodeEntities(value.term), 'data-page': 'tracks', 'data-title': value.term ,
                                    class: 'not-load', href: 'track.php?search=' + value.term  }).appendTo(itemWrapper);
                            });

                            $(itemWrapper).appendTo(recentItemWrapper);
                        }

                    }else alert(uploadedObj.message);
                }
            });
        }
    });


    $(document).on('mousedown', function(e) {
        var container = $('.search-wrapper');

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            if($('#search-area').hasClass('active')){
                $('#search-area').removeClass('active');
                undoBodyFixed();
            }
        }
    });


    $(document).on('change', '.ajax-img-upload', function(e){
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            _URL = window.URL || window.webkitURL,
            file = $this[0].files[0];

        if ($this[0].files && file) {

            uploadImageAjax($this, file);
        }

        $this.val('');
    });


    $(document).on('submit', '.ajax-form' ,function(e){
        e.stopPropagation();
        e.preventDefault();

        var $this = $(this);
        if(validateForm($this)) updateFormContent($this);
    });


    $(document).on('click', '.ajax-sidebar a', function(e){
        var $this = $(this);

        $("html, body").animate({ scrollTop: 0 }, "slow");

        $('.ajax-sidebar').find('a').removeClass('active');
        $('.ajax-form-wrapper').find('form').removeClass('active');

        $('.ajax-bar').removeClass('active');

        $this.addClass('active');
        $($this.attr('href')).addClass('active');

        $('.ajax-sidebar').removeClass('active');

        var currentFormID = $(this).attr('href'),
            currentFormDataUrl = $(currentFormID).data('url');

        if($.inArray(currentFormID, clickedIds) < 0) {
            clickedIds.push(currentFormID);

            getFormContent(currentFormDataUrl, currentFormID);
        }
    });


    $('#login-btn').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        showLoginForm();
    });


    $(document).on('mousedown', function(e) {
        var container = $('.popup-inner'),
            mainElement = $('.track-download-popup');

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            if(mainElement.hasClass('active')){
                mainElement.removeClass('active');
                undoBodyFixed();
            }
        }
    });


    $(document).on('mousedown', function(e) {
        var container = $('.popup-inner'),
            mainElement = $('.track-detail-popup');

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            if(mainElement.hasClass('active')){
                mainElement.removeClass('active');
                undoBodyFixed();
            }
        }
    });


    $(document).on('mousedown', function(e) {
        var container = $('.playlist-popup-inner'),
            mainElement = $('.playlist-popup');

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            if(mainElement.hasClass('active')){
                mainElement.removeClass('active');
                undoBodyFixed();
            }
        }
    });

    $('.dropdown-item').children('a').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            dropdown = $this.closest('.dropdown-item').find('.dropdown-list');

        $(dropdown).toggleClass('active');
    });


    $(document).on('mousedown', function(e) {
        var container = $('.dropdown-item'),
            mainElement = $('.dropdown-list');

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            if(mainElement.hasClass('active')){
                mainElement.removeClass('active');
            }
        }
    });


    $(document).on('mousedown', function(e) {
        var container = $('.inner'),
            mainElement = $('.login-register');

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            if(mainElement.hasClass('active')){
                mainElement.removeClass('active');
                undoBodyFixed();
            }
        }
    });


    $('[data-tab]').on('click', function(e){
        e.stopPropagation();
        e.preventDefault();

        var $this = $(this),
            target = $this.data('tab');

        $('.tab').removeClass('active');

        $(target).addClass('active');
    });


    $('#login-tab').find('form').on('submit', function(e){
        e.stopPropagation();
        e.preventDefault();

        var $this = $(this),
            url = $($this).data('url'),
            action = $($this).data('action'),
            parentElement = $('#login-tab');

        if(validateForm($this)){

            $.ajax({
                url: url,
                type: 'POST',
                data: $this.serialize() ,
                dataType : 'json',
                beforeSend: function(e){

                    bigLoaderEnable(parentElement);
                },
                error: function(err) {},
                success: function(response) {
                    
                    bigLoaderDisable(parentElement);

                    var uploadedObj = JSON.parse(JSON.stringify(response));
                    if (uploadedObj.status_code == 200) {

                        location.reload(true);

                    }else renderFormMessage(parentElement, uploadedObj.message, true);
                }
            });
        }
    });


    $('#register-tab').find('form').on('submit', function(e){
        e.stopPropagation();
        e.preventDefault();

        var $this = $(this),
            url = $($this).data('url'),
            action = $($this).data('action'),
            parentElement = $('#register-tab');

        if(validateForm($this)){

            $.ajax({
                url: url,
                type: 'POST',
                data: $this.serialize() ,
                dataType : 'json',
                beforeSend: function(e){

                    bigLoaderEnable(parentElement);
                },
                error: function(err) {},
                success: function(response) {
                    
                    bigLoaderDisable(parentElement);

                    var uploadedObj = JSON.parse(JSON.stringify(response));
                    if (uploadedObj.status_code == 200) {

                        $this.hide();
                        renderFormMessage(parentElement, registrationMessage, false);

                    }else renderFormMessage(parentElement, uploadedObj.message, true);

                }
            });
        }
    });


    $('#forgot-password-tab').find('form').on('submit', function(e){
        e.stopPropagation();
        e.preventDefault();

        var $this = $(this),
            url = $($this).data('url'),
            action = $($this).data('action'),
            parentElement = $('#forgot-password-tab');

        if(validateForm($this)){
            $.ajax({
                url: url,
                type: 'POST',
                data: $this.serialize() ,
                dataType : 'json',
                beforeSend: function(e){

                    bigLoaderEnable(parentElement);
                },
                error: function(err) {

                },
                success: function(response) {

                    bigLoaderDisable(parentElement);

                    var uploadedObj = JSON.parse(JSON.stringify(response));
                    if (uploadedObj.status_code == 200) {

                        $this.hide();
                        renderFormMessage(parentElement, registrationMessage, false);


                    }else renderFormMessage(parentElement, uploadedObj.message, true);
                }
            });
        }
    });


    /*AUDIO PLAYER*/

    $('audio').each(function(){
        renderAudioPlayer($(this));
    });

    if($.fn.magnificPopup){
        $('.magnific-grid').magnificPopup({
            delegate: '.magnific-item',
            type: 'image',
            closeOnContentClick: false,
            closeBtnInside: false,
            mainClass: 'mfp-with-zoom mfp-img-mobile mfp-fade',

            gallery: { enabled: true }
        });
    }


	$(window).bind("load", function() {
		if(isExists('.masonry-grid')){
			$('.masonry-grid').masonry({
				itemSelector: '.masonry-item',
                percentPosition: true,
			});
		}
	});

})(jQuery);
