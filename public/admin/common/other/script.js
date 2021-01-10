
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


function convertCurrency(price, fromCurrency, toCurrency){
    if (localStorage.getItem(getCurrencyAPI)) {

        var currencyRates = JSON.parse(localStorage.getItem(getCurrencyAPI)),
            convertedPrice = (currencyRates[toCurrency] * price) / currencyRates[fromCurrency];

        return (isNaN(convertedPrice)) ? 0 : convertedPrice.toFixed(2);
    }else{
        return false;
    }
}


function renderPagination(pagination, total, pageItem, currentPage){
    $(pagination).html('');

    if((total / pageItem) > 1){
        var totalPage = parseInt(total / pageItem),
            itemsLastPage = total % pageItem;
        if(itemsLastPage > 0) totalPage ++;

        for(var i = 1; i <= totalPage; i++){
            if(currentPage == i) var activeClass = 'active';
            else var activeClass = '';

            $('<a>',{ href : "#", text: i, 'data-page': i, class: activeClass }).appendTo(pagination);
        }

        var nextPage = parseInt(currentPage) + 1,
            prevPage = parseInt(currentPage) - 1;

        if(nextPage <= totalPage) {
            var nextBtn = $('<a>',{ href : "#", 'data-pagination': 'next', 'data-page' : nextPage });
            $('<i>', {class: 'ion-android-arrow-forward' }).appendTo(nextBtn);
            $(nextBtn).appendTo(pagination);

        } else if(prevPage > 0) {
            var prevBtn = $('<a>',{ href : "#", text: '', 'data-pagination': 'prev', 'data-page' : prevPage });
            $('<i>', {class: 'ion-android-arrow-back' }).appendTo(prevBtn);
            $(prevBtn).prependTo(pagination);
        }
    }
}


function renderShowingElement(itemCountElem, total, pageItem, currentPage, currentPageItemCount){
    var showingStart = (currentPage -1) * parseInt(pageItem) + 1,
        showingEnd = showingStart + currentPageItemCount - 1;

    if(total > 0) $(itemCountElem).text('Showing ' + showingStart + ' - ' + showingEnd + ' of ' + total);
    else $(itemCountElem).text('Noting Found.');
}


function deleteItem($this, url, id){
    var wrapperClass = $('.loader-wrapper');

    $.ajax({
        url: url,
        type: 'GET',
        data: { id: id },
        dataType : 'json',
        beforeSend: function(e){
            bigLoaderEnable(wrapperClass);
        },
        error: function(err) {
            bigLoaderDisable(wrapperClass);
            if(err.status == 404) renderTableMessage(wrapperClass, 'Invalid Api.', true);
            else renderTableMessage(wrapperClass, 'Something went wrong. Please try again.', true);
        },
        success: function(response) {

            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {

                $($this).closest('tr').remove();
                renderFormMessage(wrapperClass, uploadedObj.message, false);

                location.reload(true);

            }else renderFormMessage(wrapperClass, uploadedObj.message, true);

            bigLoaderDisable(wrapperClass);
        },
    });
}

