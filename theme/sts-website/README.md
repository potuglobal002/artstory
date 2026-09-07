# STS Institute — website

Static, responsive, Tailwind CSS (Play CDN). No build step. Drag the whole folder onto Netlify.

## Pages

| File | Purpose |
|---|---|
| `index.html` | Landing — top bar, video hero with nav overlay, journey rail, services, popular programs, why trust us, beginner-to-success, success stories, blog, footer |
| `about.html` | About Us |
| `courses.html` | All courses, filterable |
| `course.html?c=slug` | Individual course page |
| `course-details.html?c=slug` | Full syllabus, assessment, fees, policies |
| `blogs.html` | Blog listing |
| `blog-details.html?p=slug` | Article |
| `success-stories.html` | Filterable, searchable story grid |
| `inspiring-journey.html` | Long-form featured journey |
| `contact.html` | Contact form, department lines, campuses, map |
| `admission.html` | Three-step admission form |

Shared header, footer, all content data and interactions live in `assets/site.js`, so editing one file updates every page.

## Edit the content

Everything editable is at the top of `assets/site.js`:

- `SITE` — logo, phones, emails, campuses, social links
- `COURSES` — the 8 programs (fees, schedule, modules, FAQ). Add an object here and it appears in the nav dropdown, the courses grid, the footer and the admission form automatically
- `JOURNEY` — the timeline stops on the landing page
- `STORIES` — success stories grid
- `BLOGS` — blog posts (`body` is plain HTML)

## Hook the forms to your sheet

Set one line in `assets/site.js`:

```js
const FORM_ENDPOINT = "https://script.google.com/macros/s/AKfy.../exec";
```

The admission and contact forms then POST as `application/x-www-form-urlencoded`. In your Apps Script `doPost(e)`, the fields arrive in `e.parameter` — admission sends `course, fullname, mobile, email, education, previous, target, deadline, slot, campus, note, consent`; contact sends `name, phone, email, topic, message`. Leave it empty and the forms still validate and confirm without sending.

## Hero video

Drop your film at `assets/img/sts-at-a-glance.mp4` and a still at `assets/img/hero-poster.jpg`. It takes over automatically. Until then an animated fallback runs — four slow-zooming scenes with a drifting grid — so the hero is never a dead rectangle.

Optional scene stills: `assets/img/glance-1.jpg` … `glance-4.jpg`. If missing, gradients are used.

## Photos

Every photo slot falls back to a designed placeholder if the file is missing, so nothing breaks while you gather images. To add real ones, pass a `src` as the third argument of `photo()` in `assets/site.js`.

## Colours and type

Set in `assets/app.css` (CSS variables) and `assets/tw-config.js` (Tailwind tokens):
primary `#f38020`, secondary `#192335`, white UI, Bricolage Grotesque / Instrument Sans / IBM Plex Mono.

## Before going live

Swap the Tailwind Play CDN for a compiled stylesheet if you want the smallest possible payload —
`npx tailwindcss -i input.css -o assets/tw.css --minify` — then replace the two CDN script tags with one `<link>`.
Not required; the site works as-is.
