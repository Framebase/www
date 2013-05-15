window.onload=function(){

  // Get all players with class name "framebase-player"
  var players = document.getElementsByClassName('framebase-player');

  // Iterate and add the video file to each one
  for (var i=0; i<players.length; i++){
    p = players[i];

    var xmlHttp = null;

    xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", "http://api.framebase.io/videos.json/" +p.dataset.video, false );
    xmlHttp.send( null );

    vdata = JSON.parse(xmlHttp.responseText);
    console.log(vdata);

    // Create the video tag
    p.className = p.className + ' video-js vjs-default-skin';
    p.width = p.width ? p.width : 480;
    p.height = p.height ? p.height : 240;

    p.setAttribute('controls', p.getAttribute('controls') ? p.getAttribute('controls') : true);
    p.setAttribute('preload', p.getAttribute('preload') ? p.getAttribute('preload') : 'auto');
    p.setAttribute('data-setup', p.getAttribute('data-setup') ? p.getAttribute('data-setup') : '{}');

    // Create the source element
    videoSrc = document.createElement('source');
    videoSrc.src = "assets/res/test.mp4";
    videoSrc.type = 'video/mp4';

    p.appendChild(videoSrc);
  }

  // Get the script tag
  var tag = document.getElementById('framebasevideo');

  // Create JS
  var vidjs = document.createElement('SCRIPT');
  vidjs.src = '/assets/js/video.js';

  // Create CSS
  var vidcss = document.createElement('LINK');
  vidcss.href = '/assets/css/video-js.css';
  vidcss.rel = 'stylesheet';
  vidcss.type = 'text/css';

  // Add CSS + JS
  document.head.appendChild(vidcss);
  document.head.appendChild(vidjs);
};
