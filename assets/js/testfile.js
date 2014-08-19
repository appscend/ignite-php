var currentSong = null;
var playlistSongs = [];
var queueSongs = []; //contains 'p' elements of the songs
var scrollingTitleBarTimeout = 0;

$(document).ready(function(){
	$.ajax({
		url: 'ajax.php?action=getTree',
		dataType: 'json',
		success: afterLoad
	});
});

function afterLoad(data, textStatus, jqXHR) {
	metalDirs = data;
	$('#metalTree').kendoTreeView({
		dataSource: metalDirs,
		animation: {
			expand: {
				effects: 'fadeIn'
			}
		}
	});

	$('.k-top, .k-bot, .k-mid').each(function(index, elem){
		if(!index)
			return;

		if(elem.nextSibling !== null && elem.nextSibling.classList[0] == 'k-group')
			return;

		var f = function(){
			var artist = $('.k-item:has(.k-state-selected)').first().find('div:first-child > .k-in').first().html();
			var album = $('.k-state-selected').text();

			$.ajax({
				url: 'ajax.php?action=getAlbumSongs&artist='+encodeURIComponent(artist)+'&album='+encodeURIComponent(album),
				dataType: 'json',
				success: function(data, textStatus, jqXHR) {
					summonSongList(data, artist, album);
				}
			});
		};

		$(elem).click(function(e){
			if (!e.ctrlKey) {
				$('#songs p, #songsList ~ span').remove();
				$('#total-time').html('--:--');
				$('#elapsed-time').html('--:--');
				$('#songSeek').attr('value', 0);

				playlistSongs = [];
				queueSongs = [];
				buzz.all().pause();
				buzz.sounds = [];
				currentSong = null;

			}

			setTimeout(f, 100);
			});
	});
}

function summonSongList(data, artist, album) {
	var templateContent = $('#songsList')[0].content;
	var songsCount = playlistSongs.length;
	var i = songsCount;

	for(song in data.results) {
		templateContent.querySelector('span').textContent = data.results[song].duration;
		templateContent.querySelector('span').style.float = 'right';
		templateContent.querySelector('span').style.paddingLeft = '10px';
		templateContent.querySelector('p').textContent = (i+1)+'. '+data.results[song].display;
		templateContent.querySelector('p').dataset.filename = data.results[song].filename;
		templateContent.querySelector('p').dataset.artist = artist;
		templateContent.querySelector('p').dataset.album = album;

		$('#songs').append(templateContent.cloneNode(true));
		i++;
	}

	var selector = songsCount ?  '#songs > p:gt('+(songsCount-1)+')' : '#songs > p';

	$(selector).each(function(index, element){
		playlistSongs.push(element);

		$(element).on('click', function(e){
			if(e.ctrlKey) {
				if(!$(element).hasClass('queued-song'))
					queueSong(this);
				else
					dequeSong(this);
			} else {
				prepareSong(this);
			}
		});
	});

	$(window).off('keyup').on('keyup', function(e){
		if (e.which == 66) //b
			nextSong();
		else if (e.which == 88) //x
			togglePlay();
	});

	$('#playbackButton').off('click').on('click', function(){
		togglePlay();
	});

	$('#nextSong').off('click').on('click', function(){
		nextSong();
	});

	$('#repeatSong').off('click').on('click', function() {
		if($(this).hasClass('repeated')) {
			currentSong.unloop();
			$(this).removeClass('repeated');
		} else {
			currentSong.loop();
			$(this).addClass('repeated');
		}
	});

	$('#stopSong').off('click').on('click', function(){
		currentSong.stop();
		$('#playbackButton').css({backgroundImage: "url('media-playback-start-48.png')"});
	});
}

