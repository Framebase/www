# Implementing a custom CTA 

Let's say you have a video on your front page demoing your product. At the end of the video, you want to show a pop-up that asks the user to sign up or share the video. With Framebase, this is extremely easy and we'll show you how.

---

## Before we begin

If you haven't checked out the [Getting Started JavaScript Guide](https://stage.www.fss.int/docs/libraries/javascript) out yet, make sure to take a look there first to see how to include and initialize the Framebase library inside your web app.

In my sample project I'm also using some 3rd party libraries including:

- [Bootstrap](http://twitter.github.io/bootstrap/) for the CSS
- [Fancybox](http://fancyapps.com/fancybox/) to display the pop-up
- [jQuery](http://jquery.com/)
## Setting up a Fancybox popup
First, include the required libraries.
~~~ {.prettyprint}
<link href="assets/css/jquery.fancybox.css" rel="stylesheet">
<script src="assets/js/jquery.fancybox.js"></script>
~~~
Second, create a div with display set to hidden. Within this div, include whatever HTML you would like to be displayed within your pop-up. In this case, we're displaying a simple text and a Facebook like button.
~~~ {.prettyprint}
<div id="social" style="display:none">
    <h2>Yippie diddly do!</h2>
    Did you like the video? Be the first to share it with your friends!
    <div class="fb-like" data-href="https://framebase.io" data-send="true" data-width="450"></div>
</div>
~~~
Next, you'll want to create an anchor link where the `href` is the id name of the hidden div.
~~~ {.prettyprint}
<a class='fancybox' href='#social' /><a>
~~~
Finally, you'll need to initialize the Fancybox library somewhere in your code with the class of your anchor link.
~~~ {.prettyprint}
$(".fancybox").fancybox();
~~~
## Firing an event on stop

Framebase exposes several events via the JavaScript. The one we want to hook into is the `stop()` method which fires when the video reaches the end.

~~~ {.prettyprint}
<script>
framebase_init(
{
    token:"7e2d397e7c52137284759831f0bf502fb0ba53865788a12e6759acd0ee39a5fd",
    events: {
      video: {
        stop: function(){
          console.log("Video Stopped")
          $('.fancybox').click(); 
        }
      }
    }
});
</script>
~~~
Here, when the video is stopped, we're going to simulate click on our FancyBox object that will render our pop-up.

## That's it

Easy, right? You view the source code of the sample project [here]().

