# Himachal Safar — Luxury Brand & Identity System

*Private Himalayan Concierge · Curated journeys, private chauffeurs, handpicked stays.*

This system is built to read like a **premium Himalayan concierge** (Aman / Six Senses / Four Seasons),
not a tour operator. Restraint over decoration; one accent, one ink, generous space.

All concept files live in `assets/brand/`. Every mark is a hand-built vector on the
brand palette and works on light **and** dark surfaces (each carries a midnight disc or
gold-stroke geometry so it never disappears against a theme).

---

## 1 · The five concepts

| # | Name | File | One-line idea |
|---|------|------|----------------|
| 1 | **Alpine Seal** ★ | `concept-1-alpine-seal.svg` | Twin snow-peak + journey line inside a champagne seal with a north marker. |
| 2 | **Meridian Compass** | `concept-2-meridian-compass.svg` | A summit under a guiding star, framed by a compass ring with cardinal ticks. |
| 3 | **HS Monogram** | `concept-3-hs-monogram.svg` | Serif `HS` ligature crowned by a micro-ridge, in a hairline seal. |
| 4 | **Pine Arch** | `concept-4-pine-arch.svg` | A geometric Himalayan spruce within a sanctuary arch (the curated stay). |
| 5 | **Summit Diamond** | `concept-5-summit-diamond.svg` | A faceted gem crest holding a twin peak — exclusivity made literal. |

★ = **recommended primary mark.** See §8.

### Why each works

**1 · Alpine Seal — recommended.**
The circular seal is the oldest signal of authority and trust (passports, wax seals, member
clubs). Inside, a *minimal* twin peak — no clipart detail, just clean ridges with two ivory
snow crowns — sits above a single curved **journey line**, our "we take you there" promise.
The small diamond at true north reads as a compass cue without drawing a literal compass.
It is calm at 200px and still legible at 24px. This is the most flexible, most "concierge"
of the five.

**2 · Meridian Compass.**
For when navigation/expertise is the story (Spiti, off-route journeys). The peak is rendered
in Midnight Alpine with a champagne edge; a slim guiding star floats above it; faint cardinal
ticks on the ring whisper "compass" rather than shout it. More expedition-leaning than #1 —
excellent as a secondary "expeditions" sub-brand mark.

**3 · HS Monogram.**
The typographic anchor of the system. A Cormorant-style `H` (ivory) and `S` (gold) tucked
into a hairline ring, with a tiny ridge crowning the letters so the mountain idea survives
even in the pure-type mark. This is your stamp — embroidery, foil seal on welcome folders,
app icon, social avatar.

**4 · Pine Arch.**
The most "retreat / nature / stay" of the set. The arch evokes a lodge window or chapel —
sanctuary — and the geometric spruce keeps it from being literal foliage. Best where
*handpicked stays* and slow, restorative travel lead the message.

**5 · Summit Diamond.**
The most overtly luxurious — a gem/crest form that says exclusivity and membership. The
faceted twin peak and apex star give it a heraldic, Ritz-Carlton register. Strong for
premium packages, gift cards, and printed certificates; slightly more formal than #1.

---

## 2 · Icon construction ideas (geometry rules)

Keep every future mark on these rules so the family stays coherent:

- **Grid:** 120 × 120 viewBox, optical centre at (60, 60); keep artwork inside a 96px
  safe circle. Favicon redraws on a 64px grid (never just shrink the full mark).
- **Stroke weights:** primary ridge ≈ 3u, hairline rings ≈ 0.6–1.4u, snow crowns match the
  ridge weight. Two weights maximum per mark.
- **Peaks:** always a **twin peak** (one dominant, one supporting) — never a symmetrical
  three-bump "range." Apex angle 40–55°. Two snow crowns max, in ivory.
- **Enclosure:** circle (seal), rhombus (gem), or arch — pick one per mark, never combine.
- **The journey line:** a single shallow curve, one stroke, below the peaks. It is the
  recurring signature element across the system.
- **Corners & joins:** `stroke-linejoin="round"`, `stroke-linecap="round"`. No sharp
  miters — softness reads as hospitality.
- **Never:** gradients with more than 3 stops, drop shadows, more than one accent hue,
  realistic trees/snow, or any element that needs detail to be understood.

---

## 3 · Typography

| Role | Typeface | Usage |
|------|----------|-------|
| **Display / wordmark** | **Cormorant Garamond** (600) — fallback Playfair Display | "HIMACHAL SAFAR", section titles, the HS monogram. High-contrast serif = heritage + luxury. |
| **Sub-line / eyebrow** | **Inter** (600), tracked +4.5 | "PRIVATE HIMALAYAN CONCIERGE", labels, nav, captions. |
| **Long-form body** | **Inter** (400/500) | Paragraphs, itineraries. Optima/Canela are alternates if you license them. |

Rules:
- Wordmark is **always two-tone**: `HIMACHAL` in ink, `SAFAR` in champagne gold.
- Display serif: tracking **+1.5 to +2** in all-caps; never bold (600 max — heavy serifs
  look commercial).
- Eyebrows/labels: all-caps Inter, tracking **+4 to +5**, never larger than 12px.
- Pairing ratio: one serif headline to one tracked sans label — never two serifs together.
- The site currently loads Playfair + Inter; **add Cormorant Garamond** (Google Fonts) to
  match these masters exactly. Until then Playfair is the faithful fallback.

---

## 4 · Colour usage guidelines

