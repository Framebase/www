---
Title: Upload API
---

The Upload API is a very simple way to add cross-application support for video features.

# Location

Upload requests are POST requests to `https://api.framebase.io/videos.json`

# Sending Videos

To upload a video, the raw contents of the file should be provided as a "file" in a single or multi-part form POST.

The request must include the GET or POST parameter `token`, which is your Framebase token.

## Response

The server will respond with a JSON-encoded object with the following properties:

 * response - An HTTP error code. If this is not integer 200, an error occurred.
 * videoID - The video ID assigned by the server to your upload. Store this value.
 * errors - If response was non-200, this will be present as an array of error message strings
