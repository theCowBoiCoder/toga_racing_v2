# Toga Racing Design Language

This document is a handoff for designers and AI coding agents building a separate application that should feel like it belongs to the same product family as the Toga Racing website. Reuse the visual language and interaction principles; do not blindly copy page layouts or racing-specific content when the new product has different needs.

## Creative direction

The style is **premium virtual motorsport editorial**: fast, focused, disciplined, and cinematic. It combines the confidence of a race-team identity with the clarity of a modern product interface.

The experience should feel:

- Bold and competitive, without becoming noisy or aggressive.
- Dark and immersive, with off-white content and restrained purple depth.
- Editorial rather than dashboard-heavy.
- Precise: strong alignment, short labels, purposeful spacing, and minimal ornament.
- Photographic and atmospheric, using real action imagery beneath controlled overlays.

Avoid generic gaming aesthetics. Do not add neon glows, rainbow gradients, fake carbon fibre, excessive speed lines, chrome effects, or crowded telemetry. The identity comes from scale, contrast, imagery, and typography—not decoration.

## Design tokens

Use these values as the starting point. Semantic names are preferred so the palette can evolve without rewriting components.

```css
:root {
  --color-canvas: #100e0e;        /* Primary near-black page background */
  --color-brand-deep: #1a0b4c;    /* Dominant Toga purple */
  --color-brand-panel: #26145c;   /* Raised purple surface */
  --color-text: #ededed;          /* Main text and primary accent */
  --color-text-muted: #c9c3da;    /* Secondary copy */
  --color-line: rgb(237 237 237 / 16%);
  --color-line-strong: rgb(237 237 237 / 35%);
  --color-scrim: rgb(16 14 14 / 82%);
  --color-success: #4ade80;       /* Status only; use sparingly */

  --font-sans: "Montserrat", Arial, sans-serif;

  --radius-control: 3px;
  --radius-field: 8px;
  --radius-card: 8px;
  --radius-pill: 999px;

  --shadow-card: 0 24px 65px rgb(0 0 0 / 18%);
  --page-gutter: max(5vw, 28px);
  --content-max: 1380px;
  --copy-max: 650px;

  --motion-fast: 200ms;
  --motion-standard: 350ms;
  --ease-standard: ease;
}
```

The old orange token visible in the base stylesheet is overridden by the current theme and is not part of the intended visual direction. In this system, off-white is the accent color.

## Color usage

- Use near-black for the page canvas, footer, dark cards, and moments of visual rest.
- Use deep purple for large branded sections, image overlays, page heroes, and alternating bands.
- Use panel purple for raised surfaces on purple backgrounds.
- Use off-white for primary text, active borders, buttons, and graphic emphasis.
- Use muted lavender-gray for body text. Do not reduce important text to low-contrast gray.
- Borders are typically one pixel and translucent. They define structure quietly.
- Alternate near-black and purple sections to create rhythm across long pages.

Recommended image treatment:

```css
background:
  linear-gradient(90deg, #1a0b4c 2%, rgb(26 11 76 / 88%) 40%, rgb(16 14 14 / 18%)),
  linear-gradient(0deg, #100e0e 0%, transparent 52%),
  url("...") center / cover;
```

Adjust the gradient direction to protect text and keep important subjects visible. Photography should be cool-toned, slightly subdued, and allowed to disappear into the surrounding surface.

## Typography

Use Montserrat throughout, with weights 400, 600, 700, 800, and 900.

### Display headings

- Uppercase, weight 800–900.
- Tight line height: `0.86` to `1`.
- Tight tracking: approximately `-0.045em` to `-0.075em`.
- Large hero range: `clamp(48px, 9vw, 132px)`.
- Section-heading range: `clamp(35px, 4.4vw, 64px)`.
- Break lines for visual composition, not after every few words.

### Eyebrows and metadata