| Token | Hex | Role |
|-------|-----|------|
| Midnight Alpine Blue | `#0D1B2A` | Primary ink, seal discs, dark "drama" bands, body text on light. |
| Deep Slate | `#1B263B` | Secondary surfaces, peak fills, gradient floors, hover states. |
| Champagne Gold | `#D4AF37` | **Accent only.** Hairlines, the wordmark's "SAFAR", icons, CTAs. |
| Champagne Light | `#F0DE9E` | Top stop of the gold gradient / highlights only. |
| Champagne Deep | `#A8842A` | Bottom stop of the gold gradient / pressed states. |
| Paper | `#F8F9FA` | Primary light background. |
| Ivory | `#F4EFE6` | Snow crowns, text/marks on dark bands. |

The **gold gradient** used in every mark:
`#F0DE9E → #D4AF37 → #A8842A` (top-left → bottom-right, ~45°).

The 60-30-10 discipline (this is what makes it read luxury, not loud):
- **60%** Paper or Midnight (the field).
- **30%** the opposite ink/neutral (text, secondary surfaces).
- **10%** Champagne Gold — *and never more.* Gold is a jewel, not a wall.

Do: gold for one hairline, one keyword, one button per view.
Don't: gold fills behind large areas, gold gradients on type at body size, two accents,
pure black (`#000`) or pure white (`#fff`) — use Midnight and Paper.

Contrast: Champagne on Midnight passes for large/graphic use; for **gold text smaller than
~18px on light**, switch to ink — gold-on-paper fails small-text contrast.

---

## 5 · Monogram variations

All derive from `concept-3` / `monogram-hs.svg`:

1. **Seal monogram** — `HS` in the hairline ring, midnight disc. Primary avatar / app icon.
2. **Rounded-tile monogram** — `monogram-hs.svg` (rounded square, for favicons & social).
3. **Open monogram** — letters only, no ring, gold `S` + ink `H`, for foil-stamping on
   paper where the material itself is the frame.
4. **Reversed** — ivory `HS` on a solid Midnight tile for dark stationery.
5. **Single-initial seal** — just `S` (Safar) centred in the ring for tiny touchpoints
   (luggage tags, wax seals, sign-off stamp).

Clear space around any monogram = the cap-height of the `H` on all sides.

---

## 6 · Favicon concepts

`favicon-new.svg` (64px grid, redrawn — not a shrink):
- Midnight rounded tile + simplified twin peak + north dot + journey line.
- At 16–32px the seal ring and snow detail vanish, so the favicon keeps **only** the peak
  silhouette, one snow notch, and the curve — the minimum recognizable signature.

Export set to generate:
- `favicon.svg` (ship the vector — modern browsers prefer it)
- `favicon-32.png`, `favicon-16.png`
- `apple-touch-icon.png` (180px — use the **rounded-tile monogram** here; the `HS`
  reads better than the peak at app-icon size)
- `maskable-512.png` for PWA (peak mark centred in the 80% safe zone)

Alternative favicon if you prefer the type route: the rounded-tile `HS` monogram —
strong brand recall, but the peak mark wins on instant "mountains" recognition.

---

## 7 · Luxury brand-system recommendations

**Logo hierarchy**
- Primary: **Alpine Seal** + horizontal wordmark (`lockup-horizontal.svg`).
- Stacked: `lockup-stacked.svg` for square/centred placements (footer, loaders, print).
- Icon-only: the seal alone (nav, favicon, watermark).
- Monogram: avatars, foil, embroidery, sign-offs.

**Clear space & minimum size**
- Clear space = the height of the seal's north marker on every side.
- Minimum sizes: full lockup ≥ 150px wide; seal alone ≥ 24px; favicon mark ≥ 16px.

**Backgrounds**
- Approved: Paper, Midnight, a dark photograph behind a Midnight scrim.
- The wordmark master is for light backgrounds; on dark, flip `HIMACHAL` to ivory
  (`#F4EFE6`) and keep `SAFAR` gold.

**Photography**
- Wide, quiet, low-saturation Himalayan landscapes; mist, cedar, snow, single subjects.
- Always lay a Midnight gradient scrim (≈55–80% bottom) under any text — matches the
  site's `.lux-hero` / `.lux-banner` treatment.
- Avoid: HDR, crowds, neon sunsets, stock "happy tourist" framing.

**Tone & voice**
- Understated, first-person-plural, confident. "We read the weather, the roads and the
  season." Never exclamation marks, never "cheap/best price/deal."

**Motion**
- Slow, eased (cubic-bezier .2,.8,.2,1), 400–800ms. Things *settle*, they don't bounce.

**Applications**
- Stationery, welcome folder with foil `HS` seal, leather luggage tags, in-car welcome
  card, digital itinerary PDF, WhatsApp profile (rounded-tile monogram), email signature
  (horizontal lockup at 150px).

---

## 8 · Recommendation & how to deploy

**Primary: Concept 1 — Alpine Seal.** It carries the most meaning (mountain + journey +
compass + seal of trust) with the least ink, scales from favicon to billboard, and reads
unmistakably as a *concierge* rather than a tour desk. Pair it with the **HS Monogram**
(Concept 3) for small/avatar contexts and reserve the **Summit Diamond** (Concept 5) for
premium print (certificates, gift cards).

To make it live, point the nav and head at the new files:

```php
// includes/nav.php  → replace logo-icon.svg
<img src="assets/brand/concept-1-alpine-seal.svg" alt="" width="34" height="34" ...>
```
```html
<!-- favicon (head) -->
<link rel="icon" type="image/svg+xml" href="assets/brand/favicon-new.svg">
```

Or, simplest: overwrite `assets/logo-icon.svg`, `assets/logo.svg` and `assets/favicon.svg`
with the chosen files and nothing else changes. Tell me which concept you want as the
master and I'll wire it into the live site (and build the wordmark inverse + PNG/ICO export
set) for you.