//takes the 'p' DOMElement
function prepareSong(element) {
	$('#total-time').html('--:--');
	$('#elapsed-time').html('--:--');
	$('#songSeek').attr('value', 0);
	$('.current-song').removeClass('current-song');
	$(element).addClass('current-song');

	buzz.all().pause();
	buzz.sounds = [];
	currentSong = null;

	clearTimeout(scrollingTitleBarTimeout);

	var songData = element.dataset;
	var songFileName = encodeURIComponent(songData.filename);
	var albumArt = 'Metal/'+songData.artist+'/'+songData.album+'/folder.jpg';

	currentSong = new buzz.sound('Metal/'+encodeURIComponent(songData.artist)+'/'+encodeURIComponent(songData.album)+'/'+songFileName, {autoplay: true, volume: getLocalStorageVolume()});
	currentSong.bind('loadstart', function(){
		if($('#loadingAnim').length === 0)
			$('body').append('<img id="loadingAnim" style="display: inline-block;float: right; position: relative; right: 100px;" src="loading-animation.gif">');
	});

	currentSong.load();
	document.title = ' *** '+element.innerHTML;

	loadSetVolumeStorage();
	$('#volume').attr('value', currentSong.getVolume()).off('change').on('change', function(e){
		currentSong.setVolume(e.target.value);
		saveVolumeStorage(e.target.value);
	});

	if($('#albumArt').attr('src', albumArt).length === 0)
		$('body').prepend('<img id="albumArt" src="'+albumArt+'" style="position: fixed; opacity: 0.5; z-index: -1; max-width: 600px; max-height: 600px; right: 0; top: 0;" onerror="this.style.display=\'none\'" onload="this.style.display=\'block\'">');

	$('#volume').off('mouseup').on('mouseup', function(e){
		if(e.target.value > 75)
			this.previousSibling.style.backgroundImage = "url(1375443338_audio-volume-high-panel.png)";
		else if (e.target.value > 50 && e.target.value <= 75)
			this.previousSibling.style.backgroundImage = "url(1375443471_audio-volume-medium-panel.png)";
		else if (e.target.value > 25 && e.target.value <= 50)
			this.previousSibling.style.backgroundImage = "url(1375443468_audio-volume-low-panel.png)";
		else
			this.previousSibling.style.backgroundImage = "url(1375443473_audio-volume-low-zero-panel.png)";
	});

	$('#songSeek').off('click').on('click', function(e){
		currentSong.setPercent(parseInt((e.offsetX/400)*100));
	});

	currentSong.bind('timeupdate', function(e){
		$('#elapsed-time').html(buzz.toTimer(currentSong.getTime()));
		$('#songSeek').attr('value', currentSong.getPercent());
		$('#downloadProgress').attr('value', parseInt(currentSong.getBuffered()[0].end/currentSong.getDuration()*100));
	});

	currentSong.bind('canplay', function(e) {
		$('#loadingAnim').fadeOut(400, function(){$(this).remove()});
		$('#total-time').html(buzz.toTimer(currentSong.getDuration()));
		$('#playbackButton').css({backgroundImage: "url('media-playback-pause-64.png')"});
		(function titleMarquee() {
			document.title = document.title.substring(1)+document.title.substring(0,1);
			scrollingTitleBarTimeout = setTimeout(titleMarquee, 300);
		})();
	});

	currentSong.bind('ended', function(e) {
		if ($('#repeatSong').hasClass('repeated'))
			return;

		$('#playbackButton').css({backgroundImage: "url('media-playback-start-48.png')"});
		nextSong();
	});
}

function dequeSong(element) {
	var index = queueSongs.indexOf(element);
	$(queueSongs[index]).removeClass('queued-song').prev().remove();
	queueSongs.splice(index, 1);

	for(var i=0; i < queueSongs.length; i++) {
		queueSongs[i].previousElementSibling.innerHTML = '['+(i+1)+']';
	}
}

function queueSong(element) {
	$(element).addClass('queued-song');
	queueSongs.push(element);
	$(element).before('<span style="float: right;">['+queueSongs.length+']</span>');
}

function nextSong() {
	if (queueSongs.length > 0) {
		prepareSong(queueSongs[0]);
		$(queueSongs.shift()).removeClass('queued-song').prev().remove();

		for(var i=0; i < queueSongs.length; i++) {
			queueSongs[i].previousElementSibling.innerHTML = '['+(i+1)+']';
		}

	} else {
		prepareSong(playlistSongs[parseInt(Math.random() * (playlistSongs.length-1)) ]);
	}
}

function togglePlay() {
	if (!currentSong.isPaused()) {
		pauseSong();
	} else {
		playSong();
	}
}

function playSong() {
	$('#playbackButton').css({backgroundImage: "url('media-playback-pause-64.png')"});
	currentSong.togglePlay();
}

function pauseSong() {
	$('#playbackButton').css({backgroundImage: "url('media-playback-start-48.png')"});
	currentSong.togglePlay();
}

function getLocalStorageVolume() {
	return localStorage['volume'] !== undefined ? localStorage['volume'] : 100;
}

function loadSetVolumeStorage() {
	if (localStorage['volume'] !== undefined)
		$('#volume').attr('value', localStorage['volume']);
	else {
		$('#volume').attr('value', 100);
		saveVolumeStorage(100);
	}
}

function saveVolumeStorage(val) {
	localStorage['volume'] = val;
}