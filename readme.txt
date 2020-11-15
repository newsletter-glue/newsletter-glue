=== WordPress email posts to subscribers (For Mailchimp, MailerLite, Sendinblue) - Newsletter Glue ===

Plugin name: Newsletter Glue - Email posts to subscribers, connect Mailchimp MailerLite
Contributors: newsletterglue, lesleysim, ahmedfouade
Donate link: https://paypal.me/newsletterglue
Tags: newsletter, email, mailchimp, mailerlite, sendinblue
Requires at least: 5.3
Tested up to: 5.5
Requires PHP: 7.0
Stable Tag: 1.1.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send blog posts as email newsletters to your subscribers from WordPress. Connect to Mailchimp, MailerLite, Sendinblue… 

== Description ==
**Email subscribers your blog posts without leaving WordPress. Create newsletters using the Gutenberg editor. Connect to Mailchimp, MailerLite, Sendinblue, and more.**

## When it's time to share your latest post, do you:
**Look longingly at Substack**, wishing you could auto-publish to your site and subscribers too?

**Head grudgingly to Mailchimp** and painstakingly re-create your WordPress post as a newsletter?

**Hope to set up an RSS campaign...** One of these days?

**Rely uneasily on RSS**, because there's no way to confirm which version got sent, until it's in your inbox?

**Procrastinate?**

## What if you could just:
#### Check a box, and hit publish?

https://www.youtube.com/watch?v=0LiLb3KKarE


## With Newsletter Glue you can:
* Connect WordPress to Mailchimp, MailerLite, Sendinblue and Campaign Monitor
* Email subscribers without leaving WordPress
* Get easy-to-share past issues, and a search engine-friendly newsletter archive (When your newsletter is a blog post, your newsletter archives are your blog archives.)
* Send test emails before publishing
* Choose the audience, segments, and tags for your newsletter
* Set up in 2 minutes (Literally. I timed it.)
* Selectively show/hide content blocks in your email/blog
* Design your email newsletter to look different from your blog

## Watch full feature walk through

https://www.youtube.com/watch?v=CJWl6m_byxg

**0:44 Onboarding starts:** I'll go from setting up Newsletter Glue from the first time to sending the first post to subscribers in 2 min 30 secs.

**4:07 "Send as newsletter" features inside the WordPress editor:** This is what you see for every post you send. We pre-load defaults for you so you don't have to fill everything out from scratch for every post you send. You can change the defaults in Settings.

**8:18 Status log:** See the sent/not sent status for every newsletter you send. Even when you send multiple newsletters from one post.

**9:05 Connect:** This is where you add new integrations to your email service providers (ESPs). Right now, we've got Mailchimp, MailerLite, Sendinblue, and Campaign Monitor, with lots more ESPs in the future!

**9:51 Settings:** Here's where you can change your email defaults (the pre-filled details in the WordPress editor).

There's also a Custom CSS section which you can use to add CSS to your newsletter which won't show up in your post.

 == Frequently Asked Questions == 
## FAQ
= What if I don't use Mailchimp, MailerLite, Sendinblue or Campaign Monitor? =

We're in the process of integrating other email service providers (ESP). Check back for more updates soon!


= How will my post look as an email? =

Your post will be sent as a simple and clean email. To see it, you can send a test email from inside the WordPress editor before publishing your post. This will let you check your post before sending it to all your subscribers.

= Does this just send plain text? What if I want a custom design? =

You can customise your newsletter design by adding custom CSS in the Settings. Go to Newsletter Glue > Settings > Custom CSS.

The CSS you add here will show up in the emails you send, but not in your blog posts.

To learn more about adding custom CSS to your email newsletter, [head here](https://docs.newsletterglue.com/article/13-custom-css-email).

= What if I don't want to publish my full post as a newsletter? =

You're not alone. Others use Newsletter Glue as it lets them use the WP editor to write their newsletter.

Simply add a new post, and create your newsletter. Don't forget to put it in a different category, so that your newsletter will have its own archive.

= Why not directly send from WordPress using SendGrid or Amazon SES, instead of connecting to an external email service provider? =

There's a lot more to email service providers (ESPs) than sending emails.

There's subscriber maintenance, tagging, scheduling, automation, sequences and more. Building an entire ESP inside WordPress isn't our focus right now.

If that's what you're looking for, MailPoet is an amazing option.

= Do you have a sign-up form I can add to my blog? =

We don't have a sign-up form right now. But we're thinking about building one...

For now, you can use a separate form plugin for this purpose. Mailchimp, MailerLite, Sendinblue and Campaign Monitor also have basic sign up forms to get you up and running quickly.

= I'm planning to set up a members-only newsletter with restricted content. Will your plugin do that? =

Glad you asked! We're launching these features at the end of the year. 

But in the mean time, you can use Paid Member Subscriptions or Paid Memberships Pro to set up your memberships.

You'll also need to set up a corresponding tag inside Mailchimp (or the email service provider you're using) for your paid members. 

