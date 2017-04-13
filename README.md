# Contao Sermoner
Sermoner is an Open Source management tool for sermons, church services, seminars, workshops for the Open Source PHP Content Management System [Contao][1]. Whenever you have an audio file to put up on the internet Sermoner will help you manage it. 

`Attention: Version 1.*.* is not compatible with prior versions. Please make a manual backup of database tables tl_sermon_archive, tl_sermoner_items and tl_sermon_feed. To use your data from those tables, please import them manually in the corresponding table tl_news, tl_news_archive and tl_news_feed.`

## Usage
### Add a sermon
Contao Sermoner adds two fields (speaker, moderator) in the news section. Please use existing news fields as followed:
* title: sermon title
* date: sermon date
* news teaser: Short description of the message
* image: season image oder image of the speaker
* add enclosure: use enclosure for the audio file (you can also put other images, scripts, slides etc to the enclosure. please make sure there is just one audio file.)

To play the audio file directly in the fronted use template *news_sermon* in the your module.

### Podcast Feed
If you want to publish your sermons as a rss feed (e.g. for iTunes) have a look under *RSS Feeds* in the news section and set the rss feed as a podcast-feed. There you can provide itunes categories.

[1]: https://contao.org