- Uppercase, weight 900.
- `9px` to `11px`, with `0.12em` to `0.2em` tracking.
- Often introduced by a short 28 × 2px off-white rule.
- Use concise phrases such as “FROM THE PADDOCK” or “BUILT FOR MORE PACE.”

### Body copy

- `13px` to `17px`, regular or semibold.
- Line height `1.7` to `1.9`.
- Use muted text color and keep lines roughly 55–70 characters wide.
- Write short, confident sentences. Motorsport language can add flavor, but clarity comes first.

## Layout and spacing

- Use full-width color or image bands with content constrained to `1380px`.
- Default horizontal gutter: `max(5vw, 28px)`.
- Standard section padding: about `100px` vertically on desktop and `64–76px` on mobile.
- Heroes are tall and cinematic: usually `760–880px`, or close to one viewport height.
- Prefer strong two-column compositions for desktop, collapsing to one column below `850–950px`.
- Card grids use `16–18px` gaps. Interior card padding is generally `24–34px`.
- Align section headings and supporting links along the bottom edge on desktop.
- Leave meaningful negative space. A section should have one clear focal point.

## Core components

### Header

- Height: `82px`.
- Place over the hero using absolute positioning.
- Use a dark translucent fill, subtle blur, and a faint bottom border.
- Brand lockup sits left; compact uppercase navigation sits right.
- Navigation labels use `11px`, weight 800, and wide tracking.
- On small screens, replace navigation with a simple menu control and open a stacked near-black panel below the header.

### Hero

- Full-bleed action image under layered purple/black gradients.
- Content is anchored toward the lower-left, with generous breathing room above.
- Use one huge uppercase statement, a short supporting sentence, and no more than two calls to action.
- It is acceptable for the photograph to be more visible on the right while the left is protected by the gradient.

### Buttons

- Compact uppercase labels, `10–11px`, weight 900, letter spacing near `0.1em`.
- Primary: off-white fill, near-black text, one-pixel off-white border.
- Secondary: transparent/dark fill, off-white text, translucent or off-white border.
- Small radius (`3px`), not pill-shaped.
- Hover: lift by about `2px`; primary can invert to transparent, secondary can invert to off-white.
- Keep arrows when they communicate navigation: `View gallery →` or `YouTube ↗`.

### Section heading

Compose from a kicker, a large uppercase heading, optional one- or two-line description, and an optional small text link aligned opposite the title block.

### Image cards

- Use a strong image as the majority of the surface.
- Apply a bottom-up purple scrim so copy remains readable.
- Copy is anchored at the bottom with about `32px` padding.
- Border: translucent off-white; radius: `8px`; subtle deep shadow.
- Hover may shift content upward `4–6px` and slightly reduce the scrim. Do not zoom or animate aggressively.

### Information cards

- Use near-black or panel-purple surfaces with a subtle radial highlight in one corner.
- Preserve the same border, radius, and shadow family as image cards.
- Lead with metadata, then a bold title, restrained copy, and a clear action.
- Cards may alternate near-black and purple to create rhythm.

### Forms

- Use a desktop split: sticky explanatory column on the left, form on the right.
- Labels are compact, uppercase, bold, and widely tracked.
- Inputs use a dark translucent fill, off-white text, `8px` radius, and quiet borders.
- Focus changes the border to off-white. Do not rely on glow alone.
- Group related fields into two columns, collapsing to one on small screens.
- Keep error and success states explicit in both color and text.

### Footer

- Near-black background with a strong 3px off-white top rule.
- Brand and short tagline left, compact links right, legal text below.
- Stack naturally on mobile.

## Interaction and motion

Motion should feel mechanical and controlled.

- Use `200–350ms` transitions.
- Prefer small vertical shifts, border changes, opacity changes, and scrim adjustments.
- Keep hover movement within `2–6px`.
- Do not use bouncing, elastic easing, spinning, or constant ambient animation.
- Respect `prefers-reduced-motion` and remove nonessential transforms when enabled.
- Use smooth scrolling only when it does not interfere with user preferences.