And when it comes time to publish, all you need to do is select the right subscriber tag to ensure you're only sending your email to paid members.

= Will you have a premium upgrade? =

We're working on it! Stay tuned!

== Screenshots ==

1. Check **Send as newsletter** then simply hit **Publish...**
2. Send test email
3. Real time demo of test email (receive in 5 seconds)
4. All email details are auto-filled for you. Look out for the green **"Your email is ready to publish."** notification.
5. Settings page - Your email defaults can be changed here.
6. Connect new email service providers
7. Add custom CSS to your email newsletter
8. Example of how sent newsletter looks like in Gmail
9. Newsletter theme designer
10. Newsletter Group Block (for showing/hiding content in your email/blog)

== Changelog ==

= 1.1.6, November 12, 2020 =

* **New feature:** Add preview text to each newsletter. Preview text shows up next to your Subject line in inboxes. It's a great way to give subscribers a sneak peek of your email and convince them to open it. Find it next to the **Subject** field in the **Send as newsletter** section.
* **New feature:** You can now choose if you want to add the blog post's title to the top of each newsletter. Previously, every newsletter automatically added the blog post title. This gives you more flexibility and control over your newsletter's layout. Find this feature at the bottom of the Newsletter Theme Designer (Newsletter Glue > Settings > Newsletter Theme Designer).
* **New feature:** Alignment options (left, center, right) for headers and paragraph text can now be found in the Newsletter Theme Designer. Note this will only show up in your newsletter. It will not affect or appear in your published blog post or in your block editor.

= 1.1.5, October 29, 2020 =

* **Improvement:** You can now choose to **disable** Newsletter Glue on selected custom post types. Go to **Newsletter Glue > Settings > Additional** tab to do this.
* Minor UI and copy improvements.

= 1.1.4, October 13, 2020 =

* **Bug fix:** Fixed bug causing our **show/hide content** blocks to "break" some CSS.
* **New feature:** You'll now find a **Newsletter block manager** in the Newsletter Glue admin bar. Use it to manage blocks and discover new ones. Go to **Newsletter Glue > Newsletter Blocks**

= 1.1.3, October 7, 2020 =

* **Bug fix for Sendinblue users:** There was a conflict between our plugin and the official Sendinblue plugin making it impossible to connect to Newsletter Glue. We've fixed this and you should be able to connect again.
* **Improvement:** You can now use Newsletter Glue for all **custom post types**.

= 1.1.2, October 3, 2020 =

* Bug fix: Featured image not loading for certain file sizes.
* Bug fix: Fixed & sign in title not loading in emails.
* Bug fix: Fixed newsletter block where hidden content was appearing in the rss feed.

= 1.1.1, October 2, 2020 =

**New feature:**

* You can now add your logo to your newsletter in the **newsletter theme designer**.

**New integration:**

* We now connect with **Campaign Monitor**!

**Improvements:**

* Added **"Send as newsletter" checkbox** on the top toolbar in the **post editor**. This lets you quickly confirm you'll be sending a newsletter when you hit the **Publish/Update button**, and makes it easy to turn off the option.
* "Add header image" is now changed to "Add featured image" to make it clearly different from the new logo feature. This can now be found in the **newsletter theme designer**.
* "**Seamlessly sent by Newsletter Glue**" has also been moved to the **newsletter theme designer.**
* Added simple forms for you to quickly **report bugs** and **request features** in the top toolbar in the settings pages.
* Made a bunch of tiny UI and microcopy improvements across the plugin to improve your experience.

= 1.1.0, September 7, 2020 =

* New feature: **Newsletter theme designer** lets you customise fonts and colours for your newsletter. Head to **Newsletter Glue > Settings** then the **Newsletter theme designer** tab to use it.
* New feature: **Newsletter Glue group** custom block lets you hide content from your posts or emails. For example, add a special message to your email subscribers that won't show up in your blog.
* New feature: Added **Position header image** option, so that you can choose if you want your header image to be above or below the headline.
* Bug fix: Mailchimp users previously could only see up to 10 audiences and segments. This has been fixed so that all audiences and segments show up now.

= 1.0.3, August 26, 2020 =

* New integration: We now connect with Sendinblue!
* Bug fix: Fixed email responsiveness causing longer images to get squished in emails
* Other small improvements: Updated microcopy across plugin

= 1.0.2, August 19, 2020 =

* New feature: You can now add header images to your newsletter. Use featured image or select a default image in settings.
* Bug fix: Fixed email responsiveness causing images to get squished in emails
* Improvement: Scheduling a post for publishing now works for newsletters too
* Other small improvements: Simplified UI and copy across plugin

= 1.0.1, August 14, 2020 =

* Bug fix: Segments now show up properly when switching between audiences for Mailchimp connection.

= 1.0.0, August 10, 2020 =

* First public release on WordPress.