function decodeEntities(encodedString) {
	if(encodedString != null){
		
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
}


function renderAll(response) {
    var heading = response.data.head,
        data = response.data.body,
        total = response.data.total,
        currentPage = response.data.page,
        pageItem = response.data.page_item,
        currentPageItemCount = response.data.current_item_count;

    var parentElement = $('.item-wrapper'),
        editLink = $(parentElement).find('table').data('edit-link'),
        tableBody = $(parentElement).find('tbody'),
        tableHead = $(parentElement).find('thead'),
        pagination = $(parentElement).find('.pagination'),
        itemCountElem = $(parentElement).find('.current-item');

    $(tableBody).html('');

    if(!isExists(tableHead.find('tr'))) {
        var tableHeadRow = $('<tr>');

        $.each(heading, function (key, value) {
            if(value) {
                var tableData = $('<td>');

                var sortingLink = $('<a>', { href: '#', text: decodeEntities(key), 'data-sort' : value[0], 'data-sort-type' : value[1] });
                var icon = $('<i>', { class: 'ion-arrow-up-c' }).appendTo(sortingLink);

                if(value[1] == 'DESC') icon.addClass('sort-desc');

                $(sortingLink).appendTo(tableData);
                $(tableData).appendTo(tableHeadRow);

            } else $('<td>', { text: decodeEntities(key) }).appendTo(tableHeadRow);
        });

        $(tableHeadRow).appendTo(tableHead);
    }

    renderShowingElement(itemCountElem, total, pageItem, currentPage, currentPageItemCount);
    renderPagination(pagination, total, pageItem, currentPage);

    $.each(data, function (key, value) {
        var tableRow = $('<tr>'),
            status = value['status'],
            featured = value['featured'],
            deleteId = value['delete'],
            lyricsId = value['lyrics'],
            notifyId = value['notify'],
            editId = value['edit'],
            viewId = value['view'],
            image = value['image'],
            values = value['values'];

        if(image != undefined && image != null) {
            if(image == '') image = defaultImage;

            if(image.startsWith("http")) var imgLink = image;
            else var imgLink = uploadedThumbLink + image;

            var tableDataImage = $('<td>');
            $('<img>', { class: 'table-img', src: imgLink }).appendTo(tableDataImage);
            $(tableDataImage).appendTo(tableRow);
        }


        $.each(values, function(key, value){
            if(!Array.isArray(value)){
                if(value['text'] != null && value['text'] != undefined){

                    if(value['link'] != null && value['link'] != undefined) {
                        var tableData = $('<td>');
                        $('<a>', { href : value['link'], class: 'link', text: decodeEntities(value['text']) }).appendTo(tableData);
                        $(tableData).appendTo(tableRow);

                    } else $('<td>', { text: decodeEntities(value['text']) }).appendTo(tableRow);

                }else $('<td>', { text: decodeEntities(value['text']) }).appendTo(tableRow);

            }else{

                var tableData = $('<td>');
                $.each(value, function(innerKey, innerValue){

                    var linkWrapper = $('<span>', { style: 'padding: 0 5px;' });
                    $('<a>', { href : innerValue['link'], class: 'link', text: decodeEntities(innerValue['text']) }).appendTo(linkWrapper);
                    $(linkWrapper).appendTo(tableData);
                });

                $(tableData).appendTo(tableRow);
            }
        });


        if(status != undefined){

            var statusTD = $('<td>');
            if(status)  $('<span>', { class: 'status ok' }).appendTo(statusTD);
            else $('<span>', { class: 'status not-ok' }).appendTo(statusTD);

            $(statusTD).appendTo(tableRow);
        }

        if(featured != undefined){

            var featuredTD = $('<td>');
            if(featured)  $('<span>', { class: 'status ok' }).appendTo(featuredTD);
            else $('<span>', { class: 'status not-ok' }).appendTo(featuredTD);

            $(featuredTD).appendTo(tableRow);
        }
        

        var hasDelete = (deleteId != undefined && deleteId != null),
            hasLyrics = (lyricsId != undefined && lyricsId != null),
            hasNotify = (notifyId != undefined && notifyId != null),
            hasEdit = (editId != undefined && editId != null),
            hasView = (viewId != undefined && viewId  != null);


        if(hasDelete || hasEdit || hasView){
            var tableDataOperation = $('<td>');

            if(lyricsId) $('<a>', { class: 'lyrics-link', text: 'Lyrics', 'data-id' : lyricsId }).appendTo(tableDataOperation);
            
            if(hasDelete) $('<a>', { class: 'delete-link', text: 'Delete', 'data-id' : deleteId }).appendTo(tableDataOperation);

            if(hasEdit) $('<a>', { class: 'edit-link', href: editLink + editId, text: 'Edit' }).appendTo(tableDataOperation);
            else if(hasView) $('<a>', { class: 'edit-link', href: editLink + viewId, text: 'View' }).appendTo(tableDataOperation);


            if(hasNotify) $('<a>', { class: 'notify-link', text: 'Notify', 'data-id' : notifyId }).appendTo(tableDataOperation);

            $(tableDataOperation).appendTo(tableRow);
        }

        $(tableRow).appendTo(tableBody);
    });
}


var gotAllWithPage = [];

function gotAllResponse(response){
    var uploadedObj = JSON.parse(JSON.stringify(response));
    if (uploadedObj.status_code == 200) {

        renderAll(uploadedObj);

    } else alert(uploadedObj.message);
}


function ajaxGetAll(url, page, searched, sort, sortType){
    searched = $.trim(searched);

    $.ajax({
        url: url,
        type: 'GET',
        data: { page: page, search : searched, sort: sort, sort_type: sortType },
        dataType : 'json',

        error: function(err) {

            bigLoaderDisable($('.item-wrapper'));

            if(err.status == 404) renderFormMessage('.item-wrapper', 'Invalid Api.', true);
            else renderFormMessage('.item-wrapper', 'Something went wrong. Please try again.', true);
        },
        beforeSend: function(){

            bigLoaderEnable($('.item-wrapper'));


            if(url in gotAllWithPage) {
                gotAllResponse(gotAllWithPage[this.url]);

                bigLoaderDisable($('.item-wrapper'));

                return false;
            }

        },
        success: function(response) {


            gotAllWithPage[this.url] = response;
            gotAllResponse(response);

            bigLoaderDisable('.item-wrapper');
        },
    });
}


function renderSingleTrack(tracksWrapper, trackObj){
    var deleteID = trackObj['delete'],
        removeFromPL = trackObj['remove_from_pl'],
        editID = trackObj['edit'],
        mainValue = trackObj['value'];

    var editLink = $(tracksWrapper).closest('[data-edit-link]').data('edit-link');

    var singleTrackWrapper = $('<div>', { class: 'single-track-container' });

    var playerWrapper = $('<div>', { class: 'admin-player player-wrapper' });
    var audioElem = $('<audio>');

    if(uploadedType == mainValue["audio_type"]){
        $('<source>', { type : 'audio/ogg', src: uploadedAudioLink + '/' + mainValue["track_name"] }).appendTo(audioElem);

    }else if(youtubeType == mainValue["audio_type"]){
        $('<source>', { type : 'audio/ogg', src: mainValue["audio_link"] }).appendTo(audioElem);
    }

    $(audioElem).appendTo(playerWrapper);
    $(playerWrapper).appendTo(singleTrackWrapper);

    renderAudioPlayer(audioElem);


    var titleWrapper = $('<div>', { class: 'track-title-wrapper' });
    $('<p>', { class: 'track-title', text: decodeEntities(mainValue['title']) }).appendTo(titleWrapper);


    var trackLinkWrapper = $('<div>', { class: 'track-link-wrapper' });


    if(removeFromPL !=  undefined && removeFromPL != null){
        var removeFromPLBtn = $('<a>', { href: '#', class: 'remove-from-pl-link link', text: 'Remove From Playlist', 'data-id': mainValue['id'] });
        $(removeFromPLBtn).appendTo(trackLinkWrapper);
    }


    if(deleteID !=  undefined && deleteID != null){
        var deleteBtn = $('<a>', { href: '#', class: 'delete-link', 'data-id': mainValue['id'] });
        $('<i>', { class: 'ion-android-delete' }).appendTo(deleteBtn);
        $(deleteBtn).appendTo(trackLinkWrapper);
    }


    if(editID !=  undefined && editID != null){
        var editBtn = $('<a>', { href: editLink + mainValue['id'], class: 'edit-link' });
        $('<i>', { class: 'ion-compose' } ).appendTo(editBtn);
        $(editBtn).appendTo(trackLinkWrapper);
    }


    $(trackLinkWrapper).appendTo(titleWrapper);
    $(titleWrapper).prependTo(singleTrackWrapper);

    return singleTrackWrapper;
}


function renderMultipleTracks(mainObj, objKey){
    var subObjKey = Object.keys(mainObj)[0],
        tracksWrapper = $('.' + subObjKey + '.' + objKey),
        multipleTracks = mainObj['multiple_tracks'];

    for (var key in multipleTracks) {

        var singleLem = renderSingleTrack(tracksWrapper, multipleTracks[key]);
        $(singleLem).prependTo(tracksWrapper);
    }
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
                $('<p>', { class: 'dropdown-value-title main-tag', text: decodeEntities(imageDropdownValues['title']) }).appendTo(dropdownValue);

                $(dropdownValue).appendTo(readonlyWrapper);
            }
        }
    }

    if(wshywyg != undefined && wshywyg != null){

        console.log(wshywyg[key]);

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
            if($.inArray(key, currencyKeys)  > -1){

                var currentKeyCurrency = key + '_currency';
                if(text[currentKeyCurrency] != currentCurrency) currentValue = convertCurrency(text[key], text[currentKeyCurrency], currentCurrency);

                if(!currentValue){

                    if(text[currentKeyCurrency] != undefined && text[currentKeyCurrency] != ''){
                        $('[name="' + key + '"]').closest('.price-input').find('.price-font').text(text[currentKeyCurrency]);
                        $('[name="' + key + '"]').closest('form').find('[name="saved_currency"]').val(text[currentKeyCurrency]);
                    }
                    currentValue = text[key];
                }
            }

            if(currentValue) $('[name="' + key + '"]').val(decodeEntities(currentValue));
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


function ajaxFormRequest(url, form, method){
    var ajaxBar = $(form).find($('.ajax-bar'));

    $.ajax({
        url: url,
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
            else renderTableMessage(form, 'Something went wrong. Please try again.', true);
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

                    window.location.href = currentBaseUrl[0] + '?' + uploadedObj.data.redirect + currentTabID;

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


function getFormContent(url, form){
   ajaxFormRequest(url, form, 'GET');
}


function updateFormContent(url, form){
    ajaxFormRequest(url, form, 'POST');
}



function mappingDropDown(selectName, values) {
    var selectElement = $('select[name="' + selectName + '"]');

    $.each(values['values'], function(key, value){
        var option = $('<option>', { value: key, text: decodeEntities(value) });
        if(values['selected'] == key) $(option).prop('selected', true);
        $(option).appendTo(selectElement)
    });
}


function renderAjaxImage(data){
    var objName = Object.keys(data)[0],
        mainObj = data[objName],
        image = mainObj['image'],
        imageKey = Object.keys(image)[0],
        imageName = image[imageKey],
        imageElement = $('.' + objName + '.' + imageKey),
        siteLogoElement = $(imageElement).data('logo-element');

    $(imageElement).attr('src', uploadedLink + '/' + imageName);
    if(siteLogoElement) $(siteLogoElement).attr('src', uploadedLink + '/' + imageName);
    return objName;
}


function renderMultipleImages(multipleImages, objKey){
    var imagesWrapper = $('.' + objKey);

    bigLoaderEnable('.ajax-form-wrapper');

    $.each(multipleImages, function(key, value){

        var masonryItem = $('<div>', { class : 'masonry-item' });
        var singleItem = $('<div>', { class : 'single-img' });

        var magnificLink = $('<a>', { class : 'magnific-item', href : uploadedLink + '/' + value.image_name });
        $('<img>', { src: uploadedThumbLink + '/' + value.image_name, alt: '', }).appendTo(magnificLink);

        var deleteButton = $('<a>', { class : 'delete-btn' });
        $('<i>', { class: 'ion-close-round' }).appendTo(deleteButton);
        $(deleteButton).prependTo(singleItem);

        $(deleteButton).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            if (confirm('Are you sure?')) imageDelete(deleteButton, value.id);
            else return false;
        });

        $(magnificLink).prependTo(singleItem);
        $(singleItem).prependTo(masonryItem);
        $(masonryItem).prependTo(imagesWrapper);
    });

    $(imagesWrapper).masonry();
    $(imagesWrapper).masonry('destroy');


    $('.single-img').css({ 'opacity' : 0 });

    $(imagesWrapper).css({ 'max-height' : '0px' });

    $(imagesWrapper).imagesLoaded().always( function( instance ) {
        $(imagesWrapper).masonry({
            itemSelector: '.masonry-item',
        });

        $(imagesWrapper).css({ 'max-height' : '5000px' });

        $('.single-img').animate({
            opacity: 1,
        }, 300, function() {

        });

        bigLoaderDisable('.ajax-form-wrapper');

    });
}


function renderImagesAfterDelete(objKey, removedElem){
    $(removedElem).remove();
    var imagesWrapper = $('.' + objKey);

    $(imagesWrapper).masonry('destroy');

    $(imagesWrapper).masonry({
        itemSelector: '.masonry-item',
    });
}


function imageDelete($this, imageID){
    var formData = new FormData(),
        form = $($this).closest('form'),
        url = $(form).data('url');

    formData.append('id', imageID);

    $.ajax({
        url: url,
        method: 'POST',
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        
        beforeSend: function(){
            $('.ajax-bar').addClass('active').css('width', '0px');
        },
        
        error: function(err) {

            if(err.status == 404) renderTableMessage(form, 'Invalid Api.', true);
            else renderTableMessage(form, 'Something went wrong. Please try again.', true);
        },
        success: function(response) {

            var uploadedObj = JSON.parse(JSON.stringify(response));
            if (uploadedObj.status_code == 200) {

                var objKey = Object.keys(uploadedObj.data)[0];

                renderImagesAfterDelete(objKey, $($this).closest('.masonry-item'));

            } else alert(uploadedObj.message);
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


function uploadImageAjax($this, file){
    var url = $this.data('url'),
        id = $this.closest('form').find('[name="id"]').val(),
        form_data = new FormData();

    if(imageValidation(file)){
        form_data.append('image_name', file);
        form_data.append('id', id);

        $.ajax({
            url: url,
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
                if(err.status == 404) renderTableMessage($this, 'Invalid Api.', true);
                else renderTableMessage($this.closest('form'), 'Something went wrong. Please try again.', true);
            },

            success: function(response) {


                var uploadedObj = JSON.parse(JSON.stringify(response));
                if (uploadedObj.status_code == 200) {

                    renderTableMessage($this.closest('form'), uploadedObj.message, false);

                    var objName = renderAjaxImage(uploadedObj.data);
                    if(uploadedObj.data[objName].redirect != undefined) {
                        var currentUrl = window.location.href,
                            currentBaseUrl = currentUrl.split('#'),
                            currentTabID = (currentBaseUrl[1] != undefined) ? '#' + currentBaseUrl[1] : '';

                        window.location.href = currentBaseUrl[0] + '?' +uploadedObj.data[objName].redirect + currentTabID;
                    }

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

    if($.inArray(fileType, validFormat) < 0) alert(fileName + ' is in invalid format.');
    else if(maxUploadedFile < fileSize) alert('Max size of the image should not be grater than' + maxUploadedFile);
    else return true;
}



function audioValidation(file){
    var validFormat = [];
    $.each(supportedAudio, function(key, value){
        if(value == 'm4a') value = 'x-m4a';
        validFormat.push('audio/' + value);
    });

    var fileType = file.type,
        fileSize = file.size/(1024 * 1024),
        fileName = file.name;

    if($.inArray(fileType, validFormat) < 0) alert(fileName + ' is not an audio format.');
    else if(maxTrackSize < fileSize) alert('Max size of the image should not be grater than' + maxUploadedFile);
    else return true;
}



function singleTrackUpload(input){
    var file = input.get(0).files[0],
        formData = new FormData(),
        form = $(input).closest('form'),
        url = $(input).data('url'),
        uploadBy = $(input).data('upload-by'),
        uploadByValue = $('[name="' + uploadBy + '"]').val(),
        isValid = false;

    if(audioValidation(file)){

        var audioName;
        $.each(supportedAudio, function(key, value){
            if(file.name.endsWith(value)){
                audioName = file.name.split('.' + value);
                return;
            }
        });

        $('[name="title"]').val(audioName[0]);

        isValid = true;
        formData.append('track', file);
        formData.append('title', audioName[0]);
    }

    if(isValid){

        if(uploadByValue != undefined && uploadByValue != null && uploadByValue != '') {
            formData.append(uploadBy, uploadByValue);
        }

        $.ajax({
             url: url,
             method: 'POST',
             dataType: 'json',
             cache: false,
             contentType: false,
             processData: false,
             data: formData,
             beforeSend: function(){
                 $('.ajax-bar').addClass('active').css('width', '0px');
             },
             error: function(err) {

                 if(err.status == 404) renderTableMessage(form, 'Invalid Api.', true);
                 else renderTableMessage(form, 'Something went wrong. Please try again.', true);
             },
             success: function(response) {


                 var uploadedObj = JSON.parse(JSON.stringify(response));
                 if (uploadedObj.status_code == 200) {

                     renderTableMessage(form, uploadedObj.message, false);

                     var objName = Object.keys(uploadedObj.data)[0],
                         mainObj = uploadedObj.data[objName],
                         audio = mainObj['audio'],
                         audioKey = Object.keys(audio)[0],
                         audioName = audio[audioKey];


                     var audioElem = $('.' + objName + '.' + audioKey);
                     $(audioElem).find('source').attr('src', uploadedAudioLink + '/' + audioName);
                     audioElem[0].load();

                     resetPlayer(audioElem);

                     if(uploadedObj.data.redirect != undefined) {
                         var currentUrl = window.location.href,
                             currentBaseUrl = currentUrl.split('#'),
                             currentTabID = (currentBaseUrl[1] != undefined) ? '#' + currentBaseUrl[1] : '';

                         /*alert(uploadedObj.message);*/
                         window.location.href = currentBaseUrl[0] + '?' + uploadedObj.data.redirect + currentTabID;

                     } else {

                         renderTableMessage(form, uploadedObj.message, false);
                         var objKey = Object.keys(uploadedObj.data)[0],
                             mainObj = uploadedObj.data[objKey],
                             radio = mainObj['radio'];

                         if(mainObj['image'] != null && mainObj['image'] != ''){
                             var imageKey =  Object.keys(mainObj['image'])[0],
                                 imageName = mainObj['image'][imageKey];

                             $('.' + objKey).find('.' + imageKey).attr('src', uploadedLink + imageName);
                         }

                         if(radio != undefined && radio != null) {
                             for (var key in radio) {

                                 $($('[name="' + key + '"]')).each(function(){

                                     if($(this).val() == radio[key]) $(this).prop('checked', true);
                                     else  $(this).prop('checked', false);
                                 });
                             }
                         }

                     }

                 } else renderTableMessage(form, uploadedObj.message, true);
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
             },     mimeType:"multipart/form-data"
        });
    }
}




function multipleTrackUpload(input){
    var fileCount = input.get(0).files.length,
        formData = new FormData(),
        form = $(input).closest('form'),
        url = $(input).data('url'),
        uploadBy = $(input).data('upload-by'),
        uploadByValue = $('[name="' + uploadBy + '"]').val(),
        isValid = false;

    if(maxUploadedFileCount < fileCount) alert('You can upload maximum ' + maxUploadedFileCount + ' files per upload.');
    else {
        
        for (var i = 0; i < fileCount; ++i) {

            var _URL = window.URL || window.webkitURL,
                file = input[0].files[i];

            if(audioValidation(file)){
                isValid = true;
                formData.append('tracks[' + i + ']', file);
            }
        }

        if(isValid){
            if(uploadByValue != undefined && uploadByValue != null && uploadByValue != '') formData.append(uploadBy, uploadByValue);
            
            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                beforeSend: function(){
                    $('.ajax-bar').addClass('active').css('width', '0px');
                },
                error: function(err) {

                    if(err.status == 404) renderTableMessage(form, 'Invalid Api.', true);
                    else renderTableMessage(form, 'Something went wrong. Please try again.', true);
                },
                success: function(response) {


                    var uploadedObj = JSON.parse(JSON.stringify(response));

                    if (uploadedObj.status_code == 200) {

                        renderTableMessage(form, uploadedObj.message, false);

                        var objKey = Object.keys(uploadedObj.data)[0],
                            mainObj = uploadedObj.data[objKey];

                        renderMultipleTracks(mainObj, objKey);

                    } else renderTableMessage(form, uploadedObj.message, true);
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
}


function multipleImageUpload(input){
    var fileCount = input.get(0).files.length,
        formdata = new FormData(),
        form = $(input).closest('form'),
        url = $(form).data('url'),
        uploadBy = $(input).data('upload-by'),
        uploadByValue = $(form).find('[name="' + uploadBy + '"]').val(),
        isValid = false;

    formdata.append(uploadBy, uploadByValue);

    if(maxUploadedFileCount < fileCount) alert('You can upload maximum ' + maxUploadedFileCount + ' files per upload.');
    else {
        var fileIndex = 0;
        for (var i = 0; i < fileCount; ++i) {

            var _URL = window.URL || window.webkitURL,
                file = input[0].files[i];

            if(imageValidation(file)){
                isValid = true;
                formdata.append('image_name[' + fileIndex + ']', file);
                fileIndex++;
            }
        }

        if(isValid){
            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: formdata,
                beforeSend: function(){
                    $('.ajax-bar').addClass('active').css('width', '0px');
                },
                error: function(err) {

                    if(err.status == 404) renderTableMessage(form, 'Invalid Api.', true);
                    else renderTableMessage(form, 'Something went wrong. Please try again.', true);
                },

                success: function(response) {

                    var uploadedObj = JSON.parse(JSON.stringify(response));
                    if (uploadedObj.status_code == 200) {

                        renderTableMessage(form, uploadedObj.message, false);

                        var objKey = Object.keys(uploadedObj.data)[0],
                            mainObj = uploadedObj.data[objKey];

                        renderMultipleImages(mainObj, objKey);

                    } else renderTableMessage(form, uploadedObj.message, true);
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
                $this.after('<h6 class="err-msg">'+ 'Invalid Email.' + '</h6>');
            }
        }else if(ajaxField == 'dropdown') {
            if ($this.prop('selectedIndex') < 0) {
                validInput = false;
                $this.addClass('has-error');
                $this.after('<h6 class="err-msg">' + 'Required.' + '</h6>');
            }
        }else if(ajaxField == true){

            if(isEmpty($this.val())){

                validInput = false;
                $this.addClass('has-error');
                $this.after('<h6 class="err-msg">'+ 'Required.' + '</h6>');
            }

        }else if(ajaxField == 'numeric'){
            if(isEmpty($this.val())){
                validInput = false;
                $this.addClass('has-error');
                $this.after('<h6 class="err-msg">' + 'Required.' + '</h6>');

            }else if(!$.isNumeric($this.val()) || $this.val() == 0){
                validInput = false;
                $this.addClass('has-error');
                $this.after('<h6 class="err-msg">' + ucFirst($this.attr('name')) + ' must be valid numeric.' + '</h6>');
            }
        }else if(ajaxField == 'wysiwyg'){

            if(isEmpty($this.val())){

                validInput = false;
                $this.closest('.trumbowyg-box').addClass('has-error');
                $this.closest('.trumbowyg-box').after('<h6 class="err-msg">' + 'Required.' + '</h6>');

            }
        }else if(ajaxField == 'readonly-input'){

            if(isEmpty($this.find('input').val()) || $this.find('input').val() == 0 || $this.find('input').val() == ','){
                validInput = false;
                $this.addClass('has-error');
                $this.after('<h6 class="err-msg">'+ 'Required.' + '</h6>');
            }
        }
    });
    
    return validInput;
}


var clickedIds = [];

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

function emptyRenderFormMessage(form){
    $(form).find('.ajax-message').removeClass('active').removeClass('error');
}


function renderFormMessage(form, message, isError){
    if(isError) $(form).find('.ajax-message').addClass('active').addClass('error').text(message);
    else $(form).find('.ajax-message').addClass('active').addClass('success').removeClass('error').text(message);
}


function renderTableMessage(form, message, isError){
    if(isError) $(form).find('.ajax-message').addClass('active').addClass('error').removeClass('success').text(message);
    else $(form).find('.ajax-message').addClass('active').addClass('success').removeClass('error').text(message);
}


function removeTableMessage(container){
    $(container).find('.ajax-message').removeClass('success').removeClass('error').removeClass('active');
}


function bigLoaderEnable(container){
    (container).find('.loader-big.btn-loader').addClass('active');
    $(container).find('.loader-big .ajax-loader').addClass('active');
}


function bigLoaderDisable(container){

    $(container).find('.loader-big.btn-loader').removeClass('active');
    $(container).find('.loader-big .ajax-loader').removeClass('active');
}

function smLoaderEnable(container){
    $(container).find('.loader-sm .btn-text').removeClass('active');
    $(container).find('.loader-sm .ajax-loader').addClass('active');
}


function smLoaderDisable(container){
    $(container).find('.loader-sm .btn-text').addClass('active');
    $(container).find('.loader-sm .ajax-loader').removeClass('active');
}


function addHiddenInput(hiddenInput, id){
    var addedIds = $(hiddenInput).val();
    addedIds += id + ',';
    $(hiddenInput).val(addedIds);
}


function deleteHiddenInput(hiddenInput, id){
    var addedIds = $(hiddenInput).val();
    addedIds = addedIds.replace(id + ',', '');
    $(hiddenInput).val(addedIds);
}


function addTag(mainInput, hiddenInput, id, text){
    var inputTag = $('<span>', { text: decodeEntities(text), class: 'item-' + id });
    var deleteBtn = $('<a>', { class: 'remove-item-btn', href:'', 'data-id' : id });
    $('<i>', { class: 'ion-android-close' }).appendTo(deleteBtn);

    $(deleteBtn).appendTo(inputTag);
    $(inputTag).appendTo($(mainInput).find('.selected-items'));

    $(mainInput).find('.no-selected').removeClass('active');

    addHiddenInput(hiddenInput, id);

    $(deleteBtn).on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            id = $this.data('id');

        deleteHiddenInput(hiddenInput, id);

        var selectedItemsWrapper = $this.closest('.selected-items');

        $this.closest('span').remove();
        $('.db-items').find('[data-id="' + id + '"]').closest('.tag-item-wrapper').removeClass('selected');


        if($.trim($(selectedItemsWrapper).find('span').length) < 1){
            $(selectedItemsWrapper).find('.no-selected').addClass('active');
        }
    });
}


function addTagBtnTOList(deleteUrl, selected, dbItemsElem, id, value){

    if(selected) var tagItemWrapper = $('<div>', { class: 'tag-item-wrapper ok selected' });
    else var tagItemWrapper = $('<div>', { class: 'tag-item-wrapper ok' });

    if(deleteUrl != undefined) {
        $(tagItemWrapper).addClass('delete-enable');
        var deleteBtn = $('<a>', { class: 'delete-tag-btn', href:'', 'data-id' : id });
        $('<i>', { class: 'ion-android-close' }).appendTo(deleteBtn);
        $(deleteBtn).appendTo(tagItemWrapper);

        var wrapperClass = $(dbItemsElem).closest('.search-dropdown');

        $(deleteBtn).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            var currentTagItemWrapper = $(this).closest('.tag-item-wrapper'),
                isSelected = $(currentTagItemWrapper).hasClass('selected');

            $.ajax({
                url: deleteUrl,
                type: 'GET',
               data: { id: id },
                dataType : 'json',
                beforeSend: function(e){

                    if(confirm('Are you sure?')){

                        if(isSelected) {
                            renderTableMessage(wrapperClass, 'The item is selected.', true);
                            return false;
                        } else bigLoaderEnable(wrapperClass)

                    }else return false;


                },
                error: function(err) {
                    bigLoaderDisable(wrapperClass);
                    if(err.status == 404) renderTableMessage(wrapperClass, 'Invalid Api.', true);
                    else renderTableMessage(wrapperClass, 'Something went wrong. Please try again.', true);
                },
                success: function(response) {


                    var uploadedObj = JSON.parse(JSON.stringify(response));
                    if (uploadedObj.status_code == 200) {

                        $(deleteBtn).closest('.tag-item-wrapper').remove();
                        renderFormMessage(wrapperClass, uploadedObj.message, false);


                    }else renderFormMessage(wrapperClass, uploadedObj.message, true);

                    bigLoaderDisable(wrapperClass);
                },
            });

        });
    }

    $('<a>', { class: 'main-tag', href: '#', 'data-id' : id, text: decodeEntities(value) }).appendTo(tagItemWrapper);

    $(tagItemWrapper).appendTo(dbItemsElem);
}


function renderAudioPlayer(audio){
    var duration = 0;

    var playPauseBtn = $('<button>', { class: 'paused play-pause-btn' }).insertAfter(audio);

    var audioProgressSection = $('<div>', { class: 'audio-progress-section' }).insertAfter(audio);
    var audioProgressBarWrapper = $('<a>', { class: 'audio-progress-bar-wrapper' }).appendTo(audioProgressSection);
    var audioProgressBar = $('<div>', { class: 'audio-progress-bar' }).appendTo(audioProgressBarWrapper);

    var audioCurrentTime = $('<h6>', { class: 'audio-current-time', text: '00:00' }).appendTo(audioProgressSection);
    var audioDuration = $('<h6>', { class: 'audio-duration', text: '00:00' }).appendTo(audioProgressSection);


    var soundSection = $('<div>', { class: 'audio-sound-section' }).insertAfter(audio);
    var soundBtn = $('<a>', { class: 'audio-sound-btn' }).appendTo(soundSection);
    $('<i>', { class: 'ion-android-volume-up'}).appendTo(soundBtn);

    var soundProgressSection = $('<div>', { class: 'sound-progress-section' }).appendTo(soundSection);
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

        if(audio[0].paused) {
           $('audio').each(function(){

               var $this = $(this),
                   currentPlayPauseBtn = $this.siblings('.play-pause-btn');

               $this[0].pause();
               $(currentPlayPauseBtn).addClass('paused').removeClass('playing');
           });

            audio[0].play();
            $(playPauseBtn).addClass('playing').removeClass('paused');

        }else{
            audio[0].pause();
            $(playPauseBtn).addClass('paused').removeClass('playing');
        }
    });


    function getTime(t) {
        var m=~~(t/60), s=~~(t % 60);
        return (m<10?"0"+m:m)+':'+(s<10?"0"+s:s);
    }

    audio.on('loadedmetadata', function(e){
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
        }
        counter ++;
    });



    $(audio).on('ended', function() {
        $(playPauseBtn).addClass('paused').removeClass('playing');
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


function saveYoutubeLink(elem, youtubeID, youtubeDuration, youtubeTitle, url){
    var loaderWrapper = $(elem).closest('.loader-wrapper');
    var messageWrapper = $(elem).closest('.item-content');

    var uploadBy = $(elem).data('upload-by'),
        uploadByValue = $('[name="' + uploadBy + '"]').val(),
        youtubeLink = 'https://www.youtube.com/watch?v=' + youtubeID;

    var formData = new FormData();
    formData.append('title', youtubeTitle);
    formData.append('youtube_link', youtubeLink);
    formData.append('duration', youtubeDuration);
    formData.append(uploadBy, uploadByValue);


    $.ajax({
        url: url,
        method: 'POST',
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: formData,

        beforeSend: function(e){
            $('.ajax-bar').addClass('active');
            bigLoaderEnable(loaderWrapper);
        },

        error: function(err) {
            bigLoaderDisable(loaderWrapper);
            if(err.status == 404) renderTableMessage(messageWrapper, 'Invalid Api.', true);
            else renderTableMessage(messageWrapper, 'Something went wrong. Please try again.', true);
        },

        success: function(response) {

            var uploadedObj = JSON.parse(JSON.stringify(response));
            if(uploadedObj.status_code == 200){

                renderTableMessage(messageWrapper, uploadedObj.message, false);


                if(uploadedObj.data.redirect != undefined) {
                    var currentUrl = window.location.href,
                        currentBaseUrl = currentUrl.split('#'),
                        currentTabID = (currentBaseUrl[1] != undefined) ? '#' + currentBaseUrl[1] : '';

                    window.location.href = currentBaseUrl[0] + '?' + uploadedObj.data.redirect + currentTabID;
                }else{

                    var objKey = Object.keys(uploadedObj.data)[0],
                        mainObj = uploadedObj.data[objKey],
                        radio = mainObj['radio'];

                    if(radio != undefined && radio != null) {
                        for (var key in radio) {

                            $($('[name="' + key + '"]')).each(function(){

                                if($(this).val() == radio[key]) $(this).prop('checked', true);
                                else  $(this).prop('checked', false);
                            });
                        }
                    }

                }

            }else renderTableMessage(messageWrapper, uploadedObj.message, true);

            bigLoaderDisable(loaderWrapper);

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
    });
}


function convertYoutubeDuration(duration) {
    var a = duration.match(/\d+/g);

    if (duration.indexOf('M') >= 0 && duration.indexOf('H') == -1 && duration.indexOf('S') == -1) {
        a = [0, a[0], 0];
    }

    if (duration.indexOf('H') >= 0 && duration.indexOf('M') == -1) {
        a = [a[0], 0, a[1]];
    }
    if (duration.indexOf('H') >= 0 && duration.indexOf('M') == -1 && duration.indexOf('S') == -1) {
        a = [a[0], 0, 0];
    }

    duration = 0;

    if (a.length == 3) {
        duration = duration + parseInt(a[0]) * 3600;
        duration = duration + parseInt(a[1]) * 60;
        duration = duration + parseInt(a[2]);
    }

    if (a.length == 2) {
        duration = duration + parseInt(a[0]) * 60;
        duration = duration + parseInt(a[1]);
    }

    if (a.length == 1) {
        duration = duration + parseInt(a[0]);
    }
    return duration
}


function fetchingYoutubeData(elem, youtubeID, saveLinkApi){
    var loaderWrapper = $(elem).closest('.loader-wrapper');
    var messageWrapper = $(elem).closest('.item-content');

    var youtubeUrl = elem.data('youtube-url'),
        action = elem.data('action');


    $.ajax({
        url: youtubeUrl,
        method: 'POST',
        dataType: 'json',
        data: { action: action, youtube_id : youtubeID },

        beforeSend: function(e){
            $('.ajax-bar').addClass('active');
            bigLoaderEnable(loaderWrapper);
        },

        error: function(err) {
            bigLoaderDisable(loaderWrapper);

            if(err.status == 404) renderTableMessage(messageWrapper, 'Invalid Api.', true);
            else renderTableMessage(messageWrapper, 'Something went wrong. Please try again.', true);
        },

        success: function(response) {
            var uploadedObj = JSON.parse(JSON.stringify(response));

            if(uploadedObj.status_code == 200){

                var videoTitle = uploadedObj.data.title,
                    videoDuration = uploadedObj.data.duration;

                $(elem).closest('form').find('[name="title"]').val(videoTitle);
                saveYoutubeLink(elem, youtubeID, convertYoutubeDuration(videoDuration), videoTitle, saveLinkApi);

            }else renderTableMessage(messageWrapper, 'Something went wrong. Please try again.', true);

            bigLoaderDisable(loaderWrapper);
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
    });
}




function searchTrackForPlaylist(url, value, wrapper) {
    var searchedTracks = $('#searched-tracks');

    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
        data: { search: value },
        beforeSend: function(e){
            $(wrapper).addClass('active');
            $(searchedTracks).text('');
            bigLoaderEnable(wrapper);
            emptyRenderFormMessage(wrapper);
        },
        error: function(err) {
            renderTableMessage(wrapper, 'Something went wrong. Please try again.', true);
        },
        success: function(response) {

            var uploadedObj = JSON.parse(JSON.stringify(response));
            if(uploadedObj.status_code == 200){

                if(!isEmpty(uploadedObj.data)){
                    $.each(uploadedObj.data, function(key, value){

                        var singleTrackWrapper = $('<div>', { class: 'single-track' });

                        $('<p>', { class: 'left-area', text: decodeEntities(value.title) }).appendTo(singleTrackWrapper);
                        var addToplaylistBtn = $('<a>', { href: '#', class: 'right-area', text: 'Add to Playlist' }).appendTo(singleTrackWrapper);

                        addToPlaylist(addToplaylistBtn, value, wrapper);

                        $(singleTrackWrapper).appendTo(searchedTracks);
                    });

                }else renderTableMessage(wrapper, 'No Data Found.', false);
            }else renderTableMessage(wrapper, uploadedObj.message, true);

            bigLoaderDisable(wrapper);
        },
    });
}


function addToPlaylist(addToplaylistBtn, value, wrapper){
    $(addToplaylistBtn).on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        $.ajax({
            url: addToPlaylistAPI,
            method: 'POST',
            dataType: 'json',
            data: { track_id: value.id, playlist_id: playlistID },
            beforeSend: function(e){
                $(wrapper).addClass('active');
                bigLoaderEnable(wrapper);
                emptyRenderFormMessage(wrapper);
            },
            error: function(err) {
           
            },
            success: function(response) {

                var uploadedObj = JSON.parse(JSON.stringify(response));
                if(uploadedObj.status_code == 200){

                    if(uploadedObj.data != null){
                        var addedTrack = {
                            remove_from_pl : value.id,
                            value: value
                        };

                        var trackContainer = $('.multiple_tracks');

                        $(renderSingleTrack(trackContainer, addedTrack)).appendTo(trackContainer);

                        renderTableMessage(wrapper, uploadedObj.message, false);

                    }else renderTableMessage(wrapper, uploadedObj.message, false);
                }else  renderTableMessage(wrapper, uploadedObj.message, true);

                bigLoaderDisable(wrapper);
            }
        });

    });
}


function trackRemoveFromPlaylist($this, url, id){
    var wrapper = $this.closest('.item-content');

        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: { track_id: id, playlist_id: playlistID },
            beforeSend: function(e){
                $(wrapper).addClass('active');
                bigLoaderEnable(wrapper);
                emptyRenderFormMessage(wrapper);
            },
            error: function(err) {
                renderTableMessage(wrapper, 'Something went wrong. Please try again.', true);
            },
            success: function(response) {

                var uploadedObj = JSON.parse(JSON.stringify(response));
                if(uploadedObj.status_code == 200){

                    $this.closest('.single-track-container').remove();

                }else renderTableMessage(wrapper, uploadedObj.message, true);

                bigLoaderDisable(wrapper);
            }
        });
}



(function ($) {
    "use strict";
    
    /*SEARCH FOR PLAYLIST*/

    var typingTimer;
    var doneTypingInterval = 200;
    var $input =  $('#audio-search'),
        searchedTextWrapper = $('#search-tracks-wrapper');

    $input.on('keyup', function () {
        var $this = $(this);
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function(){

            var searchedText = $this.val(),
                url = $this.data('url');

            if(!isEmpty(searchedText)) searchTrackForPlaylist(url, searchedText, searchedTextWrapper);

        }, doneTypingInterval);
    });

    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });

    $input.on('click', function(){

        var typedText = $(this).val();


        if(!isEmpty(typedText)) $(searchedTextWrapper).addClass('active');

    });


    $(document).click(function(e) {
        var container = $('.search-track-wrapper');
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            $('#search-tracks-wrapper').removeClass('active');
            $('.tp-layer').removeClass('active');
        }
    });


    /*AUDIO PLAYER*/

    $('audio').each(function(){
        renderAudioPlayer($(this));
    });


    $('#multiple-track-upload').on('change', function(e){
        e.preventDefault();
        e.stopPropagation();

        multipleTrackUpload($(this));
    });


    $('#track-upload').on('change', function(e){
        e.preventDefault();
        e.stopPropagation();

        singleTrackUpload($(this));
    });


    $(document).on('click', '.remove-from-pl-link', function(e){
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            id = $this.data('id'),
            url = $this.closest('[data-remove-from-pl-link]').data('remove-from-pl-link');

        if (confirm('Are you sure?')) {

            trackRemoveFromPlaylist($this, url, id);

        } else return false;
    });


    $(document).on('click', '.delete-link', function(e){
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            id = $this.data('id'),
            url = $this.closest('[data-delete-link]').data('delete-link');

        if (confirm('Are you sure?')) {

            deleteItem($this, url, id);

        } else return false;
    });


    $('#audio-link-add').find('a').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();



        var $this = $(this),
            url = $this.data('url'),
            uploadBy = $this.data('upload-by'),
            uploadByInput = $this.closest('form').find('[name="' + uploadBy + '"]'),
            uploadByVal = $(uploadByInput).val(),
            thisWrapper = $('#audio-link-add'),
            audioLink = thisWrapper.find('input').val(),
            loaderWrapper = $this.closest('.loader-wrapper'),
            currentForm = $this.closest('form'),
            ajaxBar = currentForm.find('.ajax-bar');

        $(thisWrapper).removeClass('has-error');
        $(thisWrapper).siblings('.err-msg').remove();

        $(ajaxBar).addClass('active');


        if(audioLink != undefined && $.trim(audioLink) != '') {

            var validFormat = false;
            $.each(supportedAudio, function(key, value){
                if(audioLink.endsWith(value)) validFormat = true;
            });


            if(audioLink.startsWith("http") && validFormat){

                var audio = document.createElement('audio');
                audio.src = audioLink;
                audio.load();

                audio.addEventListener('loadedmetadata',function(){

                    var obj = {};
                    obj[uploadBy] = uploadByVal;
                    obj['audio_link'] = audioLink;
                    obj['duration'] = audio.duration.toFixed(0);

                    $.ajax({
                        url: url,
                        method: 'POST',
                        dataType: 'json',
                        data: obj,

                        beforeSend: function(e){
                            bigLoaderEnable(loaderWrapper);
                        },
                        error: function(err) {
                            bigLoaderDisable(loaderWrapper);

                            if(err.status == 404) renderTableMessage(currentForm, 'Invalid Api.', true);
                            else renderTableMessage(currentForm, 'Something went wrong. Please try again.', true);
                        },
                        success: function(response) {


                            var uploadedObj = JSON.parse(JSON.stringify(response));
                            if(uploadedObj.status_code == 200){

                                if(uploadedObj.data.redirect != undefined) {
                                    var currentUrl = window.location.href,
                                        baseUrl = currentUrl.split('?'),
                                        currentBaseUrl = currentUrl.split('#'),
                                        currentTabID = (currentBaseUrl[1] != undefined) ? '#' + currentBaseUrl[1] : '';

                                    window.location.replace(currentBaseUrl + '?' + uploadedObj.data.redirect);


                                } else if(uploadedObj.data.album_page){

                                    var trackWrapper = $('.multiple_tracks.album');
                                    var singleLem = renderSingleTrack(trackWrapper, uploadedObj.data.album_page)

                                    $(singleLem).prependTo(trackWrapper);

                                }else if(uploadedObj.data.artist_page){

                                    var trackWrapper = $('.multiple_tracks.artist');
                                    var singleLem = renderSingleTrack(trackWrapper, uploadedObj.data.artist_page)

                                    $(singleLem).prependTo(trackWrapper);

                                }else renderForm(uploadedObj.data);

                            }else renderTableMessage(currentForm, 'Something went wrong. Please try again.', true);

                            bigLoaderDisable(loaderWrapper);
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
                    });

                },false);
                
            }else {
                $(thisWrapper).addClass('has-error');
                $(thisWrapper).after('<h6 class="err-msg">'+ "Invalid Link." + '</h6>');
            }

        } else {
            $(thisWrapper).addClass('has-error');
            $(thisWrapper).after('<h6 class="err-msg">'+ "Link can't be empty." + '</h6>');
        }
    });

    
    $('#youtube-link-add').find('a').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            youtubeLinkWrapper = $('#youtube-link-add'),
            youtubeLinkInput = $(youtubeLinkWrapper).find('input'),
            youtubeLinkValue = $(youtubeLinkInput).val(),
            youtubeId = youtubeLinkValue.split('?v=')[1],
            saveLinkApi = $this.data('link-save-url');

        $(youtubeLinkWrapper).removeClass('has-error');
        $(youtubeLinkWrapper).siblings('.err-msg').remove();


        if(youtubeId != undefined) fetchingYoutubeData($this, youtubeId, saveLinkApi);
        else {
            $(youtubeLinkWrapper).addClass('has-error');
            $(youtubeLinkWrapper).after('<h6 class="err-msg">'+ 'Invalid Youtube Link.' + '</h6>');
        }
    });


    $('.close-search-dropdown').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            wrapper = $this.closest('.dropdown-search-input');

        $(wrapper).find('.search-dropdown').removeClass('active');
    });


    /*SEARCH INPUT ADD*/

    $('[data-add-url]').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            url = $this.data('add-url'),
            wrapper = $this.closest('.attached-button'),
            title = $.trim(wrapper.find('input').val()),
            messageWrapper = $('.search-dropdown'),
            formData= new FormData,
            dbItemsElem = $this.closest('.dropdown-search-input').find('.db-items'),
            deleteUrl = $(dbItemsElem).data('url');

        if(title != undefined && title!= '' && title != null){
            formData.append('title', title);

            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,

                beforeSend: function(){
                    smLoaderEnable(wrapper);
                },
                error: function(err) {
                    smLoaderDisable(wrapper);

                    if(err.status == 404) renderTableMessage(messageWrapper, 'Invalid Api.', true);
                    else renderTableMessage(messageWrapper, 'Something went wrong. Please try again.', true);
                },
                success: function(response) {
                    smLoaderDisable(wrapper);

                    var uploadedObj = JSON.parse(JSON.stringify(response));
                    if (uploadedObj.status_code == 200) {

                        renderTableMessage(messageWrapper, uploadedObj.message, false);

                        addTagBtnTOList(deleteUrl, false, dbItemsElem, uploadedObj.data.id, uploadedObj.data.title);

                    } else renderTableMessage(messageWrapper, uploadedObj.message, true);
                },
            });
        }
    });


    /*ADD ITEM INTO INPUT BOX*/

    $(document).on('click', '.db-items a.main-tag', function(e){
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            id = $this.data('id'),
            text = $this.text(),
            wrapper = $this.closest('.dropdown-search-input'),
            readonlyInput = $(wrapper).find('.readonly-input'),
            hiddenInput = $(readonlyInput).find('input'),
            initialParent = $this.closest('.tag-item-wrapper');

        if(!initialParent.hasClass('selected')){
            initialParent.addClass('selected');

            var mainInput = $this.closest('.dropdown-search-input').find('.readonly-input');
            addTag(mainInput, hiddenInput, id, text);

        }else{
            var selectedItemsWrapper = $(readonlyInput).find('.selected-items');

            deleteHiddenInput(hiddenInput, id);
            initialParent.removeClass('selected');
            $('.item-' + id).remove();

            if($(selectedItemsWrapper).find('span').length < 1){
                $(selectedItemsWrapper).find('.no-selected').addClass('active');
            }
        }
    });


    $(document).on('click', '[data-dropdown-value-id]', function(e){
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            dropdownValueId = $this.data('dropdown-value-id'),
            dropdownWrapper = $this.closest('.dropdown-search-input'),
            searchDropdown = $this.closest('.search-dropdown'),
            readonlyInput = $(dropdownWrapper).find('.readonly-input'),
            innerHtml = $($this).html();

        $(searchDropdown).removeClass('active');
        $(readonlyInput).find('input').val(dropdownValueId);


        $(readonlyInput).find('.selected-item').html(innerHtml);
    });
    


    $('.search-dropdown').find('input').on('keyup', function(e){
        var $this = $(this),
            text = $this.val().toLowerCase();

        $('.db-items').find('.tag-item-wrapper').addClass('ok');

        $.each($('.db-items').find('.main-tag'), function(){

            var currentA = $(this),
                tagItemWrapper = currentA.closest('.tag-item-wrapper');
            var str = $(this).text().toLowerCase();
            var res = str.search(text);

            if(res == -1) tagItemWrapper.removeClass('ok');
        });
    });



    /*CLICK EVENT FOR OPEN SEARCH DROPDOWN. GET ALL THE NAMES*/

    var fetchedBefore = [];
    $(document).on('click', '.readonly-input', function(e){
        e.preventDefault();
        e.stopPropagation();

        $('.search-dropdown').removeClass('active');

        var $this = $(this),
            wrapper = $this.closest('.dropdown-search-input'),
            url = $this.data('url'),
            dbItemsElem = $(wrapper).find('.db-items'),
            deleteUrl = $(dbItemsElem).data('url'),
            singleDropdown = ($(wrapper).data('single-dropdown')) ? $(wrapper).data('single-dropdown') : false;

        $(wrapper).find('.search-dropdown').addClass('active');

        removeTableMessage(wrapper);


        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){

                if(url in fetchedBefore) return false;
                else bigLoaderEnable(wrapper);

            },
            error: function(err) {

                bigLoaderDisable(wrapper);

                if(err.status == 404) renderTableMessage($(wrapper), 'Invalid Api.', true);
                else renderTableMessage($(wrapper), 'Something went wrong. Please try again.', true);
            },
            success: function(response) {
                
                bigLoaderDisable(wrapper);

                var uploadedObj = JSON.parse(JSON.stringify(response));
                if (uploadedObj.status_code == 200) {

                    fetchedBefore[url] = uploadedObj.data;

                    if(singleDropdown){

                        var dropdownValueWrapper = $(wrapper).find('.dropdown-items');

                        $.each(uploadedObj.data, function(key, value){

                            var dropdownValue = $('<a>', { href: '#', class: 'single-dropdown tag-item-wrapper ok', 'data-dropdown-value-id' : value.id });

                            $('<img>', { class: 'dropdown-value-image', src: uploadedLink + value.image_name }).appendTo(dropdownValue);
                            $('<p>', { class: 'dropdown-value-title main-tag', text: decodeEntities(value.title) }).appendTo(dropdownValue);

                            $(dropdownValue).appendTo(dropdownValueWrapper)
                        });

                    }else{
                        var selectedTagIdArr = [];
                        var added_tags_arr = $(wrapper).find('.readonly-input').find('input').val().split(',');

                        $.each(added_tags_arr, function(key, value){
                            if(value != '') selectedTagIdArr.push(parseInt($.trim(value)));
                        });


                        $.each(uploadedObj.data, function (key, value) {

                            var selectedTag = false;
                            if($.inArray(parseInt(key), selectedTagIdArr) >= 0) selectedTag = true;

                            addTagBtnTOList(deleteUrl, selectedTag, dbItemsElem, key, value);
                        });
                    }

                } else renderTableMessage(wrapper, uploadedObj.message, true);
            },
        });
    });


    $(document).on('click', function(e) {
        var container = $('.dropdown-search-input');

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            $('.search-dropdown').removeClass('active');
        }
    });


    $('.multiple-files-upload').on('change', function(e){
        e.preventDefault();
        e.stopPropagation();

        multipleImageUpload($(this));
    });


    $('.ajax-sidebar-dropdown').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        
        if($(window).width() < 576) $('.ajax-sidebar').toggleClass('active');
    });


    $('#toggle-sidebar').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        $('.tp-layer').toggleClass('active');
        $('#sidebar').toggleClass('active');
    });


    $('#toggle-setting').on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        $('#setting-dropdown').toggleClass('active');
    });


    $(document).click(function(e) {
        var container = $('#setting-dropdown');
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            $('#setting-dropdown').removeClass('active');
        }
    });


    $(document).click(function(e) {
        var container = $('#sidebar');
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            $('#sidebar').removeClass('active');
            $('.tp-layer').removeClass('active');
        }
    });


    $(document).click(function(e) {
        var container = $(".ajax-sidebar");
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            $('.ajax-sidebar').removeClass('active')
        }
    });


    $('.ajax-img-upload').on('change', function(e){
        e.preventDefault();
        e.stopPropagation();

        var $this = $(this),
            _URL = window.URL || window.webkitURL,
            file = $this[0].files[0];


        if ($this[0].files && file) {

            if(imageValidation(file)){

                var reader = new FileReader();

                reader.onload = function (e) {

                    var cropperModal = $('<div>', { id: 'cropper-popup', class: 'active' });
                    var imageWrapper = $('<div>', { id: 'crop-img-wrapper' });

                    var $image = $('<img>', {
                        id: 'cropable-img',
                        src: e.target.result,
                        style: 'padding: 30px; max-height: 500px; max-width: 600px;'
                    }).appendTo(imageWrapper);

                    var cropper = $image.cropper({
                        movable: true,
                        zoomable: false,
                        rotatable: false,
                        scalable: false,
                        aspectRatio: 1/1,
                        viewMode: 2,
                    });


                    var deleteBtn = $('<a>', { href: '#', class: 'delete-btn' });
                    $('<i>', { class: 'ion-close-round' }).appendTo(deleteBtn);
                    $(deleteBtn).appendTo(cropperModal);

                    var btnWrapper = $('<div>', { class: 'btn-wrap' });
                    var cancelBtn = $('<a>', { href: '#', text: 'Cancel' }).appendTo(btnWrapper);
                    var cropBtn = $('<a>', { href: '#', text: 'Crop' }).appendTo(btnWrapper);

                    $(btnWrapper).appendTo(imageWrapper);

                    $(cropBtn).on('click', function(e){

                        $image.cropper('getCroppedCanvas', {
                            width: 500,

                        }).toBlob(function (blob) {

                            Object.defineProperties(blob, {
                                type: {
                                    value: file.type,
                                    writable: true
                                },
                            });


                            blob.lastModifiedDate = new Date();
                            blob.name = file.name;

                            var blobToFile = new File([blob], file.name, {type: file.type, lastModified: Date.now()});

                            uploadImageAjax($this, blobToFile);

                        },  file.type);

                        $(cropperModal).removeClass('active');
                    });


                    $(deleteBtn).on('click', function(e){
                        $(cropperModal).removeClass('active');
                    });

                    $(cancelBtn).on('click', function(e){
                        $(cropperModal).removeClass('active');
                    });

                    $(imageWrapper).prependTo(cropperModal);
                    $(cropperModal).prependTo('body');

                };

                reader.readAsDataURL(file);
            }
        }

        $this.val('');


    });
    
    $('.ajax-form').on('submit', function(e){
        e.stopPropagation();
        e.preventDefault();

        var $this = $(this),
            url = $this.data('url');

        if(validateForm($this)) updateFormContent(url, $this);
    });


    $('.ajax-sidebar').find('a').on('click', function(e){
        $("html, body").animate({ scrollTop: 0 }, "slow");
        
        $('.ajax-sidebar').find('a').removeClass('active');
        $('.ajax-form-wrapper').find('form').removeClass('active');

        $('.ajax-bar').removeClass('active');

        $(this).addClass('active');
        $($(this).attr('href')).addClass('active');

        $('.ajax-sidebar').removeClass('active');

        var currentFormID = $(this).attr('href'),
            currentFormDataUrl = $(currentFormID).data('url');

        if($.inArray(currentFormID, clickedIds) < 0) {
            clickedIds.push(currentFormID);

            getFormContent(currentFormDataUrl, currentFormID);
        }
    });


    $('.room-sidebar').find('a').on('click', function(e){
        $("html, body").animate({ scrollTop: 0 }, "slow");

        $('.room-sidebar').find('a').removeClass('active');
        $('.room-form').find('form').removeClass('active');

        $(this).addClass('active');
        $($(this).attr('href')).addClass('active');
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



    $('.image-input').on('change', function (e) {
        var uploadedImage = $(this).closest('.image-upload').find('.uploaded-image'),
            uploadContent = $(this).closest('.image-upload').find('.upload-content');
        $(uploadedImage).attr('src', '');
        $(uploadContent).show();

        var _URL = window.URL || window.webkitURL,
            file = $(this)[0].files[0],
            img = new Image(),
            targetResolution = $(this).data("traget-resolution");

        if(file){
            var fileType = file["type"],
                fileSize = file["size"] / (1024 *1024),
                validImageTypes = ["image/jpeg", "image/png"];

            if ($.inArray(fileType, validImageTypes) < 0) {
                $(this).val('');
                alert("Invalid FileType");
            }else if(fileSize > maxUploadedFile){
                $(this).val('');
                alert('Uploaded Image : ' + fileSize.toFixed(2) + 'MB (Maximum file size : ' + maxUploadedFile + 'MB)');
            }else{
                img.src = _URL.createObjectURL(file);
                img.onload = function() {
                    var imgwidth = this.width,
                        imgheight = this.height;

                    if(targetResolution) $('input[name=' + targetResolution + ']').attr('value', imgwidth + ':' + imgheight);

                    $(uploadedImage).attr('src', img.src);
                    $(uploadedImage).addClass('active').fadeIn(2000);
                    $(uploadContent).hide();
                };
            }
        }
    });


    $(document).on('click', '.removable-image', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if(confirm("Are You Sure?")){
            var $this = $(this);
            removeExistingFile($this)
        }
        return false;
    });


    $('[data-validation]').on('submit', function(e){
        var form = $(this);
        $('.image-upload').removeClass('has-error');
        $('input').removeClass('has-error');
        $('select').removeClass('has-error');
        $('textarea').removeClass('has-error');
        $('.trumbowyg-box').removeClass('has-error');
        $('.err-msg').remove();
        var hasError = false;

        $($(this).find('[data-required]')).each(function(){
            var $this = $(this);

            if(($this.attr('type') != 'hidden') && ($this.data('required') != false)){

                if($this.data('required') == 'wysiwyg'){

                    if(isEmpty($this.val())){

                        hasError = true;
                        $this.closest('.trumbowyg-box').addClass('has-error');
                        $this.closest('.trumbowyg-box').after('<h6 class="err-msg">' + 'Required.' + '</h6>');

                    }
                } else if($this.data('required') == 'dropdown'){
                    if($(this).prop('selectedIndex') < 0){
                        hasError = true;
                        $this.addClass('has-error');
                        $this.after('<h6 class="err-msg">' + 'Required.' + '</h6>');
                    }
                }else if($this.data('required') == 'image'){

                    if(isEmpty($this.val())){
                        if(isEmpty($this.attr('value'))){
                            hasError = true;
                            var imageUpload = $this.closest('.image-upload');
                            imageUpload.addClass('has-error');
                            imageUpload.after('<h6 class="err-msg">' + 'Required.' + '</h6>');
                        }
                    }

                }else if($this.data('required') == 'video'){

                    if(isEmpty($this.val())){
                        if(isEmpty($this.attr('value'))){
                            hasError = true;
                            var imageUpload = $this.closest('.video-upload');
                            imageUpload.addClass('has-error');
                            imageUpload.after('<h6 class="err-msg">' + 'Required.' + '</h6>');
                        }
                    }

                    if(!hasError){


                    }

                }else if($this.data('required') == true){
                    if($this.attr('type') == 'file') var val = $this.attr('value');
                    else var val = $.trim($this.val());

                    if(isEmpty(val)){
                        hasError = true;
                        $this.addClass('has-error');
                        $this.after('<h6 class="err-msg">'+ 'Required.' + '</h6>');
                    }

                    if(hasError) $(form).attr('data-has-error', true);
                    else  $(form).attr('data-has-error', false);

                }else if($this.data('required') == 'numeric'){
                    if(isEmpty($this.val())){
                        hasError = true;
                        $this.addClass('has-error');
                        $this.after('<h6 class="err-msg">' + 'Required.' + '</h6>');

                    }else if(!$.isNumeric($this.val()) || $this.val() == 0){
                        hasError = true;
                        $this.addClass('has-error');
                        $this.after('<h6 class="err-msg">' + ucFirst($this.attr('name')) + ' must be valid numeric.' + '</h6>');
                    }
                }
            }
        });

        if(hasError) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
	});


    $(document).on('click', '[data-confirm]', function(e){
        if (!confirm($(this).data('confirm'))) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });


	if(isExists('.uploaded-image')){
		if(!isEmpty($('.uploaded-image').attr('src'))){
			$(this).find('.upload-content').hide();
		}
	}


	$(window).bind("load", function() {
		if(isExists('.masonry-grid')){
			$('.masonry-grid').masonry({
				itemSelector: '.masonry-item',
                percentPosition: true,
			});
		}
	});


    $('[data-ajax-field="wysiwyg"]').trumbowyg({
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

})(jQuery);