## Responsive behavior

Primary breakpoints in the current system are approximately `950px`, `850px`, and `650px`.

- Below `950px`: four-column content becomes two columns; complex hero side content may disappear.
- Below `850px`: paired feature cards, sponsor cards, and split forms become one column.
- Below `650px`: navigation becomes a menu; all major grids become one column; actions stack; section headers stack.
- Mobile hero type should use `clamp(48px, 16vw, 72px)` and may wrap onto intentional lines.
- Reposition hero backgrounds to keep the vehicle or subject visible (the current racing hero uses roughly `62% center`).
- Never create horizontal scrolling to preserve an oversized headline. Wrap or scale it intentionally.
- Maintain at least 28px page gutters on compact layouts unless the component is deliberately edge-to-edge.

## Accessibility and quality bar

- Preserve strong contrast between text and dark/purple surfaces.
- Every interactive element needs a visible keyboard focus state.
- Touch targets should be at least 44 × 44px, even when the label is visually compact.
- Supply useful image alt text; use empty alt text for purely decorative images.
- Do not encode meaning through purple/off-white color alone.
- Use semantic headings in order and real buttons/links rather than clickable containers.
- Test at 320px, 390px, 768px, 1024px, and a wide desktop viewport.

## Content voice

The voice is concise, collective, and performance-oriented.

- Prefer: “Built on pace. Driven by teamwork.”
- Prefer: “One team. One target.”
- Prefer verbs such as build, race, improve, compete, prepare, and progress.
- Avoid exaggerated claims, gamer slang, long marketing paragraphs, or fake technical jargon.
- Use periods in display statements when extra confidence is useful.

## Do and do not

Do:

- Let one oversized statement dominate each major section.
- Use action photography as atmosphere as well as content.
- Alternate dark and purple environments.
- Repeat the same border, radius, type, and motion rules consistently.
- Adapt components to the new app’s content hierarchy.

Do not:

- Introduce unrelated accent colors without a semantic need.
- Round every component into soft pills.
- Fill empty space with icons or decorative lines.
- Use multiple competing gradients in non-photographic UI.
- Make every card and section look equally important.
- Copy Toga Racing names, logos, photographs, or wording into an unrelated product unless permission and relevance are explicit.

## AI implementation brief

Copy the following into a coding-agent prompt together with the new application’s requirements:

> Build this interface in the Toga Racing design language: a premium, cinematic, motorsport-inspired system using near-black (#100e0e), deep purple (#1a0b4c), panel purple (#26145c), off-white (#ededed), and muted lavender-gray (#c9c3da). Use Montserrat. Make display headings oversized, uppercase, tightly tracked, and heavy; keep body copy calm and readable. Alternate near-black and deep-purple full-width sections, constrain content to about 1380px, and use generous vertical spacing. Cards should have thin translucent off-white borders, 8px radii, restrained shadows, and subtle 2–6px hover movement. Buttons are compact, uppercase, nearly square, and invert between off-white and transparent states. Use real imagery under directional purple/black scrims where appropriate. Keep the composition focused and editorial—no neon gamer effects, fake carbon fibre, crowded dashboards, or decorative clutter. Collapse grids cleanly on tablet/mobile, preserve at least 28px gutters, give controls 44px touch targets, provide strong keyboard focus, and respect reduced-motion preferences. Adapt this language to the product’s actual information architecture rather than copying a Toga Racing page verbatim.

## Source references in this repository

The current implementation can be inspected in:

- `public/css/site.css` — base layout and component rules.
- `public/css/theme.css` — current purple, off-white, and near-black theme refinements.
- `resources/views/site.blade.php` — page composition and content hierarchy.
- `homepage-desktop.png` and `homepage-mobile-full.png` — visual reference captures.

When implementation and an older screenshot differ, treat `public/css/theme.css` as the source of truth for current tokens and component styling, while using the screenshots for overall mood and composition.
