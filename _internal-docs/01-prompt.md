# Dav/Devs CMS

## Story

- Dav/Devs stands for Davina Develops

## Goal

- I want to move my personal NextJS Dav/Devs website to Laravel to turn it into a web application.

## Website

- davinaleong.com

## What I Want

- Tech stack:
	- Laravel
	- Blade + Tailwind + Alpine
	- MySQL
	- Laravel Cloud
	- Cloudinary (or recommend a better option)
	- React for the Tools posts
- Privacy-first
- Security-first
- SEO-friendly
- Simple image browser and manager. Include a lightbox for viewing
	- Should QR codes be handled separately?
	- Store standard pratice image metadata
- Link manager
	- Store standard pratice link metadata
	- Label
	- URL
	- Target
	- ...
- YouTube embed manager
	- Store standard pratice video metadata
- Layout manger (mainly for product pages)
	- Store standard pratice Laravel layout metadata
- Post manager
	- Assigned a Layout
	- Type manager
		- Page
		- Project
		- Article
		- Tool
		- ...
		- (scan through the website)
	- Category manager
		- Isolated by type
	- Tags
		- Isolated by type
	- Links
	- Metadata
		- OG Image as Image
	- Link multiple Images
	- Link multiple Youtube embeds
	- Programatically-calculated read time
	- Additional content like the Lemon Squeezy store UUIDs for eBooks
		- (Suggest better ways to store these kind of content)
	- Markdown editor for the post content
	- Status
		- Draft
		- Private
		- Published
	- Sharing to social media links
		- LinkedIn
		- Facebook
		- Instagram
		- Threads
- Secure anonymous like/unlike system
- (Bonus) Include an AI provider
	- Generate post detail & content
	- Audit post detail & content
- Sitemap generator
- Good UX search entire website
- Performance-driven CMS pagination
- Performance-driven frontend pagination
	- Enable endless scrolling on listing pages on mobile
- Settings:
	- Frontend display date format
	- CMS display date format
	- Header
		- Navbar
			- Brand Image as Image
			- Brand name
	- Footer
		- Copyright information
- Log everything
	- Store standard pratice log metadata
- Show-off website's Lighthouse score
- Light/Dark mode
- Export all data
- QR Code 2FA for Admin Panel
- (Bonus) Headless CMS
- (Bonus) GraphQL API
	
## Some Cavets

- Original jokes come in 2 variants:
	- DO NOT include them in the main post list
	- Q/A
	- Statements
	- (Suggest a good UX for this)
	- Refreshes on new launch
	- Q/A variant
		- Show Q with a loader or timer
		- A revealed after that
- NO COMMENTS system

## Tasks

- Give me some inspritation for the new design:
	- Portfolio
	- CMS
- Generate the milestone checklist for this project
- A content migration strategy from the NextJS project to this Laravel CMS
	I have the project locally on my machine
- DB Schemas


---

## Action

look through the all spec docs in the internal folder and start implementation in these stages:
- panel
- site

until checklist is 100% checked

favicons are in the public folder

use this workflow: implement > document progress in a separate document > update checklist > commit and push > repeat
