---
Title: Javascript Library
Meta: Fuck meta
---

The Javascript Library allows you to quickly add Framebase to your web-based application.

# Getting Started

Getting started with the Javascript library is extremely simple. Like any other Javascript libraries, you'll need to include it on your
page. Add the following code to the bottom of your site, right above the closing `</body>` tag:

~~~ {.prettyprint}
<script src="//framebase.io/assets/framebase-js/framebase.js"></script>
<script type="text/javascript">
    framebase_init({
        token: 'your_framebase_token'
    });
</script>
~~~

You can get your token from the Framebase dashboard. That's it - Framebase is ready! Let's take a look at how easy it is to use:

---

# Uploading Videos

Framebase makes uploading videos work like other form field types. With the built-in uploader widget, you can start accepting video uploads
in no time. Just add `<input type="framebase" />` to an existing form:

~~~ {.prettyprint}
<h1>Upload a Video</h1>
<form method="post">
    <input type="text" name="title" placeholder="Video Title" />
    <input type="framebase" name="videoID" />
</form>
~~~

## Storing Uploaded Videos

With Framebase, you don't need to worry about storing uploads, CDNs, transcoding, or almost anything else. When a user uploads a file with
the Javascript Library, it goes to our server, where we quickly convert it to multiple formats and distribute it around the world.

The Javascript Library sends an ID to your server to store. When the user submits the above form, `videoID` will be a GUID which represents
the video.

~~~
POST /
Host: example.com

title=My+awesome+video&videoID=002653c4-4b9e-4422-af90-738d7ee78599
~~~

Store that short string in your database - it's all you need to do operations on the video later. In pseudocode, this might look something
like:

~~~ {.prettyprint}
$my_sql_adapter->insert('INSERT INTO `videos` (`title`, `videoID`) VALUES (?, ?);', [$_POST['title'], $_POST['videoID']]);
~~~

## Recording Videos

We've seen how easy it is to let users upload videos, but what if you want to allow users to record their own videos? Framebase makes that
easy, too! Just add `record="true"` to an `<input type="framebase" />`:

~~~ {.prettyprint}
<h1>Record a Greeting!</h1>
<form method="post">
    <input type="framebase" record="true" name="greeting_videoID" />
</form>
~~~

# Playing Videos

Once Framebase is loaded, playing a video is simple. Remember that video ID we stored in the previous step? Create a video element in your
page:

~~~ {.prettyprint}
<video type="framebase" data-video="002653c4-4b9e-4422-af90-738d7ee78599" />
~~~

Again, make sure you have the Javascript library loaded. This will automatically create a cross-platform video player.

---

# Customization

## Analytics

Framebase supports sending analytics about plays, recordings, and uploads to third-party analytics providers. You can specify your account
information for these providers, as well as which events you want tracked, in `framebase_init()`:

~~~ {.prettyprint}
framebase_init(
{
    token: 'your_framebase_token',
    analytics: {
        track: ['video_start', 'video_play', 'video_pause'],
        providers: {
            'Google Analytics': 'UA-XXXXXX-XX',
            'Mixpanel': 'XXXXXXXXXX',
            'KISSmetrics': 'XXXXXXXXXX'
        }
    }
}
~~~

### Supported Analytics Providers

 * Google Analytics
 * Mixpanel
 * KISSmetrics

### Supported Track Events

 * video_start - The video started playing for the first time
 * video_play - The video was played
 * video_pause - The video was pause
 * video_stop - The video stopped, either because the page was unloaded, or it was watched in its entirety. (If it was watched, `complete` will be true)
 * upload_success - The user uploaded a video
 * upload_error - An upload failed
 * record_success - The user recorded and saved a video
 * record_error - An error occurred when recording a video
 * record_discard - The user discarded their recording

## Javascript Events

Framebase.js supports interacting with custom Javascript. You can bind functions to events in framebase_init to work globally. When binding
to events in this way, all events are sub-objects of the "events" object.

A bound event can be either an individual function, or an array of them. `this` provides state information.

~~~ {.prettyprint}
framebase_init(
{
    token: 'your_framebase_token',
    events: {
        video: {
            play: [
                function(){
                    alert('Hey I see you played a video!');
                },
                function(){
                    console.log('The user played a video!', this);
                    // "this" will refer to the video object
                }
            ]
        }
    }
})
~~~

### Supported Events

 * video.start - The video started playing for the first time
 * video.play - The video was played
 * video.pause - The video was pause
 * video.stop - The video stopped, either because the page was unloaded, or it was watched in its entirety.
 * upload.success - The user uploaded a video
 * upload.error - An upload failed
 * record.success - The user recorded and saved a video
 * record.error - An error occurred when recording a video
 * record.discard - The user discarded their recording

### Local Events

You can also bind events on a specific player, recorder, or uploader using the `register_callback(event_name, lambda)` function. When
adding callbacks this way, `event_name` should not be namespaced, e.g. `events.video.play` would be simply `play`.

~~~ {.prettyprint}
<video type="framebase" data-video="002653c4-4b9e-4422-af90-738d7ee78599" id="myvideo" />
<script type="text/javascript">
    document.getElementById('myvideo').register_callback('pause', function(){
        alert("Hey, why'd you stop watching?");
    })
</script>
~~~

## Player Control

The player can also be controlled through Javascript. You can do this by calling methods on the object directly:

~~~ {.prettyprint}
<video type="framebase" data-video="002653c4-4b9e-4422-af90-738d7ee78599" id="myvideo" />
<a href="#" id="playbutton">Play!</a>
<script type="text/javascript">
    document.getElementById('playbutton').onclick = function(){
        document.getElementById('myvideo').play();
    }
</script>
~~~

### Supported Control Events

 * play()
 * pause()
 * stop()
 * seek(seconds)

---

# Extra Considerations

## Compatibility

The Javascript library is compatible with the three most-recent versions of Internet Explorer, Chrome, Firefox, Safari, and Opera.

Version                 | Internet Explorer | Chrome  | Firefox | Safari  | Opera   | Mobile Safari | Mobile Chrome
----------------------: | :---------------: | :-----: | :-----: | :-----: | :-----: | :-----------: | :-----------:
            **Current** | **Yes**           | **Yes** | **Yes** | **Yes** | **Yes** | **Yes**       | **Yes**
   **One Version Back** | **Yes**           | **Yes** | **Yes** | **Yes** | **Yes** | **Yes**       | **Yes**
  **Two Versions Back** | **Yes**           | **Yes** | **Yes** | **Yes** | **Yes** | **Yes**       | **Yes**
**Three Versions Back** | *No*              | **Yes** | **Yes** | *No*    | *No*    | **Yes**       | **Yes**
 **Four Versions Back** | *No*              | **Yes** | **Yes** | *No*    | *No*    | **Yes**       | **Yes**

## Speed

A recorder, uploader, and player are all exposed by the Javascript. None of these will be loaded until used
to keep bandwidth use low. The library will automatically handle this lazy-loading.
