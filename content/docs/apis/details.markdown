# Details API

The Details API is a very simple way to play back videos in any application.

# Location

Details requests are GET requests to `https://api.framebase.io/videos/[video_id].json`, where `[video_id]` is the ID returned by the API
when the video was uploaded

# Data Format

The API will return a JSON-encoded object containing the following properties:

 * fileUri - Location of the video file
 * fileUriHttps - TLS-enabled location of the video file
 * rtmpUri - RTMP stream location of the video file
 * transcodingInfo - Information about the transcoding status of the video
   * status - The state of the video. See below.
   * timeStarted - The time transcoding was started
   * fileLength - Size of the source file in seconds
   * fileSize - Size of the source file in bytes
   * eta - Estimated time remaining in seconds
   * percentageComplete - Integer from 0-100, representing how complete the transcoding is
   * errors - an object containing any error details which occurred during the transcoding

## Status Types

The status property of transcodingInfo will be one of the following strings:

 * queued - Waiting for available transcode server
 * running - Transcoding in progress
 * completed - Transcoding done
 * canceled - Transcoding was canceled
 * failed - Transcoding failed
